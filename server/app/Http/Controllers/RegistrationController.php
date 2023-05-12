<?php

namespace App\Http\Controllers;

use App\Mail\Confirmation;
use App\Models\SignupMailConfirmation;
use App\Models\UserData;
use App\Models\Users;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class RegistrationController extends Controller
{
    use MakesHttpRequests;

    public function registration(): \Illuminate\Http\JsonResponse
    {

        $validator = Validator::make(
            request()->all(), [
            'name' => 'required|string',
            'lastname' => 'required|string',
            'password' => 'required|string',
            'email' => 'required|string|unique:usr,usr_email',
            'phone' => 'required|string'],
            $messages = ['unique' => 'REPEATED']
        );

        try {
            $inputData = $validator->validated();

            $usersTable = new Users;
            $usersTable->id_usr = hash('sha256', $inputData['email']);
            $usersTable->usr_email = $inputData['email'];
            $usersTable->usr_passw = $inputData['password'];
            $usersTable->usr_FechaAlta = DB::raw('NOW()');
            $usersTable->save();

            $userDataTable = new UserData;
            $userDataTable->id_usr2 = hash('sha256', $usersTable->id_usr);
            $userDataTable->id_usr = $usersTable->id_usr;
            $userDataTable->usr2_nombre = $inputData['name'];
            $userDataTable->usr2_apellido = $inputData['lastname'];
            $userDataTable->usr2_cel1 = $inputData['phone'];
            $userDataTable->save();


        } catch (ValidationException $e) {
            if (in_array('REPEATED', $validator->errors()->get('email'), true)) {
                return response()->json(['status' => '3001', 'message' => 'email already registered'], 422);
            }

            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }


        // Create the confirmation entries
        $signupMail = new SignupMailConfirmation;
        $signupMail->url = hash('sha256',
            $inputData['email'] .
            Str::random(128) .
            $inputData['name'] .
            $inputData['lastname']);

        $signupMail->id_usr = $userDataTable->id_usr;
        $signupMail->timestamp = DB::raw('NOW()');

        $signupMail->save();

        try {
            Mail::to($inputData['email'])->send(new Confirmation([
                'name' => $inputData['name'],
                'lastname' => $inputData['lastname'],
                'link' => env('APP_URL', 'localhost') . ':' . env('APP_PORT', '4000') .
                    '/api/session/confirmation/' . $signupMail->url
            ]));
        } catch (Throwable $e) {
            return response()->json(['status' => 3002, 'message' => 'Mail could not be sent'], 503);
        }

        return response()->json(['status' => 0, 'message' => 'success']);
    }

    public function confirmation(string $url): string
    {
        $signupMail = SignupMailConfirmation::query()
            ->selectRaw('signup_mail.id_usr, TIMESTAMPDIFF(MINUTE, signup_mail.timestamp, NOW()) AS diff, usr_det.usr2_nombre AS fn, usr_det.usr2_apellido AS ln')
            ->join('usr_det', 'signup_mail.id_usr', '=', 'usr_det.id_usr')
            ->where('signup_mail.url', '=', $url)
            ->get();


        if ($signupMail->count() != 1) {
            return view('registration.missing',
                ['url' => env('APP_FRONTEND_URL', '')]);
        }

        $maxTime = (int)env('REGISTRATION_MAX_TIME', 30);
        $queryData = $signupMail->first();

        if ($maxTime < $signupMail->first()['diff']) {
            // Generate a new URL for the confirmation
            $resendSignupMail = new SignupMailConfirmation;
            $resendSignupMail->url = hash('sha256',
                Str::random(128) .
                $queryData['fn'] .
                $queryData['ln']);
            $resendSignupMail->id_usr = $queryData['id_usr'];
            $resendSignupMail->timestamp = DB::raw('NOW()');
            $resendSignupMail->save();

            return view('registration.expired',
                [
                    'fn' => $queryData['fn'],
                    'ln' => $queryData['ln'],
                    'resendUrl' => env('APP_URL', 'localhost') . ':' . env('APP_PORT', '4000') .
                        '/api/session/resend/' . $resendSignupMail->url
                ]);
        }

        // Set the verified field to 1
        $usr = Users::query()->find($queryData['id_usr']);
        if ($usr !== null) {
            $usr->usr_verificado = 1;
            $usr->save();

            // Remove all urls where for the current user
            SignupMailConfirmation::query()
                ->where('id_usr', $queryData['id_usr'])
                ->delete();

            // At this point we will create the user image directory
            Storage::createDirectory('images/users/' . $queryData['id_usr']);

            return view('registration.confirm',
                ['name' => $queryData['fn'],
                    'lastName' => $queryData['ln'],
                    'url' => env('APP_FRONTEND_URL', '')]);
        }


        return view('operation_error', ['url' => env('APP_FRONTEND_URL', '')]);
    }

    public function resend($url): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        $userInformation = SignupMailConfirmation::query()
            ->selectRaw('url, usr_email AS em, usr2_nombre AS fn, usr2_apellido AS ln')
            ->join('usr', 'signup_mail.id_usr', '=', 'usr.id_usr')
            ->join('usr_det', 'signup_mail.id_usr', '=', 'usr_det.id_usr')
            ->where('signup_mail.url', '=', $url)
            ->get();

        if ($userInformation->count() != 1) {
            return view('registration.missing',
                ['url' => env('APP_FRONTEND_URL', '')]);
        }

        $userInformationData = $userInformation->first();

        try {
            Mail::to($userInformationData['em'])->send(new Confirmation([
                'name' => $userInformationData['fn'],
                'lastname' => $userInformationData['ln'],
                'link' => env('APP_URL', 'localhost') . ':' . env('APP_PORT', '4000') .
                    '/api/session/confirmation/' . $userInformationData['url']
            ]));
        } catch (Throwable $e) {
            return response()->json(['status' => 3002, 'message' => 'Mail could not be sent'], 503);
        }

        return view('registration.resend', [
            'fn' => $userInformationData['fn'],
            'ln' => $userInformationData['ln'],
            'url' => env('APP_FRONTEND_URL', '')
        ]);
    }
}

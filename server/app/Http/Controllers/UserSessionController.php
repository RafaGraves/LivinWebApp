<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\UserData;
use App\Models\UserLog;
use App\Models\Users;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserSessionController extends Controller
{
    protected string $nullUser = '000000000000000000000000000000000000000000000000000000000000000';

    public function signin()
    {
        $validator = Validator::make(
            request()->all(), [
                'email' => 'required|string',
                'password' => 'required|string']
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                $userInfo = Users::query()->findOrFail(hash('sha256', $inputData['email']));

                if ($userInfo['usr_passw'] !== $inputData['password']) {
                    return response()->json(['status' => 2001, 'message' => 'Wrong user data'], 422);
                }

                // Get the authorization token
                $authTokenBearer = request()->header('Authorization');
                $authToken = substr($authTokenBearer, 7, strlen($authTokenBearer));

                // Check for current open sessions
                $currentSession = Session::query()->find($authToken);
                if ($currentSession !== null && $currentSession['usr_id'] !== $this->nullUser) {                // Check if the user has the same UserAgent and IP
                    if (request()->ip() === $currentSession['ip'] &&
                        request()->userAgent() === $currentSession['userAgent']) {
                        // Just return success if the data is the same for everyone
                        return response()->json(['status' => 0, 'message' => 'signed in']);
                    }
                }

                // Remove the Current Local Session
                Session::query()
                    ->where('token', $authToken)
                    ->delete();

                // Generate a new CSRF - token
                $csrfToken = Str::random(64);
                // Generate a new authorization Token
                $newAuthToken = Str::random(64);

                // Create the Database Session
                $session = new Session;
                $session->token = $newAuthToken;
                $session->usr_id = $userInfo['id_usr'];
                $session->ip = request()->ip();
                $session->userAgent = request()->userAgent();
                $session->csrf = $csrfToken;
                $session->expire = DB::raw('DATE_ADD(now(),interval 2 hour)');
                $session->save();

                // Update the log
                $userLog = new UserLog;
                $userLog->id_bitacora = $newAuthToken;
                $userLog->id_usr = $userInfo['id_usr'];
                $userLog->bit_entrada = DB::raw('NOW()');
                $userLog->bit_salida = DB::raw('NULL');
                $userLog->save();

                // Update the user entries
                Users::query()
                    ->where('id_usr', $userInfo['id_usr'])
                    ->increment('usr_entradas');

                return response()->json(
                    [
                        'status' => 0,
                        'message' => 'success',
                        'token' => $newAuthToken
                    ])->header('X-CSRF-Token', $csrfToken);

            } catch (ModelNotFoundException $notFoundException) {
                return response()->json(['status' => 2000, 'message' => 'User not found'], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }


    }

    public function signout()
    {
        // Body of the request is not needed, however, the LivinAuth Middleware
        // Will require that the body must be set to {}

        // Get the authorization token
        $authTokenBearer = request()->header('Authorization');
        $authToken = substr($authTokenBearer, 7, strlen($authTokenBearer));

        // Check for current open sessions
        $currentSession = Session::query()->find($authToken);
        if ($currentSession === null) {
            return response()->json(['status' => 3003, 'message' => 'Can´t close an non-existing session']);
        }

        // Update the user log
        UserLog::query()->where(
            'id_bitacora', $authToken)
            ->update(['bit_salida' => DB::raw('NOW()')]);

        // Remove the connection
        Session::query()
            ->where('token', $authToken)
            ->delete();

        // Now if we leave now, the problem will raise because
        // The page will no longer have an 'local authorization'
        // So we need to generate a new one
        $newAuthToken = Str::random(64);
        Session::query()->create(
            [
                'token' => $newAuthToken,
                'usr_id' => $this->nullUser,
                'ip' => request()->ip(),
                'userAgent' => request()->userAgent(),
                'csrf' => '', // Locals don't need the CSRF
                'expire' => DB::raw('DATE_ADD(now(),interval 2 hour)')
            ]
        );

        return response()->json(
            [
                'status' => 0,
                'message' => 'success',
                'token' => $newAuthToken
            ]);
    }

    public function update()
    {
        $validator = Validator::make(
            request()->all(),
            [
                'cel1' => 'string',
                'cel2' => 'string',
                'name' => 'string',
                'lastname' => 'string',
                'mail2' => 'string'
                // Users pictures are set in another endpoint
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            // Get the authorization token
            $authTokenBearer = request()->header('Authorization');
            $authToken = substr($authTokenBearer, 7, strlen($authTokenBearer));

            $currentSession = Session::query()->find($authToken);
            if ($currentSession === null) {
                return response()->json(['status' => 3004, 'message' => 'Can´t update an non-existing session']);
            }

            if (array_key_exists('cel1', $inputData)) {
                UserData::query()
                    ->where('id_usr', $currentSession['usr_id'])
                    ->update(['usr2_cel1' => $inputData['cel1']]);
            }

            if (array_key_exists('cel2', $inputData)) {
                UserData::query()
                    ->where('id_usr', $currentSession['usr_id'])
                    ->update(['usr2_cel2' => $inputData['cel2']]);
            }

            if (array_key_exists('name', $inputData)) {
                UserData::query()
                    ->where('id_usr', $currentSession['usr_id'])
                    ->update(['usr2_nombre' => $inputData['name']]);
            }

            if (array_key_exists('lastname', $inputData)) {
                UserData::query()
                    ->where('id_usr', $currentSession['usr_id'])
                    ->update(['usr2_apellido' => $inputData['lastname']]);
            }

            if (array_key_exists('mail2', $inputData)) {
                UserData::query()
                    ->where('id_usr', $currentSession['usr_id'])
                    ->update(['usr2_mail2' => $inputData['mail2']]);
            }

            return response()->json(
                [
                    'status' => 0,
                    'message' => 'success'
                ]);

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }

    }


}

<?php

namespace App\Http\Controllers;

use App\Models\UserData;
use App\Models\Users;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LocalImagesController extends Controller
{
    protected string $emptyUserPhoto = 'images/users/000000000000000000000000000000000000000000000000000000000000000/Gl2jzTB99SNCdiMFuj5qQ37HAOfDm1iq.png';

    public function userImage()
    {
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            $queryData = UserData::query()
                ->select('usr2_foto')
                ->where('id_usr', $sessionUsrId)->get();

            $userDataID = $queryData->first()['usr2_foto'];

            $filePath = 'images/users/' . $sessionUsrId . '/' . $userDataID . '.png';
            if ($userDataID === '' || !Storage::exists($filePath)) {
                $filePath = $this->emptyUserPhoto;
            }

            return response(Storage::get($filePath), 200)
                ->header('Content-type', Storage::mimeType($filePath));


        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }
    }

    public function updateUserImage()
    {
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            $validator = Validator::make(
                request()->all(),
                [
                    'data' => 'required|string'
                ]
            );

            try {
                $validator->validate();
                $inputData = $validator->validated();

                $queryData = UserData::query()
                    ->select('usr2_foto')
                    ->where('id_usr', $sessionUsrId)->get();

                $userDataID = $queryData->first()['usr2_foto'];

                $imageId = '';
                if ($userDataID === '') {
                    // Generate a new
                    $imageId = Str::random(32);

                    UserData::query()
                        ->where('id_usr', $sessionUsrId)
                        ->update(['usr2_foto' => $imageId]);
                } else {
                    $imageId = $userDataID;
                }

                $filePath = 'images/users/' . $sessionUsrId . '/' . $imageId . '.png';
                Storage::put($filePath, base64_decode($inputData['data']));

                return response()->json(['status' => 0, 'message' => 'success']);

            } catch (ValidationException $e) {
                return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
            }

        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }
    }
}

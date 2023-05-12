<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @throws Exception
     */
    public function accessUserIdFromAuthToken(): string
    {
        $authTokenBearer = request()->header('Authorization');
        $authToken = substr($authTokenBearer, 7, strlen($authTokenBearer));
        $currentSession = Session::query()->find($authToken);


        if ($currentSession === null) {
            throw new Exception();
        }
        return $currentSession['usr_id'];
    }
}

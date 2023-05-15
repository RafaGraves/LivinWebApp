<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Cassandra\Exception\InvalidQueryException;
use Exception;
use http\Message;
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

        try {
            $currentSession = Session::query()
                ->select(['usr_id'])
                ->where('token', $authToken)->get();
        } catch (Exception $e) {
            dd($e);
        }

        if ($currentSession === null) {
            throw new Exception();
        }
        return $currentSession->first()['usr_id'];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\UserLog;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Sessions:
 */

class LocalSessionController extends Controller
{
    use MakesHttpRequests;

    protected string $nullUser = '000000000000000000000000000000000000000000000000000000000000000';

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $token = Str::random(64);

        $usrLog = new UserLog();
        $usrLog->id_bitacora = $token;
        $usrLog->id_usr = $this->nullUser;
        $usrLog->bit_entrada = DB::raw('NOW()');
        $usrLog->bit_salida = DB::raw('NULL');

        if ($usrLog->save()) {

            $sessionDB = new Session();

            $sessionDB->token = $token;
            $sessionDB->usr_id = $this->nullUser;
            $sessionDB->ip = $request->ip();
            $sessionDB->userAgent = $request->header('User-Agent');
            $sessionDB->csrf = ''; // Local session will not have csrf
            $sessionDB->expire = DB::raw('DATE_ADD(now(),interval 1 hour)');

            $success = $sessionDB->save();

            return response()->json([
                'success' => (int)$success
            ])->header('Authorization', 'Bearer ' . $token, 180);
        } else return response()->json([
            'success' => 0
        ]);
    }
}

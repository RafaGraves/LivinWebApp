<?php

namespace App\Http\Middleware;

use App\Models\Session;
use Closure;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LivinAuth
{
    protected string $nullUser = '000000000000000000000000000000000000000000000000000000000000000';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('Content-Type') != 'application/json') {
            return response()->json(
                [
                    'status' => -1001,
                    'message' => 'Request is not a JSON object'
                ], 401);
        }

        $authHeader = $request->header('Authorization');

        if ($authHeader !== null && substr($authHeader, 0, '6') === 'Bearer') {
            $authToken = substr($authHeader, 7, strlen($authHeader));


            try {

                $sessionData = Session::query()->findOrFail($authToken);

                // Check the expiration
                $expiration = $sessionData['expire'];
                $currentDate = new DateTime('now', new DateTimeZone('America/Mexico_City'));


                // Check the Authorization Token Expiration
                if ($currentDate > new DateTime($expiration, new DateTimeZone('America/Mexico_City'))) {
                    // Expiration date has passed

                    // Check if the user is the nullUser
                    if ($sessionData['usr_id'] === $this->nullUser) {
                        // If it is just renovate a new
                        $updateSession = Session::query()->find($authToken);
                        if ($updateSession !== null) {
                            $updateSession->expire = DB::raw('DATE_ADD(now(),interval 1 hour)');
                            $updateSession->save();
                        }
                    } else {

                        // Must remove the session
                        Session::query()
                            ->where('token', $authToken)
                            ->delete();
                        // The user is not null and the token has expired
                        return response()->json(
                            [
                                'status' => 4003,
                                'message' => 'Session expired'
                            ], 401);
                    }
                }

                // Check the IP
                if ($request->ip() !== $sessionData['ip']) {
                    return response()->json(
                        [
                            'status' => 4005,
                            'message' => 'Session expired'
                        ], 401);
                }

                if ($request->userAgent() !== $sessionData['userAgent']) {
                    return response()->json(
                        [
                            'status' => 4006,
                            'message' => 'Session expired'
                        ], 401);
                }

                // Check the CSRF Token
                if ($sessionData['csrf'] !== '') {
                    $csrfToken = $request->header('X-CSRF-Token');
                    if ($csrfToken !== $sessionData['csrf']) {
                        return response()->json(
                            [
                                'status' => 4001,
                                'message' => 'CSRF Failed to validate the request'
                            ], 401);
                    } else {
                        // Renew the token and store it in the DB
                        $sessionData['csrf'] = Str::random(64);
                        $sessionData->save();

                        // Reset the Header
                        $response = $next($request);
                        $response->header('X-CSRF-Token', $sessionData['csrf']);
                        return $response;
                    }
                }

            } catch (Exception $e) {
                // Not authorize
                return response()->json(
                    [
                        'status' => 4002,
                        'message' => 'Failed to validate the request'
                    ], 401);
            }
        } else {
            return response()->json(
                [
                    'status' => 4004,
                    'message' => 'Forbidden access'
                ], 401);
        }


        return $next($request);
    }
}

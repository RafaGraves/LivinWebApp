<?php

namespace App\Http\Controllers;

use App\Models\UserData;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LocationController extends Controller
{
    public function zipCode()
    {
        // The token/CSRF validation we'll be made by the middleware
        $validator = Validator::make(
            request()->all(),
            [
                'cp' => 'required|string'
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            $result = DB::table('cod_postal')
                ->join('muni', 'cp_municipio', '=', 'muni_clave')
                ->join('entidad', 'cod_postal.id_entidad', '=', 'entidad.id_entidad')
                ->select('cod_post_cp', 'cp_municipio', 'cp_asentamiento', 'muni_nombre', 'entidad.ent_nombre', 'ent_sigla')
                ->where('cod_postal.cod_post_cp', '=', $inputData['cp'])->get();

            if ($result->count() === 0) {
                return response()->json(['status' => 0, 'count' => 0, 'results' => []]);
            }


            $innerData = [];
            foreach ($result as $item) {
                $innerData[] = [
                    'settlement' => $item->cp_asentamiento,
                    'municipality' => $item->muni_nombre,
                    'state' => $item->ent_nombre
                ];

            }

            return response()->json(['status' => 0, 'count' => $result->count(), 'results' => $innerData]);


        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }


    }
}

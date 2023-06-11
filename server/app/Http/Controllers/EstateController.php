<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyPhysAmenity;
use App\Models\PropPhoto;
use App\Models\PropsAmenities;
use App\Models\PropsStatus;
use App\Models\PropsTypes;
use App\Models\PropsUses;
use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Psy\Util\Json;

class EstateController extends Controller
{
    protected string $nullUser = '000000000000000000000000000000000000000000000000000000000000000';

    function amenities(): JsonResponse
    {
        // Get all amenities
        $result = PropsAmenities::query()->select()->get();

        $innerData = [];
        foreach ($result as $item) {
            $innerData[] = [
                'id' => $item->id_amenidad,
                'name' => $item->ameni_nombre,
                'notes' => $item->ameni_nota
            ];

        }

        return response()->json(['status' => 0, 'count' => $result->count(), 'results' => $innerData]);

    }

    function createAmenity(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'name' => 'string',
                'notes' => 'string',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                $newId = PropsAmenities::query()->insertGetId(
                    [
                        'ameni_nombre' => $inputData['name'],
                        'ameni_nota' => $inputData['notes']
                    ]);
                return response()->json(
                    [
                        'status' => 0,
                        'message' => '',
                        'id' => $newId
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8000,
                        'message' => 'Could not insert the amenity'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function updateAmenity(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'name' => 'required|string',
                'notes' => 'required|string',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                PropsAmenities::query()
                    ->where('id_amenidad', $inputData['id'])
                    ->update(
                        [
                            'ameni_nombre' => $inputData['name'],
                            'ameni_nota' => $inputData['notes']
                        ]);
                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8001,
                        'message' => 'Could not update the amenity'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function removeAmenity(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                PropsAmenities::query()
                    ->where('id_amenidad', $inputData['id'])
                    ->delete();

                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8002,
                        'message' => 'Could not delete the amenity'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function status(): JsonResponse
    {
        // Get all status
        $result = PropsStatus::query()->select()->get();

        $innerData = [];
        foreach ($result as $item) {
            $innerData[] =
                [
                    'id' => $item->id_status,
                    'name' => $item->status_nombre,
                    'notes' => $item->status_nota
                ];
        }

        return response()->json(['status' => 0, 'count' => $result->count(), 'results' => $innerData]);
    }

    function createStatus(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'name' => 'string',
                'notes' => 'string',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                $newId = PropsStatus::query()->insertGetId(
                    [
                        'status_nombre' => $inputData['name'],
                        'status_nota' => $inputData['notes']
                    ]);
                return response()->json(
                    [
                        'status' => 0,
                        'message' => '',
                        'id' => $newId
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8003,
                        'message' => 'Could not insert the status'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function updateStatus(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'name' => 'required|string',
                'notes' => 'required|string',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                PropsStatus::query()
                    ->where('id_status', $inputData['id'])
                    ->update(
                        [
                            'status_nombre' => $inputData['name'],
                            'status_nota' => $inputData['notes']
                        ]);
                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8004,
                        'message' => 'Could not update the status'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function removeStatus(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                PropsStatus::query()
                    ->where('id_status', $inputData['id'])
                    ->delete();

                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8005,
                        'message' => 'Could not delete the status'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function types(): JsonResponse
    {
        // Get all types
        $result = PropsTypes::query()->select()->get();

        $innerData = [];
        foreach ($result as $item) {
            $innerData[] = [
                'id' => $item->id_tipo_propiedad,
                'name' => $item->tipo_nombre,
                'notes' => $item->tipo_nota
            ];

        }

        return response()->json(['status' => 0, 'count' => $result->count(), 'results' => $innerData]);
    }

    function createType(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'name' => 'string',
                'notes' => 'string',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                $newId = PropsTypes::query()->insertGetId(
                    [
                        'tipo_nombre' => $inputData['name'],
                        'tipo_nota' => $inputData['notes']
                    ]);
                return response()->json(
                    [
                        'status' => 0,
                        'message' => '',
                        'id' => $newId
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8006,
                        'message' => 'Could not insert the property type'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function updateType(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'name' => 'required|string',
                'notes' => 'required|string',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                PropsTypes::query()
                    ->where('id_tipo_propiedad', $inputData['id'])
                    ->update(
                        [
                            'tipo_nombre' => $inputData['name'],
                            'tipo_nota' => $inputData['notes']
                        ]);
                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8007,
                        'message' => 'Could not update the property type'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function removeType(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                PropsTypes::query()
                    ->where('id_tipo_propiedad', $inputData['id'])
                    ->delete();

                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8008,
                        'message' => 'Could not delete the property type'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    // Uses
    function uses(): JsonResponse
    {
        // Get all uses
        $result = PropsUses::query()->select()->get();

        $innerData = [];
        foreach ($result as $item) {
            $innerData[] = [
                'id' => $item->id_uso,
                'name' => $item->uso_nombre,
                'notes' => $item->uso_nota
            ];

        }

        return response()->json(['status' => 0, 'count' => $result->count(), 'results' => $innerData]);
    }

    function createUse(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'name' => 'string',
                'notes' => 'string',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                $newId = PropsUses::query()->insertGetId(
                    [
                        'uso_nombre' => $inputData['name'],
                        'uso_nota' => $inputData['notes']
                    ]);
                return response()->json(
                    [
                        'status' => 0,
                        'message' => '',
                        'id' => $newId
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8009,
                        'message' => 'Could not insert the property use'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function updateUse(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'name' => 'required|string',
                'notes' => 'required|string',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                PropsUses::query()
                    ->where('id_uso', $inputData['id'])
                    ->update(
                        [
                            'uso_nombre' => $inputData['name'],
                            'uso_nota' => $inputData['notes']
                        ]);
                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8010,
                        'message' => 'Could not update the property use'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function removeUse(): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
            ]
        );

        try {
            $validator->validate();
            $inputData = $validator->validated();

            try {
                PropsUses::query()
                    ->where('id_uso', $inputData['id'])
                    ->delete();

                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8011,
                        'message' => 'Could not delete the property use'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function newProperty(): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }

        try {

            $validator = Validator::make(
                request()->all(),
                [
                    'notes' => 'required|string',
                    'use' => 'required|integer',
                    'type' => 'required|integer',
                    'price' => 'required|string',
                    'coin' => 'required|integer'
                ]
            );

            $validator->validate();
            $inputData = $validator->validated();

            $newPropertyID = Str::random(64);

            try {

                Property::query()
                    ->create(
                        [
                            'id_propiedad' => $newPropertyID,
                            'id_usr' => $sessionUsrId,
                            'prop_nota' => $inputData['notes'],
                            'id_uso' => $inputData['use'],
                            'id_tipo_propiedad' => $inputData['type'],
                            'prop_precio' => $inputData['price'],
                            'id_moneda' => $inputData['coin']
                        ]);

                Storage::createDirectory('images/properties/' . $newPropertyID);

                return response()->json(
                    [
                        'status' => 0,
                        'message' => '',
                        'id' => $newPropertyID
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8012,
                        'message' => 'Could not insert the property use'
                    ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }


    function updateProperty($urlId): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();

        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }


        try {
            $validator = Validator::make(
                request()->all(),
                [
                    'notes' => 'string',
                    "status" => 'integer',
                    'use' => 'integer',
                    'type' => 'integer',
                    'code' => 'string',
                    'auth' => 'integer',
                    "price" => 'string',
                    "coin" => 'integer'
                ]
            );

            $validator->validate();
            $inputData = $validator->validated();

            $newPropertyID = Str::random(64);

            try {

                if (key_exists('notes', $inputData)) {
                    Property::query()
                        ->where('id_propiedad', $urlId)
                        ->update(['prop_nota' => $inputData['notes']]);
                }

                if (key_exists('status', $inputData)) {
                    Property::query()
                        ->where('id_propiedad', $urlId)
                        ->update(['id_status' => $inputData['status']]);
                }

                if (key_exists('use', $inputData)) {
                    Property::query()
                        ->where('id_propiedad', $urlId)
                        ->update(['id_uso' => $inputData['use']]);
                }

                if (key_exists('type', $inputData)) {
                    Property::query()
                        ->where('id_propiedad', $urlId)
                        ->update(['id_tipo_propiedad' => $inputData['type']]);
                }

                if (key_exists('code', $inputData)) {
                    Property::query()
                        ->where('id_propiedad', $urlId)
                        ->update(['prop_codigo' => $inputData['code']]);
                }

                if (key_exists('auth', $inputData)) {
                    Property::query()
                        ->where('id_propiedad', $urlId)
                        ->update(['prop_autoriza' => $inputData['auth']]);
                }

                if (key_exists('price', $inputData)) {
                    Property::query()
                        ->where('id_propiedad', $urlId)
                        ->update(['prop_precio' => $inputData['price']]);
                }

                if (key_exists('coin', $inputData)) {
                    Property::query()
                        ->where('id_propiedad', $urlId)
                        ->update(['id_moneda' => $inputData['coin']]);
                }

                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {
                return response()->json(
                    [
                        'status' => 8013,
                        'message' => 'Could not update the property'
                    ], 422);
            }
        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }

    }

    function removeProperty($urlId): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();

            try {

                PropertyPhysAmenity::query()
                    ->where('id_propiedad', $urlId)
                    ->delete();

                Property::query()
                    ->where('id_propiedad', $urlId)
                    ->delete();

                return response()->json(
                    [
                        'status' => 0,
                        'message' => ''
                    ]);
            } catch (QueryException $e) {

                return response()->json(
                    [
                        'status' => 8014,
                        'message' => 'Could not delete the property'
                    ], 422);
            }


        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }
    }

    function property($urlId): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }

        try {
            $prop = Property::query()
                ->where('id_propiedad', $urlId)
                ->select(
                    [
                        'prop_nota',
                        'id_status',
                        'id_uso',
                        'id_tipo_propiedad',
                        'prop_codigo',
                        'prop_autoriza',
                        'prop_precio',
                        'id_moneda'
                    ])->get()->first();

            return response()->json(
                [
                    'status' => 0,
                    'message' => '',
                    'data' =>
                        [
                            'notes' => $prop['prop_nota'],
                            'status' => $prop['id_status'],
                            'use' => $prop['id_uso'],
                            'type' => $prop['id_tipo_propiedad'],
                            'code' => $prop['prop_codigo'],
                            'auth' => $prop['prop_autoriza'],
                            'coin' => $prop['id_moneda'],
                            'price' => $prop['prop_precio'],
                        ]
                ]);
        } catch (QueryException $e) {

            return response()->json(
                [
                    'status' => 8015,
                    'message' => 'Could not retrieve the property info'
                ], 422);
        }

    }

    function allProperties(): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }

        try {
            $result = Property::query()
                ->where('id_usr', $sessionUsrId)
                ->select(
                    [
                        'id_propiedad'
                    ])->get();

            $innerData = [];
            foreach ($result as $item) {
                $innerData[] =
                    [
                        'id' => $item->id_propiedad,
                    ];
            }

            return response()->json(['status' => 0, 'count' => $result->count(), 'results' => $innerData]);
        } catch (QueryException $e) {

            return response()->json(
                [
                    'status' => 8016,
                    'message' => 'Could not retrieve all the property info'
                ], 422);
        }
    }


    function newPropertyAmenity($urlId): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();

        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }

        try {
            $validator = Validator::make(
                request()->all(),
                [
                    'id' => 'required|integer'
                ]
            );

            $validator->validate();
            $inputData = $validator->validated();

            $newId = PropertyPhysAmenity::query()
                ->insertGetId(
                    [
                        'id_propiedad' => $urlId,
                        'id_amenidad' => $inputData['id']
                    ]
                );

            return response()->json(
                [
                    'status' => 0,
                    'message' => '',
                    'id' => $newId
                ]
            );

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function updatePropertyAmenity($urlId, $amenId): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }

        try {
            $validator = Validator::make(
                request()->all(),
                [
                    'id' => 'required|integer'
                ]
            );

            $validator->validate();
            $inputData = $validator->validated();

            PropertyPhysAmenity::query()
                ->where('id_descripcion', $amenId)
                ->where('id_propiedad', $urlId)
                ->update(
                    [
                        'id_amenidad' => $inputData['id']
                    ]
                );

            return response()->json(
                [
                    'status' => 0,
                    'message' => ''
                ]
            );

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function removePropertyAmenity($urlId, $amenId): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }

        PropertyPhysAmenity::query()
            ->where('id_propiedad', $urlId)
            ->where('id_descripcion', $amenId)
            ->delete();

        return response()->json(
            [
                'status' => 0,
                'message' => '',
            ]
        );
    }

    function removeAllPropertyAmenities($urlId): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }

        PropertyPhysAmenity::query()
            ->where('id_propiedad', $urlId)
            ->delete();

        return response()->json(
            [
                'status' => 0,
                'message' => '',
            ]
        );
    }

    function getPropertyAmenities($urlId): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }

        $result = PropertyPhysAmenity::query()
            ->where('id_propiedad', $urlId)
            ->select(
                [
                    'id_descripcion',
                    'id_amenidad'
                ])->get();

        $innerData = [];
        foreach ($result as $item) {
            $innerData[] =
                [
                    'id' => $item->id_descripcion,
                    'amenity' => $item->id_amenidad,
                ];
        }

        return response()->json(['status' => 0, 'count' => $result->count(), 'results' => $innerData]);
    }

    function addPropertyPictures($urlId): JsonResponse
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }


        try {
            $validator = Validator::make(
                request()->all(),
                [
                    'cat' => 'required|integer',
                    'data' => 'required|string',
                    'notes' => 'required|string'
                ]
            );

            $validator->validate();
            $inputData = $validator->validated();

            $photoId = Str::random(64);
            PropPhoto::query()
                ->create(
                    [
                        'id_foto' => $photoId,
                        'id_propiedad' => $urlId,
                        'id_categoria' => $inputData['cat'],
                        'foto_nota' => $inputData['notes']
                    ]
                );

            $binaryImage = base64_decode($inputData['data']);

            $filePath = 'images/properties/' . $urlId . '/' . $photoId . '.jpg';

            Storage::put($filePath, base64_decode($inputData['data']));

            return response()->json(
                [
                    'status' => 0,
                    'message' => '',
                    'id' => $photoId
                ]
            );

        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }
    }

    function updatePropertyPicture()
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }
    }

    function removePropertyPicture()
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }
    }


    function getPropertyPictureCategory($urlId, $cat): \Illuminate\Foundation\Application|\Illuminate\Http\Response|JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $sessionUsrId = '';
        try {
            $sessionUsrId = $this->accessUserIdFromAuthToken();

            //  if( $sessionUsrId == $this->nullUser)
            //      throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 5000, 'message' => 'Unknown session'], 422);
        }

        $data = PropPhoto::query()
            ->where('id_propiedad', $urlId)
            ->where('id_categoria', $cat)
            ->select(['id_foto'])->get();

        if ($data->count() == 0) {
            return response()->json(
                [
                    'status' => 0,
                    'message' => ''
                ]
            );
        } else {

            $imagePath = 'images/properties/' . $urlId . '/' . $data->first()['id_foto'] . '.jpg';
            $image = Storage::get($imagePath );

            return response($image, 200)->header('Content-type', Storage::mimeType($imagePath));

        }

    }
}

/*try {
            $validator = Validator::make(
                request()->all(),
                [
                    'id' => 'required|integer'
                ]
            );

            $validator->validate();
            $inputData = $validator->validated();



        } catch (ValidationException $e) {
            return response()->json(['status' => 3000, 'message' => 'Wrong input format'], 422);
        }*/

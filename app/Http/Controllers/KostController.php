<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Kost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exceptions;
use Validator;

class KostController extends Controller
{
    public $successStatCode = 200;

    public function index(Request $request)
    {
        $name = $request->query("name");
        $city = $request->query("city");
        $ownerId = !is_null($request->query("owner_id")) ? $request->query("owner_id") : NULL;
        $priceMin = is_null($request->query("price_min")) ? 0 : (int) $request->query("price_min");
        $priceMax = is_null($request->query("price_max")) ? 0 : (int) $request->query("price_max");
        $sort = $request->query("sort");
        $sortBy = $request->query("by");
        $kosts = $this->getData($ownerId, $name, $city, $priceMin, $priceMax, $sort, $sortBy);

        return response()->json(
            [
                "code" => $this->successStatCode,
                "status" => true,
                "message" => trans('messages-success.success'),
                "data" => $kosts,
            ],
            $this->successStatCode
        );
    }

    public function getData($ownerId, $name, $city, $priceMin, $priceMax, $sort, $sortBy)
    {
        $all = Kost::all();
        $sortBy = !is_null($sortBy) ? $sortBy : "available_rooms";

        //filter OWNER id
        if (!is_null($ownerId)) {
            $all = $all->where("owner_id", (int) $ownerId);
        }

        //filter city / location
        if (!is_null($city)) {
            $all = $all->where("city", $city);
        }

        //filter name
        if (!is_null($name)) {
            $all = $all->where("name", $name);
        }

        //filter price range
        if ($priceMin > 0) {
            $all = $all->where("price", '>=' ,$priceMax);
        }
        if ($priceMax > 0) {
            $all = $all->where("price", '<=' ,$priceMax);
        }

        if ($sort == "desc") {
            $all = $all
                ->sortByDesc($sortBy)
                ->values()
                ->all();
        } else {
            $all = $all
                ->sortBy($sortBy)
                ->values()
                ->all();
        }
        return $all;
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user["role"] == 0) {
            return response()->json(
                [
                    "code" => 401,
                    "status" => false,
                    "message" => trans('messages-error.401'),
                ],
                401
            );
        }

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "city" => "required",
            "room_length" => "required|numeric|gt:0",
            "room_width" => "required|numeric|gt:0",
            "available_rooms" => "required|numeric|min:0",
            "price" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "code" => 400,
                    "status" => false,
                    "message" => $validator->errors(),
                ],
                400
            );
        }

        $input = $request->all();
        $input["owner_id"] = $user["id"];

        $kost = Kost::create($input);
        $kost["room_size"] = $kost->roomSize();

        return response()->json(
            [
                "code" => $this->successStatCode,
                "status" => true,
                "message" => "Add kost success",
                "message" => trans('messages-success.api-add-kost-success'),
                "data" => $kost,
            ],
            $this->successStatCode
        );
    }

    public function show($id)
    {
        $kost = Kost::find($id);
        if ($kost == null) {
            return response()->json(
                [
                    "code" => 204,
                    "status" => true,
                    "message" => trans('messages-error.api-kost-not-found'),
                ],
                204
            );
        }
        $kost["room_size"] = $kost->roomSize();
        return response()->json(
            [
                "code" => $this->successStatCode,
                "status" => true,
                "message" => trans('messages-success.success'),
                "data" => $kost,
            ],
            200
        );
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $kost = Kost::find($id);

        //return error not found kost resp
        if (is_null($kost)) {
            return response()->json(
                [
                    "code" => $this->successStatCode,
                    "status" => false,
                    "message" => "Kost not found",
                ],
                204
            );
        }
        //return error 401
        if ($user["role"] == 0 || $kost["owner_id"] != $user["id"]) {
            return response()->json(
                [
                    "code" => 401,
                    "status" => false,
                    "message" => trans('messages-error.401'),
                ],
                401
            );
        }

        // perform update
        if ($request["name"] != null) {
            $kost["name"] = $request["name"];
        }
        if ($request["room_length"] != null) {
            $kost["room_length"] = $request["room_length"];
        }
        if ($request["room_width"] != null) {
            $kost["room_width"] = $request["room_width"];
        }
        if ($request["price"] != null) {
            $kost["price"] = $request["price"];
        }
        if ($request["available_rooms"] != null) {
            $kost["available_rooms"] = $request["available_rooms"];
        }
        if ($request["city"] != null) {
            $kost["city"] = $request["city"];
        }
        try {
            $kost->save();
        } catch (Exception $e) {
        }
        $kost["room_size"] = $kost->roomSize();

        return response()->json(
            [
                "code" => $this->successStatCode,
                "status" => true,
                "message" => trans('messages-success.api-edit-kost-success'),
                "data" => $kost,
            ],
            $this->successStatCode
        );
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $kost = Kost::find($id);

        if ($kost == null) {
            return response()->json(
                [
                    "code" => 204,
                    "status" => true,
                    "message" => trans('messages-error.api-kost-not-found'),
                ],
                204
            );
        }

        if ($user["role"] == 0 || $kost["owner_id"] != $user["id"]) {
            return response()->json(
                [
                    "code" => 401,
                    "status" => false,
                    "message" => trans('messages-error.401'),
                ],
                401
            );
        }

        Kost::destroy($id);
        return response()->json(
            [
                "code" => $this->successStatCode,
                "status" => true,
                "message" => trans('messages-success.api-delete-kost-success'),

            ],
            $this->successStatCode
        );
    }
}
?>

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kost;
use Illuminate\Support\Facades\Auth;
use Validator;

class OwnerController extends Controller
{
    public $successStatCode = 200;

    public function getKostList(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->role != "1") {
            return response()->json(
                [
                    "code" => 401,
                    "status" => false,
                    "message" => trans('messages-error.401'),
                ],
                401
            );
        }

        $name = $request->query("name");
        $city = $request->query("city");
        $ownerId = $user && $user ? $user->id : NULL;
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
        $all = Kost::where('owner_id', $ownerId)->get();
        $sortBy = !is_null($sortBy) ? $sortBy : "available_rooms";

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
}

?>

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class OwnerController extends Controller
{
    public $successStatCode = 200;

    public function getKostList()
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

        return response()->json(
            [
                "code" => $this->successStatCode,
                "status" => true,
                "message" => trans('messages-success.success'),
                "data" => $user,
            ],
            $this->successStatCode
        );
    }
}

?>

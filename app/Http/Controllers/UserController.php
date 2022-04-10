<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
    public $successStatCode = 200;

    public function login()
    {
        if ( Auth::attempt([ "email" => request("email"), "password" => request("password")]) ) {
            $user = Auth::user();
            $success["token"] = $user->createToken("MyApp")->accessToken;

            return response()->json(
                [
                    "code" => $this->successStatCode,
                    "status" => true,
                    "message" => trans('messages-success.api-login-success'),
                    "data" => $success,
                ],
                $this->successStatCode
            );
        } else {
            return response()->json(
                [
                    "code" => 401,
                    "status" => false,
                    "message" => "Login failed",
                    "message" => trans('messages-error.login-failed'),
                ],
                401
            );
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email",
            "password" => "required",
            "confirm_password" => "required|same:password",
            "role" => "required|numeric|min:0|max:1",
            "premium_user" => "required|numeric|min:0|max:1",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "code" => 401,
                    "status" => false,
                    "message" => $validator->errors(),
                ],
                401
            );
        }

        $input = $request->all();
        $input["password"] = bcrypt($input["password"]);
        //determine the credit and premium status for regular/premium user
        if ($input["role"] == "0") {
            if ($input["premium_user"] == "1") {
                $input["credits"] = 40;
                $input["premium_user"] = "1";
            } else {
                $input["credits"] = 20;
                $input["premium_user"] = "0";
            }
        } else {
            $input["credits"] = 0;
            $input["premium_user"] = "0";
        }
        $user = User::create($input);

        $success["token"] = $user->createToken("MyApp")->accessToken;
        $success["name"] = $user->name;

        return response()->json(
            [
                "code" => $this->successStatCode,
                "status" => true,
                "message" => "Register success",
                "message" => trans('messages-success.api-register-success'),
                "data" => $success,
            ],
            $this->successStatCode
        );
    }

    public function details()
    {
        $user = Auth::user();
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

    public function upgradeStatus()
    {
        $user = Auth::user();

        $user["credits"] = 40;
        $user["premium_user"] = "1";
        $user->save();

        return response()->json(
            [
                "code" => $this->successStatCode,
                "status" => true,
                "message" => trans('messages-success.api-upgrade-membership-success'),
                "data" => $user,
            ],
            $this->successStatCode
        );
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        return response()->json(
            [
                "code" => $this->successStatCode,
                "status" => true,
                "message" => trans('messages-success.api-logout-success'),
                "data" => [],
            ],
            $this->successStatCode
        );
    }
}

?>

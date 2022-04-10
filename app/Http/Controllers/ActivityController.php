<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class ActivityController extends Controller {
	public $successStatCode = 200;

	public function askQuestion(Request $request) {
		$user = Auth::user();
        if ($user && $user->role != "0") {
            return response()->json(
                [
                    "code" => 401,
                    "status" => false,
                    "message" => trans('messages-error.401'),
                ],
                401
            );
        }

		$credit_available = $user['credits'];
        //return error not enough credits
		if ($credit_available - $request['credit_usage'] < 0) {
			return response()->json([
				'code' => $this->successStatCode,
				'status' => true,
                "message" => trans('messages-error.api-not-enoguh-credit'),
				'data' => []
			], $this->successStatCode);
		}

        //return error no ownerid
		$recipient = User::where('id', $request['recipient_id'])->where('role', '1')->first();
		if ($recipient == null){
			return response()->json([
				'code' => 200,
				'status' => true,
                "message" => trans('messages-error.api-unknown-kost-ownerid'),
				'data' => $recipient
			], 200);
		}

		$input = $request->all();
		$input['user_id'] = $user['id'];
		$input['credit_left'] = $credit_available - $request['credit_usage'];

		$activity = UserActivityLog::create($input);

		$user['credits'] = $input['credit_left'];
		$user->save();

		return response()->json([
            'code' => $this->successStatCode,
            'status' => true,
            'message' => 'Add activity success',
            "message" => trans('messages-success.api-add-activity-success'),
            'data' => $activity
        ], $this->successStatCode);
	}
}
?>

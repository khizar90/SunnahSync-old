<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class WalletController extends Controller
{
    public function create(Request $request){
        return response()->json([
            'status' => true,
            'action' => "Wallet Created Successfully",
        ]);
    }

    public function detail($id){
        $obj = new stdClass();
        return response()->json([
            'status' => true,
            'action' => "Wallet Detail",
            'data' =>$obj
        ]);
    }

    public function payout(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        return response()->json([
            'status' => true,
            'action' => "Payout Successfully",
        ]);
    }
}

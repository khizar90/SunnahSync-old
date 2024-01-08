<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    public function zoomToken()
    {
      
        
        $key = config('17r5O8QQRWtRL9qxuc0SQ', '');
        $secret = config('zn3cNnJ9tNMvPjoGrprJf9hgy65iJ0RJ', '');
        $iat = round(time() / 1000) - 30;
        $exp = $iat + 60  60  2;
        $payload = [
            'iss' => $key,
            'exp' => $exp,
        ];
        $token = JWT::encode($payload, $secret, 'HS256');
        return response()->json(['status'=> true, 'data'=> $token, 'action'=> 'Zoom Token']);
    }
}

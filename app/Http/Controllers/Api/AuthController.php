<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EditProfileRequest;
use App\Http\Requests\Auth\LoginReqeust;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyReqeust;
use App\Models\Social;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function userVerify(VerifyReqeust $request)
    {
        return $this->authService->userVerify($request);
    }

    public function otpVerify(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:user_verifies,email',
                'otp' => 'required|min:6'
            ],
            [
                'email.required' => 'Please enter the Email Address',
                'email.email' => 'Please enter a valid Email Address',
                'email.exists' => 'The provided Email does not exist in our records',
                'otp.required' => 'Please enter the OTP Code',
                'otp.min' => 'Please enter atleast 6 digit',
            ]
        );
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        return $this->authService->otpVerify($request);
    }

    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request);
    }

    public function login(LoginReqeust $request)
    {
        return $this->authService->login($request);
    }

    public function recover(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Please enter the Email Address',
            'email.email' => 'Please enter a valid Email Address',
            'email.exists' => 'The Email Adress is not registered ',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        return $this->authService->recover($request);
    }

    public function recoverVerify(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|min:6'
            ],
            [
                'email.required' => 'Please enter the Email Address',
                'email.email' => 'Please enter a valid Email Address',
                'email.exists' => 'The Email Adress is not registered ',
                'otp.min' => 'Please enter atleast 6 digit'
            ]
        );
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        return $this->authService->recoverVerify($request);
    }

    public function newPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6'
        ], [
            'email.required' => 'Please enter the Email Address',
            'email.email' => 'Please enter a valid Email Address',
            'email.exists' => 'The Email Adress is not registered ',
            'password.required' => 'Please enter the Password',
            'password.min' => 'Please enter atleast 6 characters in Password',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        return $this->authService->newPassword($request);
    }

    public function changePassword(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required'
        ], [
            'old_password.required' => 'Please enter the Old Password',
            'new_password.required' => 'Please enter the New Password',
            'new_password.min' => 'Please enter atleast 6 characters in New Password',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        return $this->authService->changePassword($request, $user_id);
    }




    public function editProfile(EditProfileRequest $request)
    {
        return $this->authService->editProfile($request);
    }

    public function editImage(Request $request){
        return $this->authService->editImage($request);
    }


    public function getVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }

        return $this->authService->getVerify($request);
    }

    public function deleteAccount(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }
        return $this->authService->deleteAccount($request, $user_id);
    }

    public function removeImage($user_id)
    {

        return $this->authService->removeImage($user_id);
    }

    public function addDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required',
            'title' => 'required',
            'type' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }

        return $this->authService->addDetail($request);
    }

    public function getDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage
            ]);
        }

        return $this->authService->getDetail($request);
    }

    public function deleteDetail($id)
    {
        return $this->authService->deleteDetail($id);
    }

    public function socialLogin(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'platform_id' => 'required',
                'platform' => 'required',
                'platform_email' => 'required',
            ],
            [
                'platform_id.required' => 'Please enter the Platform  ID',
                'platform.required' => 'Please enter the Platform',
                'platform_email.required' => 'Please enter the Platform Email Address',
            ]
        );

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        return $this->authService->socialLogin($request);
    }

    public function logout(Request $request)
    {
        $check = User::find($request->user_id);
        if (!$check)
            return response()->json([
                'status' => true,
                'action' => 'User not found'
            ]);
        else {
            $checkDevice = UserDevice::where('user_id', $check->id)->where('device_id', $request->device_id)->first();
            if ($checkDevice) {
                // $checkDevice->token = '';
                $checkDevice->delete();
            }
            return response()->json([
                'status' => true,
                'action' => 'User logged out'
            ]);
        }
    }

    public function removeSocial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'platform' => 'required',
            'platform_email' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        return $this->authService->removeSocial($request);
    }

    public function socialConnect(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'platform_id' => 'required',
            'platform' => 'required',
            'platform_email' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        return $this->authService->socialConnect($request);

    }
    public function getSocial($id){
        return $this->authService->getSocial($id);
    }
}

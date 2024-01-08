<?php

namespace  App\Services\Auth;

use App\Mail\EmailSend;
use App\Models\ImageVerify;
use App\Models\ScholarDetail;
use App\Models\Social;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserDevices;
use App\Models\UserVerify;
use GuzzleHttp\Psr7\Request;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthService
{


    public function userVerify($request)
    {
        // $phone = $request->country_code . $request->phone;
        $verify = User::where('country_code', $request->country_code)->where('phone', $request->phone)->first();
        if ($verify) {
            return response()->json([
                'status' => false,
                'action' => 'This phone is already exists'
            ]);
        } else {
            $otp = random_int(100000, 999999);

            $mail_details = [
                'body' => $otp,
            ];
            // Mail::to($request->email)->send(new EmailSend($mail_details));


            $user = new UserVerify();
            $user->email = $request->email;
            $user->otp = $otp;
            $user->save();
            return response()->json([
                'status' => true,
                'action' => 'User verify and OTP send',
            ]);
        }
    }

    public function otpVerify($request)
    {
        // $user = UserVerify::where('email', $request->email)->latest()->first();
        $otp = 123456;
        if ($otp) {

            if ($request->otp == $otp) {
                // $user = UserVerify::where('email', $request->email)->delete();
                return response()->json([
                    'status' => true,
                    'action' => 'OTP verify',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => 'OTP is invalid, Please enter a valid OTP',
                ]);
            }
        }
    }



    public function register($request)
    {
        // $phone = $request->country_code . $request->phone;
        $user = User::where('country_code', $request->country_code)->where('phone', $request->phone)->first();
        if ($user) {
            return response()->json([
                'status' => false,
                'action' => 'Phone number  is already registered '
            ]);
        } else {
            $user = new User();
            $user->name = $request->full_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->country_code = $request->country_code;
            $user->type = $request->type;
            $user->gender = '';
            $user->password = Hash::make($request->password);
            $user->save();
            $userdevice = new UserDevice();
            $userdevice->user_id = $user->id;
            $userdevice->device_name = $request->device_name ?? 'No name';
            $userdevice->device_id = $request->device_id ?? 'No ID';
            $userdevice->timezone = $request->timezone ?? 'No Time';
            $userdevice->token = $request->fcm_token ?? 'No tocken';
            $userdevice->save();



            $newuser  = User::find($user->id);
            $newuser->platform  = 'noraml';

            return response()->json([
                'status' => true,
                'action' => 'User register successfully',
                'data' => $newuser
            ]);
        }
    }


    public function login($request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $userdevice = new UserDevice();
                $userdevice->user_id = $user->id;
                $userdevice->device_name = $request->device_name ?? 'No name';
                $userdevice->device_id = $request->device_id ?? 'No ID';
                $userdevice->timezone = $request->timezone ?? 'No Time';
                $userdevice->token = $request->fcm_token ?? 'No tocken';
                $userdevice->save();

                $user->platform  = 'normal';
                $user->accounts = [];

                return response()->json([
                    'status' => true,
                    'action' => "Login successfully",
                    'data' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => 'Password is invalid, please enter a valid Password',
                ]);
            }
        }
    }

    public function recover($request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $otp = random_int(100000, 999999);
            $user = User::where('email', $request->email)->update([
                'otp' => $otp,
                'otp_time' => now()
            ]);

            $mail_details = [
                'body' => $otp,
            ];

            // Mail::to($request->email)->send(new EmailSend($mail_details));

            return response()->json([
                'status' => true,
                'action' => 'OTP send successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'The Email Adress is not registered'
            ]);
        }
    }


    public function recoverVerify($request)
    {
        $user = User::where('email', $request->email)->first();
        $otp = 123456;

        if ($user) {

            if ($otp == $request->otp) {
                User::where('email', '=', $request->email)->update([
                    'otp' => '',
                    'otp_time' => ''
                ]);

                return response()->json([
                    'status' => true,
                    'action' => 'OTP verify successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => 'OTP is invalid, Please enter a valid OTP'
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'action' => 'The Email Adress is not registered'
            ]);
        }
    }


    public function newPassword($request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'action' => "New password is same as Old password",
                ]);
            } else {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
                return response()->json([
                    'status' => true,
                    'action' => "New password set",
                ]);
            }
            // $user->update([
            //     'password' => Hash::make($request->password)
            // ]);
            return response()->json([
                'status' => true,
                'action' => "New Password set"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'The Email Adress is not registered'
            ]);
        }
    }
    public function changePassword($request, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            if (Hash::check($request->old_password, $user->password)) {
                if (Hash::check($request->new_password, $user->password)) {

                    return response()->json([
                        'status' => false,
                        'action' => "New password is same as old password",
                    ]);
                } else {
                    $user->update([
                        'password' => Hash::make($request->new_password)
                    ]);
                    return response()->json([
                        'status' => true,
                        'action' => "Password  change",
                    ]);
                }
            }
            return response()->json([
                'status' => false,
                'action' => "Old password is wrong",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => 'User not found'
            ]);
        }
    }

    public function editProfile($request)
    {

        $user = User::find($request->user_id);
        // $phone = $request->country_code . $request->phone;
        if ($user) {
            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('dob')) {
                if ($request->dob == null) {
                    $user->dob = '';
                } else {
                    $user->dob = $request->dob;
                }
            }


            if ($request->has('gender')) {
                if ($request->gender == null) {
                    $user->gender = '';
                } else {
                    $user->gender = $request->gender;
                }
            }

            if ($request->has('about')) {
                if ($request->about == null) {
                    $user->about = '';
                } else {
                    $user->about = $request->about;
                }
            }



            if ($request->has('location')) {
                if ($request->location == null) {
                    $user->location = '';
                    $user->lat = '';
                    $user->lng = '';
                } else {
                    $user->location = $request->location;
                    $user->lat = $request->lat;
                    $user->lng = $request->lng;
                }
            }


            if ($request->has('email')) {
                if (User::where('email', $request->email)->where('id', '!=', $request->user_id)->exists()) {
                    return response()->json([
                        'status' => false,
                        'action' => 'Email already taken'
                    ]);
                } else {
                    $user->email = $request->email;
                }
            }





            if ($request->has('phone') || $request->has('country_code')) {

                if (User::where('country_code', $request->country_code)->where('phone', $request->phone)->where('id', '!=', $request->user_id)->exists()) {
                    return response()->json([
                        'status' => false,
                        'action' => 'Phone already taken'
                    ]);
                } else {

                    $user->country_code = $request->country_code;
                    $user->phone = $request->phone;
                }
            }


            $user->save();
            $user->platform = 'normal';
            return response()->json([
                'status' => true,
                'action' => "Profile edit",
                'data' => $user
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "User not found"
            ]);
        }
    }

    public function editImage($request)
    {

        $user = User::find($request->user_id);
        if ($user) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $mime = explode('/', $file->getClientMimeType());
                $filename = time() . '-' . uniqid() . '.' . $extension;
                if ($file->move('uploads/profile/', $filename))
                    $image = '/uploads/profile/' . $filename;
                $user->image = $image;
            }
            $user->save();


            return response()->json([
                'status' => true,
                'action' => "Profile edit",
                'data' => $user
            ]);
        }

        return response()->json([
            'status' => false,
            'action' => "User not found"
        ]);
    }

    public function getVerify($request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            $userImage =  new ImageVerify();
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/verify/', $filename))
                $image = '/uploads/verify/' . $filename;

            $userImage->user_id = $request->user_id;
            $userImage->image = $image;
            $user->verify = 2;
            $userImage->save();
            $user->save();

            return response()->json([
                'status' => true,
                'action' => "Request submited"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "User not found"
            ]);
        }
    }

    public function deleteAccount($request, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $user->delete();
                return response()->json([
                    'status' => true,
                    'action' => "Account deleted",
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => 'Please enter correct password',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'action' => "User not found"
            ]);
        }
    }

    public function removeImage($user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $user->image = '';
            $user->save();
            return response()->json([
                'status' => true,
                'action' => "Image remove"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "User not found"
            ]);
        }
    }

    public function addDetail($request)
    {
        $user = User::find($request->user_id);
        if ($user->type == 'Scholar') {
            $detail = new ScholarDetail();
            $detail->user_id = $request->user_id;
            if ($request->type == 'education'  || $request->type == 'experience') {
                $detail->name = $request->name;
                $detail->title = $request->title;
                $detail->type = $request->type;
                $detail->start = $request->start;
                $detail->end = $request->end;
            }

            if ($request->type == 'certification') {
                $detail->name = $request->name;
                $detail->title = $request->title;
                $detail->type = $request->type;
                $detail->start = $request->start;
            }

            if ($request->type == 'services') {
                $detail->name = $request->name;
                $detail->title = $request->title;
                $detail->type = $request->type;
            }

            $detail->save();
            return response()->json([
                'status' => true,
                'action' => $request->type  . " added"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "User cannot update these detail"
            ]);
        }
    }

    public function getDetail($request)
    {

        $detail = ScholarDetail::where('type', $request->type)->where('user_id', $request->user_id)->get();

        return response()->json([
            'status' => true,
            'action' => "list of " . $request->type,
            'data' => $detail
        ]);
    }

    public function deleteDetail($id)
    {
        $detail = ScholarDetail::find($id);
        if ($detail) {
            $detail->delete();
            return response()->json([
                'status' => true,
                'action' => "Detail deleted",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "Detail not found"
            ]);
        }
    }

    public function socialLogin($request)
    {
        $normal = User::where('email', $request->platform_email)->first();
        if ($normal) {
            $normal = true;
        } else {
            $normal = false;
        }

        $user = Social::where('platform', $request->platform)->where('platform_id', $request->platform_id)->where('platform_email', $request->platform_email)->first();
        if ($user) {
            $data = User::find($user->user_id);
            if ($data) {
                $userdevice = new UserDevice();
                $userdevice->user_id = $user->id;
                $userdevice->device_name = $request->device_name ?? 'No name';
                $userdevice->device_id = $request->device_id ?? 'No ID';
                $userdevice->timezone = $request->timezone ?? 'No Time';
                $userdevice->token = $request->fcm_token ?? 'No tocken';
                $userdevice->save();

                $platform = Social::where('user_id', $user->user_id)->get();
                $data->platform  =  $request->platform;
                $data->accounts = $platform;

                return response()->json([
                    'status' => true,
                    'action' => "Login Successfuly",
                    'data' => $data,
                    'normal' => $normal

                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' => "Email Adress is not connected with Social Account",
                    'normal' => $normal
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'action' => "Email Adress is not connected with Social Account",
                'normal' => $normal
            ]);
        }
    }

    public function socialConnect($request)
    {
        $find = Social::where('platform', $request->platform)->where('user_id', $request->user_id)->first();
        if ($find) {
            return response()->json([
                'status' => false,
                'action' =>  'Account already exist on this platform',
            ]);
        }
        $social = new Social();
        $social->user_id = $request->user_id;

        $social->platform = $request->platform;
        $social->platform_id = $request->platform_id;
        $social->platform_email = $request->platform_email;
        $social->save();
        $account = Social::where('user_id', $request->user_id)->get();


        return response()->json([
            'status' => true,
            'action' =>  'Connected Successfully',
            'data' => $account
        ]);
    }

    public function removeSocial($request)
    {


        $remove = Social::where('user_id', $request->user_id)->where('platform', $request->platform)->where('platform_email', $request->platform_email)->first();
        if ($remove) {
            $remove->delete();

            $accounts = Social::where('user_id', $request->user_id)->get();
            return response()->json([
                'status' => true,
                'action' =>  'Social account removed',
                'data' => $accounts
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' =>  'Please enter correct Email and Platform',
            ]);
        }
    }

    public function getSocial($id)
    {
        $user = User::find($id);
        if ($user) {
            $social = Social::where('user_id', $id)->get();
            return response()->json([
                'status' => true,
                'action' =>  'Social Accounts',
                'data' => $social
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }
}

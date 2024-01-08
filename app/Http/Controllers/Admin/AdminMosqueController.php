<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FirebaseNotification;
use App\Actions\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\RejectMosque;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class AdminMosqueController extends Controller
{
    public function list($status)
    {
        if ($status == 'all') {
            $mosques = Mosque::where('status', 1)->latest()->get();
            foreach ($mosques as $mosque) {
                $user = User::find($mosque->user_id);
                $mosque->user = $user;
            }
        } else {
            $mosques = Mosque::where('status', 0)->latest()->get();
            foreach ($mosques as $mosque) {
                $user = User::find($mosque->user_id);
                $mosque->user = $user;
            }
        }

        return view('mosque.index', compact('mosques'));
    }

    public function approve($id)
    {
        $mosque = Mosque::find($id);
        if ($mosque) {
            $mosque->status= 1;
            $mosque->save();
            $user = User::find($mosque->user_id);
            NewNotification::handle($user,0,$mosque->id,'Your mosque is approved by the admin','approved','mosque');
            $tokens = UserDevice::where('user_id', $user->id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            FirebaseNotification::handle($tokens, 'Your mosque is approved by the admin', 'Mosque Approved', ['data_id' => $mosque->id, 'type' => 'mosque']);
            
            return redirect()->back()->with('success' ,' Mosque approved');

        }
        else{
            return redirect()->back();
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FirebaseNotification;
use App\Actions\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationCategory;
use App\Models\RejectDonation;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class AdminDonationController extends Controller
{
    public function getCategory()
    {
        $categories = DonationCategory::where('status',0)->get();

        return view('donation.category', compact('categories'));
    }

    public function deleteCategory($id)
    {
        $category = DonationCategory::find($id);
        $category->status = 1;
        $category->save();
        return redirect()->back()->with('delete', 'Category  Deleted');
    }

    public function addCategory(Request $request)
    {
        $category = new DonationCategory();
        $category->name = $request->name;
        $category->save();
        return redirect()->back()->with('success', 'Category  Added Successfully');
    }


    public function donation()
    {
        $donations = Donation::where('status', 1)->where('is_complete', 0)->latest()->get();
        foreach ($donations as $donation) {
            $user = User::find($donation->user_id);
            $category = DonationCategory::find($donation->category_id);
            $donation->user = $user;
            $donation->category = $category;
        }
        return view('donation.index', compact('donations'));
    }



    public function pending()
    {
        $donations = Donation::where('status', 0)->where('is_reject', 0)->latest()->get();
        foreach ($donations as $donation) {
            $user = User::find($donation->user_id);
            $category = DonationCategory::find($donation->category_id);
            $donation->user = $user;
            $donation->category = $category;
        }
        return view('donation.pending', compact('donations'));
    }

    public function reject(Request $request, $id)
    {
        $donation = Donation::find($id);
        if ($donation) {
            $donation->is_reject = 1;

            $reason = new RejectDonation();
            $reason->user_id = $request->user_id;
            $reason->donation_id = $request->donation_id;
            $reason->reason = $request->reason;
            $reason->save();
            $donation->save();
            $user = User::find($donation->user_id);
            NewNotification::handle($user, 0, $donation->id, 'Your donation request is declined', 'declined', 'donation');
            $tokens = UserDevice::where('user_id', $user->id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            FirebaseNotification::handle($tokens, 'Your donation request is declined', 'Donation Request Declined', ['data_id' => $donation->id, 'type' => 'donation']);
            return redirect()->back()->with('delete', 'Donation reject');
        }
        return redirect()->back();
    }

    public function approve($id)
    {
        $donation = Donation::find($id);
        if ($donation) {
            $donation->status = 1;
            $donation->save();
            $user = User::find($donation->user_id);
            NewNotification::handle($user, 0, $donation->id, 'Your donation request is approved', 'approved', 'donation');
            $tokens = UserDevice::where('user_id', $user->id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            FirebaseNotification::handle($tokens, 'Your donation request is approved', 'Donation Request Approved', ['data_id' => $donation->id, 'type' => 'donation']);
            return redirect()->back()->with('success', 'Donation approved');
        }
        return redirect()->back();
    }
}

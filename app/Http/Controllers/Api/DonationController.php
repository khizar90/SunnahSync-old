<?php

namespace App\Http\Controllers\Api;

use App\Actions\FirebaseNotification;
use App\Actions\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationAmount;
use App\Models\DonationCategory;
use App\Models\RejectDonation;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    public function list()
    {
        $topDonations = Donation::select('id', 'title', 'image', 'amount', 'category_id')
            ->where('status', 1)
            ->where('is_complete', 0)
            ->orderByDesc(DB::raw('(SELECT SUM(donated_amount) FROM donation_amounts WHERE donation_id = donations.id)'))
            ->limit(1)
            ->get();

        foreach ($topDonations as $item) {
            $category = DonationCategory::find($item->category_id);
            $item->category = $category ? $category->name : null;

            $donated_amount = DonationAmount::where('donation_id', $item->id)->sum('donated_amount');
            $item->donation_raised = $donated_amount;
        }

        $otherDonations = Donation::select('id', 'title', 'image', 'amount', 'category_id')
            ->where('status', 1)
            ->where('is_complete', 0)
            // ->whereNotIn('id', $topDonations->pluck('id'))
            ->latest()
            ->paginate(12);

        foreach ($otherDonations as $item) {
            $category = DonationCategory::find($item->category_id);
            $item->category = $category ? $category->name : null;

            $donated_amount = DonationAmount::where('donation_id', $item->id)->sum('donated_amount');
            $item->donation_raised = $donated_amount;
        }

        return response()->json([
            'status' => true,
            'action' => 'Donations',
            'data' => [
                'top_donations' => $topDonations,
                'donations' => $otherDonations
            ]
        ]);
    }

    public function trending()
    {
        $topDonations = Donation::select('id', 'title', 'image', 'amount', 'category_id')
            ->where('status', 1)
            ->where('is_complete', 0)
            ->orderByDesc(DB::raw('(SELECT SUM(donated_amount) FROM donation_amounts WHERE donation_id = donations.id)'))
            ->paginate(12);
            // ->limit(10)
            // ->get();

        foreach ($topDonations as $item) {
            $category = DonationCategory::find($item->category_id);
            $item->category = $category ? $category->name : null;

            $donated_amount = DonationAmount::where('donation_id', $item->id)->sum('donated_amount');
            $item->donation_raised = $donated_amount;
        }

        return response()->json([
            'status' => true,
            'action' => 'Trending Donations',
            'data' => $topDonations,
        ]);
    }

    // public function allDonation()
    // {
    //     $otherDonations = Donation::select('id', 'title', 'image', 'amount', 'category_id')
    //         ->where('status', 1)
    //         ->where('is_complete', 0)
    //         // ->whereNotIn('id', $topDonations->pluck('id'))
    //         ->latest()
    //         ->get();

    //     foreach ($otherDonations as $item) {
    //         $category = DonationCategory::find($item->category_id);
    //         $item->category = $category ? $category->name : null;

    //         $donated_amount = DonationAmount::where('donation_id', $item->id)->sum('donated_amount');
    //         $item->donation_raised = $donated_amount;
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'action' => 'Donations',
    //         'data' => $otherDonations
    //     ]);
    // }

    public function donationCategory($id)
    {
        $category = DonationCategory::find($id);
        if ($category) {
            $donation = Donation::select('id', 'title', 'image', 'amount', 'category_id')->where('status', 1)->where('is_complete', 0)->where('category_id', $id)->latest()->paginate(12);
            foreach ($donation as $item) {
                $category = DonationCategory::find($item->category_id);
                $item->category = $category->name;
                $donated_amount = DonationAmount::where('donation_id', $item->id)->sum('donated_amount');
                $item->donation_raised = $donated_amount;
            }
            return response()->json([
                'status' => true,
                'action' =>  'Donations',
                'data' => $donation
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Invalid category id',
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'image' => 'required',
            'title' => 'required',
            'category_id' => 'required|exists:donation_categories,id',
            'amount' => 'required|numeric',
            'description' => 'required',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $donation = new Donation();
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $mime = explode('/', $file->getClientMimeType());
        $filename = time() . '-' . uniqid() . '.' . $extension;
        if ($file->move('uploads/donation/', $filename)) {
            $image = '/uploads/donation/' . $filename;
        }

        $donation->user_id = $request->user_id;
        $donation->title = $request->title;
        $donation->image = $image;
        $donation->category_id = $request->category_id;
        $donation->amount = $request->amount;
        $donation->description = $request->description;
        $donation->save();
        return response()->json([
            'status' => true,
            'action' =>  "Donation added",
        ]);
    }

    public function pendingDonation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        if ($request->status == 'pending') {
            $data = Donation::select('id', 'title', 'amount', 'category_id')->where('status', 0)->where('is_reject', 0)->where('user_id', $id)->latest()->paginate(12);
            foreach ($data as $item) {
                $category = DonationCategory::find($item->category_id);
                $item->category = $category->name;
                $item->donation_raised = 0;
            }
        }
        if ($request->status == 'rejected') {
            $data = Donation::select('id', 'title', 'amount', 'category_id')->where('is_reject', 1)->where('user_id', $id)->latest()->paginate(12);
            foreach ($data as $item) {
                $category = DonationCategory::find($item->category_id);
                $item->category = $category->name;
                $item->donation_raised = 0;
                
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  "Donations " . $request->status,
            'data' => $data
        ]);
    }

    public function donationDetail($id)
    {

        $donation = Donation::find($id);
        if ($donation) {
            $donated_amount = DonationAmount::where('donation_id', $donation->id)->sum('donated_amount');
            $category = DonationCategory::find($donation->category_id);
            $donation->category = $category ? $category->name : null;
            $donation->donation_raised = $donated_amount;
            $reason = RejectDonation::where('donation_id',$id)->first();
            if($reason){
                $donation->reason =  $reason->reason;
            }
            else{
                $donation->reason =  "";

            }
            return response()->json([
                'status' => true,
                'action' =>  "Donations detail",
                'data' => $donation
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'action' =>  "No Donations found",
            ]);
        }
    }

    public function donationComplete(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        if ($request->status == 'ongoing') {
            $data = Donation::select('id', 'title', 'amount', 'category_id', 'image')->where('status', 1)->where('is_complete', 0)->where('user_id', $id)->latest()->paginate(12);
            foreach ($data as $item) {
                $category = DonationCategory::find($item->category_id);
                $item->category = $category->name;
                $donated_amount = DonationAmount::where('donation_id', $item->id)->sum('donated_amount');
                $item->donation_raised = $donated_amount;
            }
        }

        if ($request->status == 'complete') {
            $data = Donation::select('id', 'title', 'amount', 'category_id', 'image')->where('is_complete', 1)->where('user_id', $id)->latest()->paginate(12);
            foreach ($data as $item) {
                $category = DonationCategory::find($item->category_id);
                $item->category = $category->name;
                $donated_amount = DonationAmount::where('donation_id', $item->id)->sum('donated_amount');
                $item->donation_raised = $donated_amount;
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  "Donations " . $request->status,
            'data' => $data
        ]);
    }

    public function donateAmount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'donation_id' => 'required|exists:donations,id',
            'amount' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $amount = new DonationAmount();
        $amount->user_id = $request->user_id;
        $amount->donation_id = $request->donation_id;
        $amount->donated_amount = $request->amount;
        $amount->payment_id = $request->payment_id;
        $amount->save();


        $donation = Donation::find($request->donation_id);

        $other = User::find($request->user_id);
        $user = User::find($donation->user_id);
        NewNotification::handle($user, $other->id, $donation->id, 'has made a donation ', 'amount', 'donation');
        $tokens = UserDevice::where('user_id', $user->id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
        FirebaseNotification::handle($tokens, $other->name .' has made a donation ', 'Donation Recevied', ['data_id' => $donation->id, 'type' => 'donation']);


        $donatedAmount = DonationAmount::where('donation_id', $request->donation_id)->sum('donated_amount');
        if ($donatedAmount >= $donation->amount) {
            $donation->is_complete = 1;
            $donation->save();
            $user = User::find($donation->user_id);
            NewNotification::handle($user, 0, $donation->id, 'You have raised the required donation', 'complete', 'donation');
            $tokens = UserDevice::where('user_id', $user->id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            FirebaseNotification::handle($tokens, 'You have raised the required donation', 'Donation Completed', ['data_id' => $donation->id, 'type' => 'donation']);
        }


        return response()->json([
            'status' => true,
            'action' =>  "Donations send",
        ]);
    }

   

    public function serachDonation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required',
            
        ],[
            'value.required' => 'Please enter Keyword to search Doantion'
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $value = $request->value;
        $donation = Donation::select('id', 'title', 'image', 'amount', 'category_id')->where('title', 'like', '%' . $value . '%')
            ->where('status', 1)
            ->where('is_complete', 0)
            ->latest()
            ->paginate(12);

        foreach ($donation as $item) {
            $category = DonationCategory::find($item->category_id);
            $item->category = $category ? $category->name : null;

            $donated_amount = DonationAmount::where('donation_id', $item->id)->sum('donated_amount');
            $item->donation_raised = $donated_amount;
        }
        return response()->json([
            'status' => true,
            'action' =>  "Search Donations",
            'data' => $donation
        ]);
    }
}

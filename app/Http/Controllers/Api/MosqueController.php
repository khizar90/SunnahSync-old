<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use App\Models\Prayer;
use App\Models\SavedMosque;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class MosqueController extends Controller
{
    public function list(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $validator = Validator::make($request->all(), [
                'location' => 'required',
                'lat' => 'required',
                'lng' => 'required',
            ]);

            if ($validator->fails()) {
                $errorMessage = implode(', ', $validator->errors()->all());
                return response()->json([
                    'status' => false,
                    'action' =>  $errorMessage,
                ]);
            }

            $userLat = $request->lat;
            $userLng = $request->lng;
            $radius = 50;

            $nearbyMosques = DB::table('mosques')
                ->select(
                    '*',
                    DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
                )
                ->where('status', 1)
                ->having('distance', '<=', $radius)
                ->orderBy('distance')
                ->limit(10)
                ->get();

            foreach ($nearbyMosques as $item) {
                $save = SavedMosque::where('user_id', $user_id)->where('mosque_id', $item->id)->first();
                if ($save) {
                    $item->is_saved = true;
                } else {
                    $item->is_saved = false;
                }
            }

            $allMosques = Mosque::where('status', 1)->latest()->paginate(12);

            foreach ($allMosques as $item) {
                $saved = SavedMosque::where('user_id', $user_id)->where('mosque_id', $item->id)->first();
                if ($saved) {
                    $item->is_saved = true;
                } else {
                    $item->is_saved = false;
                }
            }
            return response()->json([
                'status' => true,
                'action' => 'Mosque list',
                'data' => [
                    'nearby_mosques' => $nearbyMosques,
                    'all_mosques' => $allMosques
                ]
            ]);
        } else {

            return response()->json([
                'status' => false,
                'action' => 'User not found',
            ]);
        }
    }

    public function nearby(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'location' => 'required',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode(', ', $validator->errors()->all());
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $userLat = $request->lat;
        $userLng = $request->lng;
        $radius = 50;

        $nearbyMosques = DB::table('mosques')
            ->select(
                '*',
                DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
            )
            ->where('status', 1)
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->paginate(12);

        foreach ($nearbyMosques as $item) {
            $saved = SavedMosque::where('user_id', $request->user_id)->where('mosque_id', $item->id)->first();
            if ($saved) {
                $item->is_saved = true;
            } else {
                $item->is_saved = false;
            }
        }

        return response()->json([
            'status' => true,
            'action' => 'Mosque list',
            'data' => $nearbyMosques,
        ]);
    }
    public function allmosque($user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $allMosques = Mosque::where('status', 1)->latest()->limit(10)->get();

            foreach ($allMosques as $item) {
                $saved = SavedMosque::where('user_id', $user_id)->where('mosque_id', $item->id)->first();
                if ($saved) {
                    $item->is_saved = true;
                } else {
                    $item->is_saved = false;
                }
            }
            return response()->json([
                'status' => true,
                'action' => 'Mosque list',
                'data' => $allMosques,
            ]);
        } else {

            return response()->json([
                'status' => false,
                'action' => 'User not found',
            ]);
        }
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'image' => 'required',
            'name' => 'required',
            'scholar' => 'required',
            'location' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'description' => 'required',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $mosque = new Mosque();
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $mime = explode('/', $file->getClientMimeType());
        $filename = time() . '-' . uniqid() . '.' . $extension;
        if ($file->move('uploads/mosque/', $filename)) {
            $image = '/uploads/mosque/' . $filename;
        }

        $mosque->user_id = $request->user_id;
        $mosque->name = $request->name;
        $mosque->scholar = $request->scholar;
        $mosque->image = $image;
        $mosque->location = $request->location;
        $mosque->lat = $request->lat;
        $mosque->lng = $request->lng;
        $mosque->description = $request->description;
        $mosque->save();
        return response()->json([
            'status' => true,
            'action' =>  "Mosque added",
        ]);
    }

    public function editMosque(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mosque_id' => 'required|exists:mosques,id',
            'name' => 'required',
            'scholar' => 'required',
            'location' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'description' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $mosque = Mosque::find($request->mosque_id);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/mosque/', $filename)) {
                $image = '/uploads/mosque/' . $filename;
            }
            $mosque->image = $image;
        }

        if ($request->has('name')) {
            $mosque->name = $request->name;
        }

        if ($request->has('scholar')) {
            $mosque->scholar = $request->scholar;
        }
        if ($request->has('location')) {
            $mosque->location = $request->location;
            $mosque->lat = $request->lat;
            $mosque->lng = $request->lng;
        }
        if ($request->has('description')) {
            $mosque->description = $request->description;
        }
        $mosque->save();
        return response()->json([
            'status' => true,
            'action' =>  'Mosque edit',
        ]);
    }

    public function mosqueStatus($user_id, $status)
    {
        $mosques = Mosque::where('user_id', $user_id)->where('status', $status)->paginate(12);
        return response()->json([
            'status' => true,
            'action' =>  "Mosques",
            'data' => $mosques
        ]);
    }

    public function detailMosque($id)
    {
        $mosque = Mosque::where('id', $id)->first();
        if ($mosque) {
            $prayer = Prayer::where('mosque_id', $mosque->id)->first();
            $mosque->prayer = $prayer;
            return response()->json([
                'status' => true,
                'action' =>  "Mosque",
                'data' => $mosque
            ]);
        }

        return response()->json([
            'status' => false,
            'action' =>  "No mosque found",
        ]);
    }

    public function addPrayer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mosque_id' => 'required|exists:mosques,id',
            'fajr' => 'required',
            'dhuhr' => 'required',
            'asr' => 'required',
            'maghrib' => 'required',
            'isha' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $prayer = new Prayer();
        $prayer->mosque_id = $request->mosque_id;
        $prayer->fajr = $request->fajr;
        $prayer->dhuhr = $request->dhuhr;
        $prayer->asr = $request->asr;
        $prayer->maghrib = $request->maghrib;
        $prayer->isha = $request->isha;
        $prayer->save();

        return response()->json([
            'status' => true,
            'action' =>  "Prayer time added",
        ]);
    }

    public function editPrayer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mosque_id' => 'required|exists:mosques,id',
            'fajr' => 'required',
            'dhuhr' => 'required',
            'asr' => 'required',
            'maghrib' => 'required',
            'isha' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $prayer = Prayer::where('mosque_id', $request->mosque_id)->first();

        if ($prayer) {
            $prayer->fajr = $request->fajr;
            $prayer->dhuhr = $request->dhuhr;
            $prayer->asr = $request->asr;
            $prayer->maghrib = $request->maghrib;
            $prayer->isha = $request->isha;
            $prayer->save();
            return response()->json([
                'status' => true,
                'action' =>  "Prayer time edit",
            ]);
        }



        return response()->json([
            'status' => false,
            'action' =>  "Prayer time not found",
        ]);
    }


    public function saveMosque($user_id, $mosque_id)
    {
        $user = User::find($user_id);
        $mosque = Mosque::find($mosque_id);
        if ($mosque && $user) {
            $check = SavedMosque::where('mosque_id', $mosque_id)->where('user_id', $user_id)->first();
            if ($check) {
                $check->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  "Mosque unsaved",
                ]);
            } else {
                $save = new SavedMosque();
                $save->user_id = $user_id;
                $save->mosque_id = $mosque_id;
                $save->save();
                return response()->json([
                    'status' => true,
                    'action' =>  "Mosque saved",
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'action' =>  "Mpsque or user not found",
        ]);
    }




    public function savedList($id)
    {
        $savedMosque = SavedMosque::where('user_id', $id)->pluck('mosque_id');
        $mosques = Mosque::whereIn('id', $savedMosque)->Paginate(10);

        foreach ($mosques as $item) {
            $item->is_saved = true;
        }
        return response()->json([
            'status' => true,
            'action' =>  "Saved List",
            'data' => $mosques
        ]);
    }

    public function delete($id)
    {
        $mosque = Mosque::find($id);
        if ($mosque) {
            $mosque->delete();

            return response()->json([
                'status' => true,
                'action' =>  "Mosque deleted",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "No Mosque found",
        ]);
    }


    public function prayerTiming(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'user_id' => 'required|exists:users,id',
            'device_id' => 'required',
            'lng' => 'required',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $userLat = $request->lat;
        $userLng = $request->lng;
        $radius = 10;
        $nearbyMosque = DB::table('mosques')
            ->select(
                'id',
                DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
            )
            ->where('status', 1)
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->first();


        $userTimezone = UserDevice::where('user_id', $request->user_id)->where('device_id', $request->device_id)->latest()->first();
        if ($userTimezone) {
            $currentTime = now()->timezone($userTimezone->timezone)->format('H:i');
        } else {
            $currentTime = now()->format('H:i');
        }



        if ($currentTime >= '00:00' && $currentTime < '07:00') {
            $nearestPrayer = 'Fajr';
            $nextPrayer = 'Dhuhr';
        } elseif ($currentTime >= '07:00' && $currentTime < '15:00') {
            $nearestPrayer = 'Dhuhr';
            $nextPrayer = 'Asr';
        } elseif ($currentTime >= '15:00' && $currentTime < '18:00') {
            $nearestPrayer = 'Asr';
            $nextPrayer = 'Maghrib';
        } elseif ($currentTime >= '18:00' && $currentTime < '19:30') {
            $nearestPrayer = 'Maghrib';
            $nextPrayer = 'Isha';
        } else {
            $nearestPrayer = 'Isha';
            $nextPrayer = 'Fajr';
        }



        $showTime = '';
        $nextPrayerTime = '';

        $timingObj = new stdClass();

        if ($nearbyMosque) {
            $prayerTiming = Prayer::where('mosque_id', $nearbyMosque->id)->first();
        }
        
        
        if ($nearbyMosque && $prayerTiming) {

            if ($nearestPrayer === 'Fajr') {
                $showTime = $prayerTiming->fajr;
                $nextPrayerTime = $prayerTiming->dhuhr;
            } elseif ($nearestPrayer === 'Dhuhr') {
                $showTime = $prayerTiming->dhuhr;
                $nextPrayerTime = $prayerTiming->asr;
            } elseif ($nearestPrayer === 'Asr') {
                $showTime = $prayerTiming->asr;
                $nextPrayerTime = $prayerTiming->maghrib;
            } elseif ($nearestPrayer === 'Maghrib') {
                $showTime = $prayerTiming->maghrib;
                $nextPrayerTime = $prayerTiming->isha;
            } else {
                $showTime = $prayerTiming->isha;
                $nextPrayerTime = $prayerTiming->fajr;
            }

            $timingObj = $prayerTiming;

        } else {
            $url = "http://api.aladhan.com/v1/calendar?latitude=$userLat&longitude=$userLng&method=2"; // Islamic Finder API URL

            $response = file_get_contents($url);

            if ($response !== false) {
                $prayerTimes = json_decode($response, true);
                // Process the $prayerTimes data to access prayer timings for different dates or today's date
                // For example, access today's prayer timings
                $todayPrayers = $prayerTimes['data'][date('j') - 1]['timings'];

                // Get current time
                // $currentTime = date('H:i');

                // Find the current prayer
                $currentPrayer = '';

                // foreach ($todayPrayers as $prayerName => $prayerTime) {
                //     if (strtotime($currentTime) <= strtotime($prayerTime)) {
                //         $currentPrayer = $prayerName;
                //         break;
                //     }
                // }

                // Display only the current prayer time and its name

                $showTime = $todayPrayers[$nearestPrayer];
                $nextPrayerTime = $todayPrayers[$nextPrayer];
                $convertedTimings = array_combine(
                    array_map('strtolower', array_keys($todayPrayers)), // Lowercase keys
                    array_values($todayPrayers) // Values remain unchanged
                );

                $timingObj = $convertedTimings;

                // echo "Current Prayer: $currentPrayer, Time: {$todayPrayers[$currentPrayer]}";
            } else {
                // Handle API request failure
                echo "Failed to fetch prayer times.";
            }
        }

        $vidoe = Video::all();



        $obj = new stdClass();
        $obj->prayer = $nearestPrayer;
        $obj->time = $showTime;

        $obj1 = new stdClass();
        $obj1->prayer = $nextPrayer;
        $obj1->time = $nextPrayerTime;
        return response()->json([
            'status' => true,
            'action' =>  "Prayer Timing",
            'data' => array(
                'prayer_timing' => $timingObj,
                'now_prayer' => $obj,
                'next_time' => $obj1,
                'video' => $vidoe
            )
        ]);
    }
}

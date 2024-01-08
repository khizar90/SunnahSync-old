<?php

namespace App\Http\Controllers\Api;

use App\Actions\FirebaseNotification;
use App\Actions\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Blocklist;
use App\Models\Comment;
use App\Models\Consultation;
use App\Models\Dua;
use App\Models\DuaCategory;
use App\Models\Faq;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Link;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Prayer;
use App\Models\ReportUser;
use App\Models\Review;
use App\Models\SavedDua;
use App\Models\SavedPost;
use App\Models\ScholarDetail;
use App\Models\Social;
use App\Models\Stream;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'to_id' => 'required|exists:users,id',
        ]);


        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        } else {

            $obj = new StdClass();

            $blocked = Blocklist::where('user_id', $request->user_id)->pluck('block_id');
            $blocked1 = Blocklist::where('block_id', $request->user_id)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);



            if ($request->user_id == $request->to_id) {
                $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $request->user_id)->first();
                $user->follower = Follow::where('to_id', $request->user_id)->count();
                $user->following = Follow::where('from_id', $request->user_id)->count();

                $post_count = Post::where('user_id', $request->user_id)->count();
                $user->post_count = $post_count;

                $posts = Post::where('user_id', $request->user_id)->latest()->paginate(12);


                $saved_user_ids = SavedPost::where('user_id', $request->user_id)->pluck('post_id');
                $saved_posts  = Post::whereIn('id', $saved_user_ids)->latest()->paginate(12);


                foreach ($posts as $post) {
                    $postby = User::where('id', $post->user_id)->select('id', 'name', 'image', 'location', 'verify', 'type')->first();
                    $comment = Comment::where('post_id', $post->id)->count();
                    $like = Like::where('post_id', $post->id)->count();
                    $likestatus = Like::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                    $saved = SavedPost::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                    if ($likestatus) {
                        $post->is_liked = true;
                    } else {
                        $post->is_liked = false;
                    }

                    if ($saved) {
                        $post->is_saved = true;
                    } else {
                        $post->is_saved = false;
                    }
                    $post->comments = $comment;
                    $post->likes = $like;
                    $post->user = $postby;
                }

                foreach ($saved_posts as $post) {
                    $postby = User::where('id', $post->user_id)->select('id', 'name', 'image', 'location', 'verify', 'type')->first();
                    $comment = Comment::where('post_id', $post->id)->count();
                    $like = Like::where('post_id', $post->id)->count();
                    $likestatus = Like::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                    $saved = SavedPost::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                    if ($likestatus) {
                        $post->is_liked = true;
                    } else {
                        $post->is_liked = false;
                    }

                    if ($saved) {
                        $post->is_saved = true;
                    } else {
                        $post->is_saved = false;
                    }
                    $post->comments = $comment;
                    $post->likes = $like;
                    $post->user = $postby;
                }
                $user->all_posts = $posts;



                $user->follow = false;
                $user->is_block = false;
                $user->saved_posts = $saved_posts;

                if ($user->type == 'Scholar') {
                    $experience = ScholarDetail::where('user_id', $request->to_id)->where('type', 'experience')->get();
                    $education = ScholarDetail::where('user_id', $request->to_id)->where('type', 'education')->get();
                    $certification = ScholarDetail::where('user_id', $request->to_id)->where('type', 'certification')->get();
                    $services = ScholarDetail::where('user_id', $request->to_id)->where('type', 'services')->get();
                    $user->experience = $experience;
                    $user->education = $education;
                    $user->certification = $certification;
                    $user->services = $services;
                } else {
                    $user->experience = [];
                    $user->education = [];
                    $user->certification = [];
                    $user->services = [];
                }
            } else {
                $follow = Follow::where('from_id', $request->user_id)->where('to_id', $request->to_id)->first();
                $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $request->to_id)->first();
                $user->follower = Follow::where('to_id', $request->to_id)->count();
                $user->following = Follow::where('from_id', $request->to_id)->count();


                $post_count = Post::where('user_id', $request->to_id)->count();
                $user->post_count = $post_count;

                $posts = Post::where('user_id', $request->to_id)->latest()->paginate(12);

                foreach ($posts as $post) {
                    $postby = User::where('id', $post->user_id)->select('id', 'name', 'image', 'location', 'verify', 'type')->first();
                    $comment = Comment::where('post_id', $post->id)->count();
                    $like = Like::where('post_id', $post->id)->count();
                    $likestatus = Like::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                    $saved = SavedPost::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                    if ($likestatus) {
                        $post->is_liked = true;
                    } else {
                        $post->is_liked = false;
                    }

                    if ($saved) {
                        $post->is_saved = true;
                    } else {
                        $post->is_saved = false;
                    }
                    $post->comments = $comment;
                    $post->likes = $like;
                    $post->user = $postby;
                }
                $user->all_posts = $posts;
                $user->saved_post = $obj;
                $block = Blocklist::where('user_id', $request->user_id)->Where('block_id', $request->to_id)->first();
                if ($block) {
                    $user->is_block = true;
                } else {
                    $user->is_block = false;
                }
                if ($user->type == 'Scholar') {
                    $experience = ScholarDetail::where('user_id', $request->to_id)->where('type', 'experience')->get();
                    $education = ScholarDetail::where('user_id', $request->to_id)->where('type', 'education')->get();
                    $certification = ScholarDetail::where('user_id', $request->to_id)->where('type', 'certification')->get();
                    $services = ScholarDetail::where('user_id', $request->to_id)->where('type', 'services')->get();
                    $user->experience = $experience;
                    $user->education = $education;
                    $user->certification = $certification;
                    $user->services = $services;
                } else {
                    $user->experience = [];
                    $user->education = [];
                    $user->certification = [];
                    $user->services = [];
                }


                if ($follow) {
                    $user->follow = true;
                } else {
                    $user->follow = false;
                }
            }
            return response()->json([
                'status' => true,
                'action' =>  'User profle',
                'data' => $user
            ]);
        }
    }

    public function serachUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'value' => 'required',
            'type' => 'required'

        ], [
            'value.required' => 'Please enter Keyword to search User'
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());
        $id = $request->user_id;
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $value = $request->value;

        $users = User::select('id', 'name', 'image', 'location', 'type', 'verify')
            ->where('name', 'like', "%$value%")->where('type', $request->type)
            ->whereNotIn('id', [$id])
            ->whereNotIn('id', function ($query) use ($id) {
                $query->select('user_id')
                    ->from('blocklists')
                    ->where('block_id', $id);
            })
            ->whereNotIn('id', function ($query) use ($id) {
                $query->select('block_id')
                    ->from('blocklists')
                    ->where('user_id', $id);
            })
            ->paginate(12);



        return response()->json([
            'status' => true,
            'action' =>  "Search Users",
            'data' => $users
        ]);
    }

    public function stream()
    {
        $streams = Stream::latest()->get();
        return response()->json([
            'status' => true,
            'action' =>  'Stream list',
            'data' => $streams
        ]);
    }


    public function follow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_id' => 'required|exists:users,id',
            'to_id' => 'required|exists:users,id',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $check = Follow::where('from_id', $request->from_id)->where('to_id', $request->to_id)->first();
        if ($check) {
            $check->delete();
            Notification::where('person_id', $request->from_id)->where('notification_type', 'social')->where('type', 'follow')->where('person_id', $request->from_id)->delete();

            return response()->json([
                'status' => true,
                'action' =>  'User Un Follow',
            ]);
        }
        $follow = new Follow();
        $follow->from_id = $request->from_id;
        $follow->to_id = $request->to_id;
        $follow->time = strtotime(date('Y-m-d H:i:s'));

        $follow->save();

        $from = User::find($request->from_id);
        $to = User::find($request->to_id);
        $post = 0;

        NewNotification::handle($to, $from->id, $post, 'Started Following you.', 'follow', 'social');
        $user = User::find($request->from_id);
        $tokens = UserDevice::where('user_id', $request->to_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
        FirebaseNotification::handle($tokens, $user->name . ' has started following you', 'New Follower', ['data_id' => $request->from_id, 'type' => 'follow']);

        return response()->json([
            'status' => true,
            'action' =>  'User Follow',
        ]);
    }

    public function following($id)
    {
        $user = User::find($id);
        if ($user) {

            $followingIds  = Follow::where('from_id', $id)
                ->whereNotIn('to_id', function ($query) use ($id) {
                    $query->select('block_id')
                        ->from('blocklists')
                        ->where('user_id', $id);
                })
                ->pluck('to_id');

            $followings = User::select('id', 'name', 'image', 'location', 'type', 'verify')->whereIn('id', $followingIds)
                ->whereNotIn('id', function ($query) use ($id) {
                    $query->select('user_id')
                        ->from('blocklists')
                        ->where('block_id', $id);
                })
                ->paginate(12);

            // $followings = Follow::where('from_id', $id)->get();
            // foreach ($followings as $following) {
            //     $user = User::select('id', 'name', 'image', 'location', 'type')->where('id', $following->to_id)->first();
            //     $following->to_user = $user;
            // }
            return response()->json([
                'status' => true,
                'action' =>  'Following',
                'data' => $followings
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }
    public function followers($id)
    {
        $user = User::find($id);
        if ($user) {
            $followerIds = Follow::where('to_id', $id)
                ->whereNotIn('from_id', function ($query) use ($id) {
                    $query->select('user_id')
                        ->from('blocklists')
                        ->where('block_id', $id);
                })
                ->pluck('from_id');

            $followers = User::select('id', 'name', 'image', 'location', 'type', 'verify')->whereIn('id', $followerIds)
                ->whereNotIn('id', function ($query) use ($id) {
                    $query->select('block_id')
                        ->from('blocklists')
                        ->where('user_id', $id);
                })
                ->paginate(12);




            return response()->json([
                'status' => true,
                'action' =>  'Followers',
                'data' => $followers
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }

    public function blockUnblock(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'block_id' => 'required|exists:users,id',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }


        $check = Blocklist::where('block_id', $request->block_id)->where('user_id',  $request->user_id)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status' => true,
                'action' => 'User unblocked'
            ]);
        } else {
            $block = new Blocklist;
            $block->block_id = $request->block_id;
            $block->user_id = $request->user_id;
            $block->save();
            return response()->json([
                'status' => true,
                'action' => 'User blocked'
            ]);
        }
    }

    public function blockList($id)
    {
        $block_ids = Blocklist::where('user_id', $id)->pluck('block_id');
        $blockUsers = User::select('id', 'name', 'image', 'type', 'location', 'verify')->whereIn('id', $block_ids)->paginate(12);
        foreach ($blockUsers as $block) {
            $block->block = true;
        }

        return response()->json([
            'status' => true,
            'action' =>  'Block list',
            'data' => $blockUsers
        ]);
    }

    public function home(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
        ]);
        if ($request->type == 'home') {
            $validator = Validator::make($request->all(), [
                'lat' => 'required',
                'lng' => 'required',
            ]);
        }
        $errorMessage = implode(', ', $validator->errors()->all());
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $obj = new stdClass();


        if ($request->type == 'home'  || $request->type == 'connect') {
            $user = User::find($request->user_id);
            $blocked = Blocklist::where('user_id', $request->user_id)->pluck('block_id');
            $blocked1 = Blocklist::where('block_id', $request->user_id)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);

            if ($user) {
                $topScholars = [];
                if ($request->type == 'home') {

                    if ($request->lat != 0 && $request->lng != 0) {

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
                        } elseif ($currentTime >= '07:00' && $currentTime < '15:00') {
                            $nearestPrayer = 'Dhuhr';
                        } elseif ($currentTime >= '15:00' && $currentTime < '18:00') {
                            $nearestPrayer = 'Asr';
                        } elseif ($currentTime >= '18:00' && $currentTime < '19:30') {
                            $nearestPrayer = 'Maghrib';
                        } else {
                            $nearestPrayer = 'Isha';
                        }

                        $showTime = '';


                        if ($nearbyMosque) {
                            $prayerTiming = Prayer::where('mosque_id', $nearbyMosque->id)->first();
                        }


                        if ($nearbyMosque && $prayerTiming) {
                            if ($nearestPrayer === 'Fajr') {
                                $showTime = $prayerTiming->fajr;
                            } elseif ($nearestPrayer === 'Dhuhr') {
                                $showTime = $prayerTiming->dhuhr;
                            } elseif ($nearestPrayer === 'Asr') {
                                $showTime = $prayerTiming->asr;
                            } elseif ($nearestPrayer === 'Maghrib') {
                                $showTime = $prayerTiming->maghrib;
                            } else {
                                $showTime = $prayerTiming->isha;
                            }
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
                                // echo "Current Prayer: $currentPrayer, Time: {$todayPrayers[$currentPrayer]}";
                            } else {
                                // Handle API request failure
                                echo "Failed to fetch prayer times.";
                            }
                        }


                        $obj = new stdClass();
                        $obj->prayer = $nearestPrayer;
                        $obj->time = $showTime;
                    }


                    $topScholars = User::select('id', 'name', 'image', 'location', 'verify', 'type')
                        ->where('type', 'Scholar')
                        ->where('verify', 1)
                        ->whereNotIn('id', $blocked)
                        ->withCount('followers')
                        ->orderByDesc('followers_count')
                        ->limit(20)
                        ->get();
                    $users = User::select('id')->where('type', 'Scholar')->where('verify', 1)->whereNotIn('id', $blocked)->pluck('id');
                    $posts = Post::with(['user:id,name,location,image,type,verify'])->whereIn('user_id', $users)
                        ->latest()
                        ->paginate(12);


                    foreach ($posts as $post) {
                        $comment = Comment::where('post_id', $post->id)->count();
                        $like = Like::where('post_id', $post->id)->count();
                        $likestatus = Like::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                        $saved = SavedPost::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                        if ($likestatus) {
                            $post->is_liked = true;
                        } else {
                            $post->is_liked = false;
                        }

                        if ($saved) {
                            $post->is_saved = true;
                        } else {
                            $post->is_saved = false;
                        }


                        if ($post->type == 'image') {
                            $imagePath = public_path($post->media);

                            // Get image size
                            list($width, $height) = getimagesize($imagePath);

                            // Output the dimensions
                            $post->size = $width / $height;
                        } else {
                            $post->size = 0.80;
                        }
                        $post->comments = $comment;
                        $post->likes = $like;
                    }
                }

                if ($request->type == 'connect') {
                    $users = User::select('id')->pluck('id');

                    $posts = Post::whereIn('user_id', $users)
                        ->with(['user' => function ($query) {
                            $query->select('id', 'name', 'location', 'image', 'type', 'verify');
                        }])
                        ->latest()
                        ->paginate(12);

                    foreach ($posts as $post) {
                        $comment = Comment::where('post_id', $post->id)->count();
                        $like = Like::where('post_id', $post->id)->count();
                        $likestatus = Like::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                        $saved = SavedPost::where('post_id', $post->id)->where('user_id', $request->user_id)->first();

                        if ($likestatus) {
                            $post->is_liked = true;
                        } else {
                            $post->is_liked = false;
                        }

                        if ($saved) {
                            $post->is_saved = true;
                        } else {
                            $post->is_saved = false;
                        }


                        if ($post->type == 'image') {
                            $imagePath = public_path($post->media);

                            // Get image size
                            list($width, $height) = getimagesize($imagePath);

                            // Output the dimensions
                            $post->size = $width / $height;
                        } else {
                            $post->size = 0.80;
                        }

                        $post->comments = $comment;
                        $post->likes = $like;
                    }
                }





                return response()->json([
                    'status' => true,
                    'action' =>  "Home",
                    'data' => array(
                        'topScholars' => $topScholars,
                        'post' => $posts,
                        'prayer' => $obj

                    )
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'action' =>  "User not found",
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'action' =>  "Invalid request type",
            ]);
        }
    }

    public function faqs()
    {
        $faqs  = Faq::all();
        return response()->json([
            'status' => true,
            'action' =>  "Faqs",
            'data' =>  $faqs,
        ]);
    }

    public function links()
    {
        $link  = Link::all();
        return response()->json([
            'status' => true,
            'action' =>  "Links",
            'data' =>  $link,
        ]);
    }


    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required',
            'user_id' => 'required|exists:users,id',
            'category' => 'required',
            'reason' => 'required',
            'type' => 'required',

        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $report = new  ReportUser();

        $report->report_id = $request->report_id;
        $report->user_id = $request->user_id;
        $report->category = $request->category ?: '';
        $report->type = $request->type;
        $report->reason = $request->reason;
        $report->save();
        return response()->json([
            'status' => true,
            'action' =>  'Report Send',
        ]);
    }

    public function notification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $notifications = Notification::where('user_id', $request->user_id)->where('notification_type', $request->type)->where('person_id', '!=', $request->user_id)->latest()->paginate(12);
        Notification::where('user_id', $request->user_id)->where('notification_type', $request->type)->where('is_read', 0)->update(['is_read' => 1]);
        foreach ($notifications as $index => $notif) {

            $person = User::find($notif->person_id);
            if ($person) {
                $notif->person_name = $person->name;
                $notif->person_image = $person->image;
            }
            else{
                $notif->person_name = '';
                $notif->person_image = '';
            }
            $checkDate = $notif->date;
            if ($index == 0 && !$request->page || $request->page == 1 && $index == 0) {
                $notif->first = true;
            } elseif ($index == 0 && $request->page && $request->page != 1) {
                $notisOld = Notification::select('date')->where('date', '!=', '')->where('notification_type', $request->type)->where('user_id',  $request->user_id)->limit(12)->skip(($request->page - 1) * 12)->orderBy('date', 'DESC')->get();
                $current = date_format(date_create($checkDate), 'Y-m-d');
                $previousDate = $notisOld[0]->date;
                $next = date_format(date_create($previousDate), 'Y-m-d');
                if ($current == $next)
                    $notif->first = false;
                else
                    $notif->first = true;
            } else {
                if ($index - 1 >= 0) {
                    $current = date_format(date_create($checkDate), 'Y-m-d');
                    $previousDate = $notifications[$index - 1]->date;
                    $next = date_format(date_create($previousDate), 'Y-m-d');
                    if ($current == $next)
                        $notif->first = false;
                    else
                        $notif->first = true;
                }
            }
            $dbCheck = date_format(date_create($checkDate), 'Y-m-d');
            $date = date_format(date_create($checkDate), 'D, d F');
            $tomorrow = date("Y-m-d", strtotime("-1 days"));
            $todayDate = date('Y-m-d');
            if ($dbCheck == $tomorrow)
                $notif->date = 'Yesterday';
            elseif ($dbCheck == $todayDate)
                $notif->date = 'Today';
            else
                $notif->date = $date;

            $post = Post::find($notif->data_id);
            if ($post) {
                $notif->data_media = $post->media;
                $notif->data_type = $post->type;
            } else {
                $notif->media = '';
                $notif->data_type = '';
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  'Notifications',
            'data' => $notifications,
        ]);
    }

    public function counter($id, $type)
    {
        $user = User::find($id);
        $obj = new stdClass();
        if ($user) {
            $message = Message::where('to', $user->id)->where('ticket_id', 0)->where('booking_id', 0)->where('is_read', 0)->count();
            $notification = Notification::where('user_id', $user->id)->where('notification_type', $type)->where('is_read', 0)->count();


            return response()->json([
                'status' => true,
                'action' =>  'Counter',
                'message_count' => $message,
                'notification_count' => $notification
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }
}

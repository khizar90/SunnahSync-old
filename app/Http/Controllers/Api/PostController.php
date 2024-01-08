<?php

namespace App\Http\Controllers\Api;

use App\Actions\FirebaseNotification;
use App\Actions\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Blocklist;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Report;
use App\Models\ReportPost;
use App\Models\ReportUser;
use App\Models\SavedPost;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;

class PostController extends Controller
{
    public function addPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caption' => 'required_without:media',
            'type' => 'required',
        ], [
            'caption.required_without' => 'Caption is required',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $user = User::find($request->user_id);
        if ($user) {
            $post = new Post();
            if ($request->hasFile('media')) {
                $file = $request->file('media');
                $extension = $file->getClientOriginalExtension();
                $mime = explode('/', $file->getClientMimeType());
                $filename = time() . '-' . uniqid() . '.' . $extension;
                if ($file->move('uploads/user/' . $user->id . '/posts/', $filename))
                    $image = '/uploads/user/' . $user->id . '/posts/' . $filename;

                $post->media = $image;
            }

            $thumbnailPath = '';
            // if($request->type == 'video'){
            //     $thumbnailPath = $this->getVideoThumbnail($filename,$user->id);
            // }


            $post->caption = $request->caption;
            $post->type = $request->type;
            $post->time = strtotime(date('Y-m-d H:i:s'));
            $post->user_id = $request->user_id;
            $post->thumbnail = $thumbnailPath;
            $post->save();
            return response()->json([
                'status' => true,
                'action' =>  "Post Added",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "User not found",
        ]);
    }

    public function getVideoThumbnail($videoFile, $user_id)
    {

        $ffmpeg = FFMpeg::create(
            array(
                'ffmpeg.binaries'  => "C:/ffmpeg/bin/ffmpeg.exe",
                'ffprobe.binaries' => "C:/ffmpeg/bin/ffprobe.exe",
            )
        );

        $video = $ffmpeg->open(public_path('uploads/user/' . $user_id . '/posts/' . $videoFile));

        $filename = time() . '-' . uniqid() . '.' .  '_thumbnail.jpg';
        $thumbnailPath = '/uploads/thumbnails/' . $filename;

        $video->frame(TimeCode::fromSeconds(2))
            ->save(public_path($thumbnailPath));

        return $thumbnailPath;
    }


    public function edit(Request $request)
    {
        $post = Post::find($request->post_id);
        if ($post) {
            if ($request->caption != null) {
                $post->caption = $request->caption;
                $post->save();
            }
            return response()->json([
                'status' => true,
                'action' =>  "Post edit",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "No post found",
        ]);
    }

    public function delete($id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            ReportUser::where('report_id', $id)->where('type', 'post')->delete();
            Notification::where('data_id', $id)->where('notification_type', 'social')->delete();
            Comment::where('post_id', $id)->delete();
            Like::where('post_id', $id)->delete();
            return response()->json([
                'status' => true,
                'action' =>  "Post deleted",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "No post found",
        ]);
    }


    public function like(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $post = Post::find($request->post_id);
        $other = User::find($request->user_id);
        $user = User::find($post->user_id);

        $check = Like::where('post_id', $request->post_id)->where('user_id', $request->user_id)->first();
        if ($check) {
            $check->delete();
            Notification::where('data_id', $request->post_id)->where('notification_type', 'social')->where('type', 'like')->where('person_id', $request->user_id)->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Post like remove',
            ]);
        }



        $like  = new Like();
        $like->post_id = $request->post_id;
        $like->user_id = $request->user_id;
        $like->time = strtotime(date('Y-m-d H:i:s'));
        $like->save();

        NewNotification::handle($user, $other->id, $post->id, 'has liked your post', 'like', 'social');

        if ($post->user_id != $request->user_id) {
            $tokens = UserDevice::where('user_id', $user->id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            FirebaseNotification::handle($tokens, $other->name . ' has Liked your Post', 'New Like', ['data_id' => $request->post_id, 'type' => 'post']);
        }

        return response()->json([
            'status' => true,
            'action' =>  'Post like',
        ]);
    }

    public function likeList($id)
    {
        $post = Post::find($id);
        if ($post) {


            $blocked = Blocklist::where('user_id', $id)->pluck('block_id');
            $blocked1 = Blocklist::where('block_id', $id)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);

            $likes = Like::where('post_id', $id)->whereNotIn('user_id', $blocked)->pluck('user_id');
            $users = User::select('id', 'name', 'image', 'location', 'verify', 'type')->whereIn('id', $likes)->Paginate(10);




            return response()->json([
                'status' => true,
                'action' =>  "Users",
                'data' => $users
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "No post found",
        ]);
    }

    public function comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
            'comment' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $post = Post::find($request->post_id);
        $other = User::find($post->user_id);
        $user = User::select('id', 'name', 'image', 'location', 'verify', 'type')->where('id', $request->user_id)->first();
        $comment  = new Comment();

        $comment->post_id = $request->post_id;
        $comment->user_id = $request->user_id;
        $comment->comment = $request->comment;
        $comment->time = strtotime(date('Y-m-d H:i:s'));

        $comment->save();
        NewNotification::handle($other, $user->id, $post->id, 'has comment on your post', 'comment', 'social');
        if ($post->user_id != $request->user_id) {
            $tokens = UserDevice::where('user_id', $other->id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            FirebaseNotification::handle($tokens, $user->name . ' commented on your Post', 'New Comment', ['data_id' => $request->post_id, 'type' => 'post']);
        }

        $comment->user = $user;
        return response()->json([
            'status' => true,
            'action' =>  'Comment added',
            'data' => $comment
        ]);
    }

    public function commentList($id)
    {
        $post = Post::find($id);
        if ($post) {
            $blocked = Blocklist::where('user_id', $id)->pluck('block_id');
            $blocked1 = Blocklist::where('block_id', $id)->pluck('user_id');
            $blocked = $blocked->merge($blocked1);

            $comments = Comment::where('post_id', $post->id)->whereNotIn('user_id', $blocked)->paginate(12);

            foreach ($comments as $comment) {
                $user = User::select('id', 'name', 'image', 'location', 'verify', 'type')->where('id', $comment->user_id)->first();
                $comment->user = $user;
            }
            return response()->json([
                'status' => true,
                'action' =>  "Comments",
                'data' => $comments
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "No post found",
        ]);
    }

    public function savePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $check = SavedPost::where('post_id', $request->post_id)->where('user_id', $request->user_id)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Post unsaved',
            ]);
        }
        $like  = new SavedPost();
        $like->post_id = $request->post_id;
        $like->user_id = $request->user_id;
        $like->save();

        return response()->json([
            'status' => true,
            'action' =>  'Post saved',
        ]);
    }

    public function detailPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $post = Post::find($request->post_id);
        if ($post) {
            $post->user =  User::select('id', 'name', 'image', 'location', 'verify', 'type')->where('id', $post->user_id)->first();

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


            return response()->json([
                'status' => true,
                'action' =>  'Post Detail',
                'data' => $post
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Post not found',
        ]);
    }
}

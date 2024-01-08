<?php

namespace App\Http\Controllers\Api;

use App\Actions\FirebaseNotification;
use App\Http\Controllers\Controller;
use App\Models\ConsultationBooking;
use App\Models\Message;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;
use stdClass;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        if ($request->ticket_id) {
            $validator = Validator::make($request->all(), [
                'ticket_id' => "required|integer|exists:reports,id",
                'from' => "required|integer|exists:users,id",
                'message' => 'required'
            ]);
        } elseif ($request->booking_id) {
            $validator = Validator::make($request->all(), [
                'booking_id' => "required|integer|exists:consultation_bookings,id",
                // 'from' => "required|integer|exists:users,id",
                'message' => 'required'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'to' => "required|integer|exists:users,id",
                'from' => "required|integer|exists:users,id",
                'message' => 'required'
            ]);
        }

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }



        if ($request->ticket_id) {
            $chat_message = new Message();
            $chat_message->from = $request->from;
            $chat_message->type = $request->type;
            $chat_message->ticket_id = $request->ticket_id;
            $chat_message->message = $request->message;
            $chat_message->time = strtotime(date('Y-m-d H:i:s'));
            $chat_message->save();
        } elseif ($request->booking_id) {
            $chat_message = new Message();

            if ($request->from) {
                $chat_message->from = $request->from;
                $booking = ConsultationBooking::find($request->booking_id);
                $user = User::find($booking->user_id);
            } else {
                $chat_message->to = $request->to;
                $user = User::find($request->to);
            }
            $chat_message->from = $request->from;
            $chat_message->type = $request->type;
            $chat_message->booking_id = $request->booking_id;
            $chat_message->message = $request->message;
            $chat_message->time = strtotime(date('Y-m-d H:i:s'));
            $chat_message->save();

            $find = Message::find($chat_message->id);

            $tokens = UserDevice::where('user_id', $user->id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            FirebaseNotification::handle($tokens, $user->name . ' has sent you a message in your consultation ', 'New Message', ['data_id' => $request->from, 'type' => 'consultation', 'user_type' => 'user']);

            $pusher = new Pusher('ec2175fecd86a44cbf83', 'dc0ea8f8a27a34389e7c', 1682390, [
                'cluster' => 'us2',
                'useTLS' => true,
            ]);
    
            $pusher->trigger($chat_message->booking_id, 'new-message', $chat_message);
            
        } else {
            $chat_message = new Message();
            $chat_message->from = $request->from;
            $chat_message->to = $request->to;
            $chat_message->type = $request->type;
            $chat_message->message = $request->message;
            $chat_message->time = strtotime(date('Y-m-d H:i:s'));
            $find = Message::where('from_to', $request->from . '-' . $request->to)->orWhere('from_to', $request->to . '-' . $request->from)->first();
            $channel = '';
            if ($find) {
                $channel = $find->from_to;
                $chat_message->from_to = $find->from_to;
                Message::where('from_to', $chat_message->from_to)->where('to', $request->from)->where('is_read', 0)->update(['is_read' => 1]);
            } else {
                $channel = '';
                $chat_message->from_to = $request->from . '-' . $request->to;
            }
            $chat_message->save();

            $find = Message::find($chat_message->id);
            $user = User::find($request->from);

            $tokens = UserDevice::where('user_id', $request->to)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            FirebaseNotification::handle($tokens, $user->name . ' has send you a message', 'New Message', ['data_id' => $request->from, 'type' => 'message', 'user_type' => 'user']);

            $pusher = new Pusher('ec2175fecd86a44cbf83', 'dc0ea8f8a27a34389e7c', 1682390, [
                'cluster' => 'us2',
                'useTLS' => true,
            ]);
    
            $pusher->trigger($chat_message->from_to, 'new-message', $chat_message);
        }

        




        return response()->json([
            'status' => true,
            'action' => "Message send",
            'data' => $chat_message
        ]);
    }


    public function conversation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => "required|integer|exists:users,id",
            'from' => "required|integer|exists:users,id",
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        Message::where('from', $request->to)->where('to', $request->from)->where('is_read', 0)->update(['is_read' => 1]);

        $messages = Message::where('ticket_id', 0)->where('from_to', $request->from . '-' . $request->to)->orWhere('from_to', $request->to . '-' . $request->from)->latest()->Paginate(25);
        $user = User::find($request->to);
        foreach ($messages as $message) {
            $message->user_name = $user->name;
            $message->user_image = $user->image;
        }

        return response()->json([
            'status' => true,
            'action' =>  'Conversation',
            'data' => $messages,
        ]);
    }
    public function inbox($user_id, Request $request)
    {

        $get = Message::select('from_to')->where('ticket_id', 0)->where('booking_id', 0)->where('from', $user_id)->orWhere('to', $user_id)->where('ticket_id', 0)->where('booking_id', 0)->groupBy('from_to')->pluck('from_to');
        $arr = [];
        foreach ($get as $item) {
            $message = Message::where('from_to', $item)->latest()->first();
            if ($message) {
                if ($message->from == $user_id) {
                    $user = User::select('id', 'name', 'location', 'image', 'type', 'verify')->where('id', $message->to)->first();
                }
                if ($message->to == $user_id) {
                    $user = User::select('id', 'name', 'location', 'image', 'type', 'verify')->where('id', $message->from)->first();;
                }
            }
            $unread_count = Message::where('from_to', $item)->where('to', $user_id)->where('is_read', 0)->count();
            $obj = new stdClass();
            $obj->message = $message->message;
            $obj->time = $message->time;
            $obj->user = $user;
            $obj->unread_count = $unread_count;
            $arr[] = $obj;
        }

        $sorted = collect($arr)->sortByDesc('time');

        // ---COMMENTED FOR FUTURE USE IF NEEDED FOR PAGINATION---
        // $sorted = $sorted->forPage($request->page, 20);

        $arr1 = [];
        $count = 0;
        foreach ($sorted as $item) {
            $arr1[] = $item;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Inbox',
            'data' => $arr1
        ]);
    }
}

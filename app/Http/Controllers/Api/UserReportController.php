<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UserReportController extends Controller
{
    public function reportCategory()
    {
        $category = ReportCategory::pluck('name');
        if ($category->count() > 0) {
            return response()->json([
                'status' => true,
                'action' => "Categories",
                'data' => $category,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "No Category Found",
            ]);
        }
    }

    public function addReport(Request $request)
    {
        $obj = new stdClass();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:report_categories,id',
            'message' => 'required'
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        } else {
            $report = new Report();
            $report->category_id = $request->category_id;
            $report->user_id = $request->user_id;
            $report->message = $request->message;
            $report->save();

            $message = new Message();
            $message->from = $request->user_id;
            $message->type = 'text';
            $message->ticket_id = $report->id;
            $message->message = $request->message;
            $message->time = strtotime(date('Y-m-d H:i:s'));
            $message->save();


            $defaultMessage = new Message();
            $defaultMessage->ticket_id = $report->id;
            $defaultMessage->to = $request->user_id;
            $defaultMessage->type = 'text';
            $defaultMessage->message = 'Hi,👋Thanks for your message. We ll get back to you within 24 hours.';
            $defaultMessage->time = strtotime(date('Y-m-d H:i:s'));
            $defaultMessage->save();


            $user = User::find($request->user_id);
            $cat = ReportCategory::find($request->category_id);
            // $karachiTime = Carbon::parse($report->created_at)->timezone('Asia/Karachi');
            // $mail_details = [
            //     'subject' => 'Express',
            //     'body' => $request->message,
            //     'user' => $user->name,
            //     'category' => $cat->name,
            //     'time' => $karachiTime->format('Y-m-d H:i:s')
            // ];

            // Mail::to('khzrkhan0000@gmail.com')->send(new \App\Mail\ReportCreated());

            // Mail::to('zrzunair10@gmail.com')->send(new ReportCreated($mail_details));

            return response()->json([
                'status' => true,
                'action' => "Report Added",
            ]);
        }
    }

    public function userReport($user_id, $status)
    {
        $reports = Report::where('user_id', $user_id)->where('status', $status)->orderBy('created_at', 'desc')->paginate(12);
        if ($reports->count() > 0) {
            foreach ($reports as $report) {
                $category = ReportCategory::select('name')->where('id', $report->category_id)->first();
                $report->category = $category;

                $createdAtDateTime = new DateTime($report->created_at);

                $formattedDate = $createdAtDateTime->format('d-n-Y H:i:s');

                $report->dateAndTime = $formattedDate;
            }
            return response()->json([
                'status' => true,
                'action' => "User Reports",
                'data' => $reports,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "No reports found",
            ]);
        }
    }

    public function closeTicket($report_id)
    {
        $obj = new stdClass();
        $report = Report::find($report_id);
        if ($report) {
            $report->status = 0;
            $report->save();
            return response()->json([
                'status' => true,
                'action' => "Report Close",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "No reports found",
            ]);
        }
    }

    public function sendMessage(Request $request)
    {
        $obj = new stdClass();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'report_id' => 'required|exists:reports,id',
            'type' => 'required',
            'message' => 'required_without:attachment'
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' => $errorMessage,
            ]);
        }

        $message = new Message();
        $message->to = $request->user_id;
        $message->ticket_id = $request->report_id;
        $message->type = $request->type;
        $message->message = $request->message;
        $message->time = strtotime(date('Y-m-d H:i:s'));
        $message->save();



        $message = Message::find($message->id);
        $user = User::find($request->user_id);

        $message->user_name = $user->name;
        $message->user_image = $user->image;

        return response()->json([
            'status' => true,
            'action' => "Message send",
            'data' => $message
        ]);
    }

    public function conversation($id)
    {
        $ticket = Report::find($id);
        if ($ticket) {
            $messages = Message::where('ticket_id', $id)->latest()->paginate(12);
            $user = User::find($ticket->user_id);
            $category = ReportCategory::find($ticket->category_id);
            foreach ($messages as $message) {
                $message->user_name = $user->name;
                $message->user_image = $user->image;
                $message->category = $category->name;
            }
            return response()->json([
                'status' => true,
                'action' => "Conversation",
                'data' => $messages,
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "No Report found",
        ]);


        // $channelValues = explode('-', $from_to);
        // $user_id = $channelValues[0];
        // $ticket_id = $channelValues[1];

        // foreach ($messages as $message) {
        //     $user =  User::where('id', $user_id)->first();
        //     $message->user_name = $user->name;
        //     $message->user_image = $user->image;
        //     $category = ReportCategory::select('name')->where('id', $ticket_id)->first();
        //     $message->category = $category->name;
        // }


    }

    public function list($user_id, $status)
    {
        $reports = Report::where('user_id', $user_id)->where('status', $status)->latest()->paginate(12);
        foreach ($reports as $report) {
            $category = ReportCategory::where('id', $report->category_id)->first();
            $report->category = $category->name;
        }
        return response()->json([
            'status' => true,
            'action' => "User Report",
            'data' => $reports,
        ]);
    }
}

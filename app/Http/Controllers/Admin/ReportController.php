<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;

class ReportController extends Controller
{
    public function getCategory()
    {
        $categories = ReportCategory::where('status',0)->get();
        return view('report.category', compact('categories'));
    }

    public function deleteCategory($id)
    {
        $category = ReportCategory::find($id);
        $category->status = 1;
        $category->save();
        return redirect()->back()->with('delete', 'Category  Deleted');
    }

    public function addCategory(Request $request)
    {
        $category = new ReportCategory();
        $category->name = $request->name;
        $category->save();
        return redirect()->back()->with('success', 'Category  Added Successfully');
    }


    public function report($status)
    {

        if ($status == 'active') {
            $reports = Report::where('status', 1)->latest()->get();

            foreach ($reports as $report) {
                $user = User::find($report->user_id);
                $category = ReportCategory::find($report->category_id);
                $report->user = $user;
                $report->category = $category;
            }
        } else {
            $reports = Report::where('status', 0)->get();

            foreach ($reports as $report) {
                $user = User::find($report->user_id);
                $category = ReportCategory::find($report->category_id);

                $report->user = $user;
                $report->category = $category;
            }
        }
        return view('report.index', compact('reports', 'status'));
    }

    public function messages($ticket_id)
    {
        // $ids = explode('-', $from_to);
        // $from = $ids[0]; 
        // $to = $ids[1];

        $conversation = Message::where('ticket_id', $ticket_id)
            ->orderBy('created_at', 'asc')
            ->get();

        $ticket = Report::find($ticket_id);


        $findUser = User::find($ticket->user_id);
        $cat = ReportCategory::find($ticket->category_id);
        // $channelName = $from_to;





        return view('report.show', compact('conversation', 'findUser', 'cat', 'ticket'));
    }

    public function closeReport($report_id)
    {
        $obj = new stdClass();
        $report = Report::find($report_id);
        if ($report) {
            $report->status = 0;
            $report->save();
            return redirect()->route('dashboard-report-report', 'active');
        }
    }


    public function sendMessage(Request $request)
    {
        $message = new Message();
        $message->ticket_id = $request->ticket_id;
        $message->to = $request->user_id;
        $message->message = $request->message;
        $message->type = 'text';
        $message->time = strtotime(date('Y-m-d H:i:s'));

        $message->save();
        return response()->json($message);
    }
}

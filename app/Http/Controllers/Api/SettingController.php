<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsultationCategory;
use App\Models\DonationCategory;
use App\Models\DonationCtaegory;
use App\Models\DuaCategory;
use App\Models\ReportCategory;
use Illuminate\Http\Request;
use stdClass;

class SettingController extends Controller
{
    public function splash(){
        $obj = new stdClass();
        $report = ReportCategory::select('id','name')->where('status',0)->get();
        $donation = DonationCategory::select('id','name')->where('status',0)->get();
        $consultation = ConsultationCategory::select('id','name')->where('status',0)->get();
        // $dua = DuaCategory::pluck('name');
        $obj->report_category = $report;
        $obj->donation_category = $donation;
        $obj->consultation = $consultation;
        // $obj->dua_category = $dua;
        return response()->json([
            'status' => true,
            'action' => "Splash",
            'data' => $obj,
        ]);
    }
}

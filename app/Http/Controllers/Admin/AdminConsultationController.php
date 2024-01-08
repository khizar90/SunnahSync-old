<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationCategory;
use Illuminate\Http\Request;

class AdminConsultationController extends Controller
{
    public function getCategory()
    {
        $categories = ConsultationCategory::where('status',0)->get();
        return view('consultation.category', compact('categories'));
    }

    public function deleteCategory($id)
    {
        $category = ConsultationCategory::find($id);
        $category->status =1;
        $category->save();
        return redirect()->back()->with('delete', 'Category  Deleted');
    }

    public function addCategory(Request $request)
    {
        $category = new ConsultationCategory();
        $category->name = $request->name;
        $category->save();
        return redirect()->back()->with('success', 'Category  Added Successfully');
    }

}

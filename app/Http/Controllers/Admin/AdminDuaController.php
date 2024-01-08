<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dua;
use App\Models\DuaCategory;
use App\Models\DuaSubCategory;
use App\Models\SavedDua;
use Illuminate\Http\Request;

class AdminDuaController extends Controller
{
    public function getCategory()
    {
        $categories = DuaCategory::all();
        return view('dua.category', compact('categories'));
    }

    public function deleteCategory($id)
    {
        $category = DuaCategory::find($id);
        $category->delete();
        return redirect()->back()->with('delete', 'Category  Deleted');
    }

    public function addCategory(Request $request)
    {
        $category = new DuaCategory();

      
        if($file = $request->file('image')){
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/dua/', $filename)) {
                $image = '/uploads/dua/' . $filename;
            }
        }
       


        $category->image = $image;
       
        $category->name = $request->name;
        $category->save();
        return redirect()->back()->with('success', 'Category  Added Successfully');
    }


    public function getSubCategory($id)
    {
        $categories = DuaCategory::find($id);
        $sub_categories = DuaSubCategory::where('category_id', $id)->get();
        foreach ($sub_categories as $sub_category) {
            $category = DuaCategory::find($sub_category->category_id);
            $sub_category->category = $category;
        }
        return view('dua.sub_category', compact('categories', 'sub_categories'));
    }

    public function addSubCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:dua_categories,id',
        ]);

        $category = new DuaSubCategory();
        $category->category_id = $request->category_id;
        $category->sub_category = $request->name;
        $category->save();
        return redirect()->back()->with('success', 'Category  Added Successfully');
    }



    public function list()
    {
        $duas =  Dua::latest()->get();
        foreach ($duas as $dua) {
            $category = DuaCategory::find($dua->category_id);
            $sub_category = DuaSubCategory::find($dua->sub_category_id);
            $dua->category = $category->name;
            $dua->sub_category = $sub_category->sub_category;
        }
        return view('dua.index', compact('duas'));
    }
    public function dua(Request $request)
    {
        $categories = DuaCategory::all();
        $sub_categories = DuaSubCategory::all();
        return view('Dua.create', compact('categories', 'sub_categories'));
    }
    public function create(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:dua_categories,id',
            'sub_category_id' => 'required|exists:dua_sub_categories,id',
            'images.*' => 'required',
        ]);



        foreach ($request->file('images') as $file) {
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/dua/', $filename)) {
                $imagePath = '/uploads/dua/' . $filename;
            }
            $dua = new Dua();
            $dua->category_id = $request->category_id;
            $dua->sub_category_id = $request->sub_category_id;
            $dua->image = $imagePath;
            $dua->save();
        }



        return redirect()->back()->with('success', 'Dua  Added Successfully');
    }

    public function delete($id)
    {
        $dua = Dua::find($id);
        if ($dua) {
            $dua->delete();
            return redirect()->back()->with('delete', 'Dua  Deleted');
        }
        return redirect()->back();
    }
}

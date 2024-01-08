<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class AdminVideoController extends Controller
{

    public function create(){
        return view('adminVideos.create');
    }
    public function store(Request $request){
        $validated = $request->validate([
            'type' => 'required',
            'media' => 'required'
        ]);

        $file = $request->file('media');
        $extension = $file->getClientOriginalExtension();
        $mime = explode('/', $file->getClientMimeType());
        $filename = time() . '-' . uniqid() . '.' . $extension;
        if ($file->move('uploads/Video/', $filename)) {
            $image = '/uploads/Video/' . $filename;
        }

        $category = new Video();
        $category->type = $request->type;
        $category->media = $image;
        $category->save();
        return redirect()->back()->with('success', 'video  Added Successfully');
    }
}

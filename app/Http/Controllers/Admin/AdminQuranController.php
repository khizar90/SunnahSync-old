<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quran;
use App\Models\Surah;
use Illuminate\Http\Request;

class AdminQuranController extends Controller
{
    public function createPara()
    {
        return view('quran.create');
    }

    public function storePara(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'arabic' => 'required',
            'pdf' => 'required',
        ]);
        $file = $request->file('pdf');
        $extension = $file->getClientOriginalExtension();
        $mime = explode('/', $file->getClientMimeType());
        $filename = time() . '-' . uniqid() . '.' . $extension;
        if ($file->move('uploads/quran/para/', $filename)) {
            $image = '/uploads/quran/para' . $filename;
        }

        $para =  new Quran();
        $para->name = $request->name;
        $para->arabic = $request->arabic;
        $para->media = $image;
        $para->save();

        return redirect()->back()->with('success', 'Para  Added Successfully');
    }


    public function createSurah()
    {
        return view('surah.create');
    }

    public function storeSurah(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'arabic' => 'required',
            'pdf' => 'required',
            'verse' => 'required',
            'type' => 'required',
        ]);
        $file = $request->file('pdf');
        $extension = $file->getClientOriginalExtension();
        $mime = explode('/', $file->getClientMimeType());
        $filename = time() . '-' . uniqid() . '.' . $extension;
        if ($file->move('uploads/surah/', $filename)) {
            $image = '/uploads/surah/' . $filename;
        }

        $para =  new Surah();
        $para->name = $request->name;
        $para->arabic = $request->arabic;
        $para->verse = $request->verse;
        $para->type = $request->type;
        $para->media = $image;
        $para->save();

        return redirect()->back()->with('success', 'Surah  Added Successfully');
    }
}

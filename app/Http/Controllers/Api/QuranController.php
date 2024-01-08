<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quran;
use App\Models\SavedPara;
use App\Models\SavedSurah;
use App\Models\Surah;
use Illuminate\Http\Request;

class QuranController extends Controller
{
    public function listPara($id)
    {
        $paras = Quran::paginate(12);
        foreach ($paras as $para) {
            $saved = SavedPara::where('user_id', $id)->where('quran_id', $para->id)->first();
            if ($saved) {
                $para->is_saved  = true;
            } else {
                $para->is_saved  = false;
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  'Para List',
            'data' => $paras
        ]);
    }

    public function listSurah($id)
    {
        $paras = Surah::paginate(12);
        foreach ($paras as $para) {
            $saved = SavedSurah::where('user_id', $id)->where('surah_id', $para->id)->first();
            if ($saved) {
                $para->is_saved  = true;
            } else {
                $para->is_saved  = false;
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  'Surah List',
            'data' => $paras
        ]);
    }

    public function savedPara($user_id, $para_id)
    {
        $find = SavedPara::where('user_id', $user_id)->where('quran_id', $para_id)->first();
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Para Unsaved',
            ]);
        }

        $para = new SavedPara();
        $para->user_id = $user_id;
        $para->quran_id = $para_id;
        $para->save();
        return response()->json([
            'status' => true,
            'action' =>  'Para Saved',
        ]);
    }
    public function savedSurah($user_id, $surah_id)
    {
        $find = SavedSurah::where('user_id', $user_id)->where('surah_id', $surah_id)->first();
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Surah Unsaved',
            ]);
        }

        $para = new SavedSurah();
        $para->user_id = $user_id;
        $para->surah_id = $surah_id;
        $para->save();
        return response()->json([
            'status' => true,
            'action' =>  'Surah Saved',
        ]);
    }

    public function savedListSurah($id)
    {

        $savedsurah = SavedSurah::where('user_id', $id)->pluck('surah_id');
        $surahs = Surah::whereIn('id', $savedsurah)->Paginate(12);

        foreach ($surahs as $item) {
            $item->is_saved = true;
        }


     
        return response()->json([
            'status' => true,
            'action' =>  'Surah List',
            'data' => $surahs
        ]);
    }

    public function savedListPara($id)
    {
        $savedsurah = SavedPara::where('user_id', $id)->pluck('quran_id');
        $surahs = Quran::whereIn('id', $savedsurah)->Paginate(12);

        foreach ($surahs as $item) {
            $item->is_saved = true;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Para List',
            'data' => $surahs
        ]);
    }
}

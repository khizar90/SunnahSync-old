<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dua;
use App\Models\DuaCategory;
use App\Models\DuaSubCategory;
use App\Models\SavedDua;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DuaController extends Controller
{
    public function duaCategory()
    {
        $categories = DuaCategory::all();
        foreach ($categories as $category) {
            $count = DuaSubCategory::where('category_id', $category->id)->count();
            $category->count = $count;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Categories list',
            'data' => $categories
        ]);
    }
    public function listDua($user_id, $id)
    {
        $category = DuaCategory::find($id);
        $user = User::find($user_id);
        if ($category && $user) {
            $duas = DuaSubCategory::where('category_id', $id)->get();

            foreach ($duas as $dua) {
                $check = SavedDua::where('user_id', $user_id)->where('sub_category_id', $dua->id)->first();
                if ($check) {
                    $dua->is_save = true;
                }
                else{
                    $dua->is_save = false;

                }
            }
            return response()->json([
                'status' => true,
                'action' =>  'Dua list',
                'data' => $duas
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' =>  'No Category found',
            ]);
        }
    }

    public function detailDua($user_id, $id)
    {

        $user = User::find($user_id);

        $sub_category_id = DuaSubCategory::find($id);

        if ($sub_category_id && $user) {
            $duas  = Dua::where('sub_category_id', $id)->get();

            $save = SavedDua::where('user_id',$id)->where('sub_category_id',$id)->first();
            if($save){
                $is_save=  true;
            }
            else{
                $is_save=  false;

            }
            return response()->json([
                'status' => true,
                'action' =>  'Dua',
                'data' => $duas,
                'is_save' => $is_save
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'No dua found',
        ]);
    }
    public function saveList($id)
    {
        $user = User::find($id);
        if ($user) {
            $saves = SavedDua::where('user_id', $id)->pluck('sub_category_id');
            $duas = DuaSubCategory::whereIn('id', $saves)->get();
            foreach ($duas as $dua) {
                $dua->is_save = true;
            }


            return response()->json([
                'status' => true,
                'action' =>  'Dua list',
                'data'  => $duas
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not found',
        ]);
    }
    public function saveDua($user_id, $dua_id)
    {

        $dua = Dua::find($dua_id);
        if ($dua) {
            $check = SavedDua::where('sub_category_id', $dua_id)->where('user_id', $user_id)->first();
            if ($check) {
                $check->delete();
                return response()->json([
                    'status' => true,
                    'action' =>  "Dua unsaved",
                ]);
            } else {
                $save = new SavedDua();
                $save->user_id = $user_id;
                $save->sub_category_id = $dua_id;
                $save->save();
                return response()->json([
                    'status' => true,
                    'action' =>  "Dua saved",
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'action' =>  "No dua found",
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Hadith;
use App\Models\SaveHadith;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HadithController extends Controller
{
    public function book(){
        $book = Book::all();
        return response()->json([
            'status' => true,
            'action' =>  'Books',
            'data' => $book
        ]);
    }

    public function bookCategory($id){

        $book = BookCategory::where('book_id',$id)->get();
        return response()->json([
            'status' => true,
            'action' =>  'Book Category',
            'data' => $book
        ]);
    }


    public function list(Request $request){
       
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
            'category_id' => 'required|exists:book_categories,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }


        $hadiths = Hadith::where('book_id',$request->book_id)->where('book_id',$request->category_id)->get();
        foreach($hadiths as $hadith){
            $save = SaveHadith::where('user_id',$request->user_id)->where('book_id',$request->book_id)->where('hadith_id',$hadith->id)->first();
            if($save){
                $hadith->is_save = true;
            }
            else{
                $hadith->is_save = false;
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  'Hadiths',
            'data' => $hadiths
        ]);
    }

    public function saveList(Request $request){
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $saveList = SaveHadith::where('user_id',$request->user_id)->where('book_id',$request->book_id)->get();
        $hadiths =[];
        foreach($saveList as $item){
            $item = Hadith::find($item->hadith_id);
            // $saveList->saveList = $item;
            $item->is_save = true;
            $hadiths[] =$item;
        }

        return response()->json([
            'status' => true,
            'action' =>  'Saved Hadith',
            'data' => $hadiths
        ]);
    }
    public function savedHadith(Request $request){
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
            'hadith_id' => 'required|exists:hadiths,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $check = SaveHadith::where('user_id',$request->user_id)->where('book_id',$request->book_id)->where('hadith_id',$request->hadith_id)->first();
        if($check){
            $check->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Hadith unsaved',
            ]);
        }
        
        $save = new SaveHadith();
        $save->user_id = $request->user_id;
        $save->book_id = $request->book_id;
        $save->hadith_id = $request->hadith_id;
        $save->save();
        return response()->json([
            'status' => true,
            'action' =>  'Hadith saved',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\ImageVerify;
use App\Models\Link;
use App\Models\Post;
use App\Models\Stream;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function users(){
        $users = User::latest()->paginate(20);
        return view('user.index' ,compact('users'));
    }
    public function verifyUsers(){
        $users = User::where('verify',2)->latest()->paginate(20);
        foreach($users as $user){
            $image = ImageVerify::where('user_id',$user->id)->latest()->first();
            $user->userimage = $image;
        }
        return view('user.verifyuser' ,compact('users'));
    }

    public function getVerify($user_id){
        $user = User::find($user_id);
        if($user){
            $user->verify = 1;
            $user->save();
            return redirect()->back()->with('success' , 'User Verify Successfully');
        }
        return redirect()->back();
    }

    public function faqs()
    {
        $faqs = Faq::all();

        return view('faq', compact('faqs'));
    }

    public function deleteFaq($id)
    {
        $faq  = Faq::find($id);
        $faq->delete();
        return redirect()->back()->with('delete', 'FAQ Deleted');
    }

    public function addFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        return redirect()->back()->with('success', 'FAQ  Added Successfully');
    }


    public function link()
    {
        $links = Link::all();
        return view('link', compact('links'));
    }

    public function addlink(Request $request){
        $link = new Link();

        $validated = $request->validate([
            'name' => 'required',
            'link' => 'required',
        ]);

        $link->name = $request->name;
        $link->link = $request->link;
        $link->save();
        return redirect()->back()->with('success', 'Link  Added Successfully');
    }

    public function editLink(Request $request,$id){

        $link = Link::find($id);
        if($link){
            $validated = $request->validate([
                'name' => 'required',
                'link' => 'required',
            ]);
    
            $link->name = $request->name;
            $link->link = $request->link;
            $link->save();
            return redirect()->back()->with('success', 'Link  Updated Successfully');
        }
        return redirect()->back(); 
    }

    public function deleteLink($id){
        $link = Link::find($id);
        if($link){
            
            $link->delete();
            return redirect()->back()->with('delete', 'Link  Deleted');
        }
        return redirect()->back(); 
    }




    public function stream(){
        return view('stream.create');
    }
    public function createStream(Request $request){
        $validated = $request->validate([
            'title' => 'required',
            'link' => 'required',
            'image' => 'required',
        ]);

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $mime = explode('/', $file->getClientMimeType());
        $filename = time() . '-' . uniqid() . '.' . $extension;
        if ($file->move('uploads/stream/', $filename)) {
            $image = '/uploads/stream/' . $filename;
        }


        $stream = new Stream();
        $stream->title = $request->title;
        $stream->image = $image;
        $stream->link = $request->link;
        $stream->save();
        return redirect()->back()->with('success', 'Stream  added Successfully');
    }

    public function listStream(){
        $streams = Stream::all();
        return view('stream.index' , compact('streams'));

    }

    public function deleteStream($id){
        $stream = Stream::find($id);
        if($stream){
            $stream->delete();
            return redirect()->back()->with('delete', 'Stream  Deleted');
        }
        return redirect()->back(); 
    }

    public function posts(){
        $posts = Post::latest()->Paginate(20);
        return view('post.index', compact('posts'));   
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Hadith;
use App\Models\HadithCategory;
use Illuminate\Http\Request;

class AdminHadithController extends Controller
{
    public function getCategory()
    {
        $categories = Book::all();
        return view('hadith.book', compact('categories'));
    }

    public function deleteCategory($id)
    {
        $category = Book::find($id);
        $category->delete();
        return redirect()->back()->with('delete', 'Book  Deleted');
    }

    public function addCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'image' => 'required|file|mimes:png,jpg'
        ]);

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $mime = explode('/', $file->getClientMimeType());
        $filename = time() . '-' . uniqid() . '.' . $extension;
        if ($file->move('uploads/hadith/', $filename)) {
            $image = '/uploads/hadith/' . $filename;
        }

        $category = new Book();
        $category->name = $request->name;
        $category->image = $image;
        $category->save();
        return redirect()->back()->with('success', 'Book  Added Successfully');
    }

    public function getSubCategory(Request $request, $id)
    {
        $books = Book::all();
        $categories = BookCategory::where('book_id', $id)->get();
        foreach ($categories as $category) {
            $book = Book::find($category->book_id);
            $category->book = $book->name;
        }
        return view('hadith.sub_category', compact('categories', 'books'));
    }

    public function addSubCategory(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'book_id' => 'required|exists:books,id',
        ]);

        $category = new BookCategory();
        $category->book_id = $request->book_id;
        $category->title = $request->title;
        $category->save();
        return redirect()->back()->with('success', 'Book Category  Added Successfully');
    }


    public function deleteSubCategory($id)
    {
        $category = BookCategory::find($id);
        $category->delete();
        return redirect()->back()->with('delete', 'Book Category  Deleted');
    }

    public function show()
    {
        $books = Book::all();
        $categories = BookCategory::all();
        return view('hadith.create', compact('categories', 'books'));
    }
    public function create(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'category_id' => 'required|exists:book_categories,id',
            'chapter' => 'required',
            'title' => 'required',
            'hadith' => 'required'
        ]);

        $hadith =  new Hadith();
        $hadith->book_id = $request->book_id;
        $hadith->category_id = $request->category_id;
        $hadith->chapter = $request->chapter;
        $hadith->title = $request->title;
        $hadith->hadith = $request->hadith;
        $hadith->save();

        return redirect()->back()->with('success', 'Hadith  Added Successfully');
    }

    public function list()
    {
        $hadiths = Hadith::paginate(50);
        foreach ($hadiths as $hadith) {
            $book = Book::find($hadith->book_id);
            $category = BookCategory::find($hadith->category_id);
            $hadith->book = $book;
            $hadith->category = $category;
        }
        return view('hadith.index', compact('hadiths'));
    }

    public function delete($id)
    {
        $hadith = Hadith::find($id);
        if ($hadith) {
            return redirect()->back()->with('delete', 'Hadith  Deleted');
        }
        return redirect()->back();
    }
}

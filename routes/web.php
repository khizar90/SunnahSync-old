<?php

use App\Http\Controllers\Admin\AdminConsultationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminDonationController;
use App\Http\Controllers\Admin\AdminDuaController;
use App\Http\Controllers\Admin\AdminHadithController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminMosqueController;
use App\Http\Controllers\Admin\AdminQuranController;
use App\Http\Controllers\Admin\AdminVideoController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Api\MosqueController;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/insert', function () {
//     $user = new Admin();
//     $user->name = 'Kevin Anderson';
//     $user->email = 'admin@admin.com';
//     $user->password = Hash::make('qweqwe');
//     $user->save();
// });


Route::get('/', function () {
    return view('layouts.base');
});
Route::group(['middleware' => 'guest'], function () {
    Route::get('/', function () {
        return view('auth-login');
    })->name('loginPage');

    Route::post('login', [AdminLoginController::class, 'login'])->name('login');
});
Route::prefix('dashboard')->middleware(['auth'])->name('dashboard-')->group(function () {
    Route::get('/', [AdminController::class, 'users'])->name('home');
    Route::get('users', [AdminController::class, 'users'])->name('users');
    Route::get('verify-users', [AdminController::class, 'verifyUsers'])->name('verify-users');
    Route::get('get-verify/{id}', [AdminController::class, 'getVerify'])->name('get-verify');



    Route::get('faqs', [AdminController::class, 'faqs'])->name('faqs');
    Route::post('add-faq', [AdminController::class, 'addFaq'])->name('add-faq');
    Route::get('delete-faq/{id}', [AdminController::class, 'deleteFaq'])->name('delete-faq');



    Route::prefix('report')->name('report-')->group(function () {
        Route::get('report/{status}', [ReportController::class, 'report'])->name('report');
        Route::get('close-report/{id}', [ReportController::class, 'closeReport'])->name('close-report');
        Route::get('messages/{from_to}', [ReportController::class, 'messages'])->name('messages');
        Route::post('send-message', [ReportController::class, 'sendMessage'])->name('send-message');
        Route::get('category', [ReportController::class, 'getCategory'])->name('category');
        Route::get('delete-category/{id}', [ReportController::class, 'deleteCategory'])->name('delete-category');
        Route::post('add-category', [ReportController::class, 'addCategory'])->name('add-category');
    });

    Route::prefix('donation')->name('donation-')->group(function () {
        Route::get('category', [AdminDonationController::class, 'getCategory'])->name('category');
        Route::get('delete-category/{id}', [AdminDonationController::class, 'deleteCategory'])->name('delete-category');
        Route::post('add-category', [AdminDonationController::class, 'addCategory'])->name('add-category');

        Route::get('all-donation', [AdminDonationController::class, 'donation'])->name('all-donation');
        Route::get('donation-request', [AdminDonationController::class, 'pending'])->name('donation-request');
        Route::get('donation-reject/{id}', [AdminDonationController::class, 'reject'])->name('donation-reject');
        Route::get('donation-approve/{id}', [AdminDonationController::class, 'approve'])->name('donation-approve');
    });

    Route::prefix('mosque')->name('mosque-')->group(function () {
        Route::get('list/{status}', [AdminMosqueController::class, 'list'])->name('lsit');
        Route::get('mosque-approve/{id}', [AdminMosqueController::class, 'approve'])->name('mosque-approve');
    });



    Route::prefix('stream')->name('stream-')->group(function () {
        Route::get('/list', [AdminController::class, 'listStream'])->name('list');
        Route::get('/', [AdminController::class, 'stream']);
        Route::post('/', [AdminController::class, 'createStream']);
        Route::get('/delete/{id}', [AdminController::class, 'deleteStream'])->name('delete');
    });


    Route::prefix('dua')->name('dua-')->group(function () {
        Route::get('category', [AdminDuaController::class, 'getCategory'])->name('category');
        Route::get('delete-category/{id}', [AdminDuaController::class, 'deleteCategory'])->name('delete-category');
        Route::post('add-category', [AdminDuaController::class, 'addCategory'])->name('add-category');

        Route::get('subCategory/{id}', [AdminDuaController::class, 'getSubCategory'])->name('subCategory');
        Route::post('subCategory', [AdminDuaController::class, 'addSubCategory'])->name('add-subCategory');
        Route::post('delete-subCategory/{id}', [AdminDuaController::class, 'deleteSubCategory'])->name('delete-subCategory');

        Route::get('add-dua', [AdminDuaController::class, 'dua'])->name('add');
        Route::post('add-dua', [AdminDuaController::class, 'create'])->name('add');
        Route::get('/', [AdminDuaController::class, 'list']);
        Route::get('delete/{id}', [AdminDuaController::class, 'delete'])->name('delete');

        

    });

    Route::prefix('hadith')->name('hadith-')->group(function () {
        Route::get('category', [AdminHadithController::class, 'getCategory'])->name('category');
        Route::get('delete-category/{id}', [AdminHadithController::class, 'deleteCategory'])->name('delete-category');
        Route::post('add-category', [AdminHadithController::class, 'addCategory'])->name('add-category');

        Route::get('sub-category/{id}', [AdminHadithController::class, 'getSubCategory'])->name('subcategory');
        Route::post('sub-category', [AdminHadithController::class, 'addSubCategory'])->name('add-subCatgeory');
        Route::get('delete-subcategory/{id}', [AdminHadithController::class, 'deleteSubCategory'])->name('delete-subcategory');

        Route::get('/', [AdminHadithController::class, 'list'])->name('list');
        Route::get('add', [AdminHadithController::class, 'show'])->name('add');        
        Route::post('add', [AdminHadithController::class, 'create']);        
        Route::get('hadith-delete/{id}', [AdminHadithController::class, 'delete'])->name('delete');        
    });

    Route::prefix('consultation')->name('consultation-')->group(function () {
        Route::get('category', [AdminConsultationController::class, 'getCategory'])->name('category');
        Route::get('delete-category/{id}', [AdminConsultationController::class, 'deleteCategory'])->name('delete-category');
        Route::post('add-category', [AdminConsultationController::class, 'addCategory'])->name('add-category');
    });


    Route::get('posts', [AdminController::class, 'posts'])->name('posts');
    Route::get('post-delete/{id}', [PanelController::class, 'deletePost'])->name('delete-post');

    Route::get('reported-posts', [AdminController::class, 'repotedPost'])->name('reported-post');


    Route::get('link', [AdminController::class, 'link'])->name('link');
    Route::get('delete-link/{id}', [AdminController::class, 'deleteLink'])->name('delete-link');
    Route::post('add-link', [AdminController::class, 'addLink'])->name('add-link');
    Route::post('edit-link/{id}', [AdminController::class, 'editLink'])->name('edit-link');


    Route::prefix('para')->name('para-')->group(function () {
        Route::get('add', [AdminQuranController::class, 'createPara'])->name('add');
        Route::post('add', [AdminQuranController::class, 'storePara']);
    });

    Route::prefix('surah')->name('surah-')->group(function () {
        Route::get('add', [AdminQuranController::class, 'createSurah'])->name('add');
        Route::post('add', [AdminQuranController::class, 'storeSurah']);
    });

    Route::prefix('video')->name('video-')->group(function () {
        Route::get('create', [AdminVideoController::class, 'create'])->name('create');
        Route::post('create', [AdminVideoController::class, 'store']);
    });




    Route::get('logout', [AdminLoginController::class, 'logout'])->name('logout');
});

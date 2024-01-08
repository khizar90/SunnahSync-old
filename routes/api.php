<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\DuaController;
use App\Http\Controllers\Api\HadithController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MosqueController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\QuranController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserReportController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('verify' ,[ AuthController::class, 'userVerify']);
Route::post('register/otpVerify' ,[ AuthController::class, 'otpVerify']);
Route::post('register' ,[ AuthController::class, 'register']);
Route::post('login' ,[ AuthController::class, 'login']);
Route::post('recover-verify' , [AuthController::class , 'recover']);
Route::post('recover-otp-verify' , [AuthController::class , 'recoverVerify']);
Route::post('new-password' , [AuthController::class , 'newPassword']);
Route::post('change-password/{id}' , [AuthController::class , 'changePassword']);
Route::post('edit-profile' , [AuthController::class , 'editProfile']);
Route::post('get-verify' , [AuthController::class , 'getVerify']);
Route::post('delete-account/{id}' , [AuthController::class , 'deleteAccount']);
Route::get('remove-image/{id}' , [AuthController::class , 'removeImage']);
Route::post('add-detail/' , [AuthController::class , 'addDetail']);
Route::post('get-detail' , [AuthController::class , 'getDetail']);
Route::get('delete-detail/{id}' , [AuthController::class , 'deleteDetail']);
Route::post('social-login' , [AuthController::class , 'socialLogin']);
Route::post('user-logout' ,[ AuthController::class, 'logout']);
Route::post('social' , [AuthController::class , 'socialConnect']);
Route::post('social-remove' , [AuthController::class , 'removeSocial']);
Route::get('social-accounts/{id}' , [AuthController::class , 'getSocial']);
Route::post('change-image' , [AuthController::class , 'editImage']);


Route::get('report-category' , [UserReportController::class , 'reportCategory']);
Route::post('add-report' , [UserReportController::class , 'addReport']);
Route::get('user-report/{user_id}/{status}' , [UserReportController::class , 'userReport']);
Route::get('close-ticket/{ticket_id}' , [UserReportController::class , 'closeTicket']);
Route::get('ticket/conversation/{id}' , [UserReportController::class , 'conversation']);
Route::get('report-list/{id}/{status}' , [UserReportController::class , 'list']);


Route::get('donation-list' , [DonationController::class , 'list']);
Route::get('donation-category/{id}' , [DonationController::class , 'donationCategory']);
Route::post('add-donation' , [DonationController::class , 'create']);
Route::post('donation-status/{id}' , [DonationController::class , 'pendingDonation']);
Route::get('donation-detail/{id}' , [DonationController::class , 'donationDetail']);
Route::post('donation-complete/{id}' , [DonationController::class , 'donationComplete']);
Route::post('donate-amount' , [DonationController::class , 'donateAmount']);
Route::post('donation-search' , [DonationController::class , 'serachDonation']);
Route::get('trending-donation' , [DonationController::class , 'trending']);
Route::get('all-donation' , [DonationController::class , 'allDonation']);


Route::post('list-mosque/{id}' , [MosqueController::class , 'list']);
Route::post('add-mosque' , [MosqueController::class , 'create']);
Route::post('edit-mosque' , [MosqueController::class , 'editMosque']);
Route::get('mosque-status/{user}/{status}' , [MosqueController::class , 'mosqueStatus']);
Route::get('mosque-detail/{id}' , [MosqueController::class , 'detailMosque']);
Route::post('add-prayer' , [MosqueController::class , 'addPrayer']);
Route::post('edit-prayer' , [MosqueController::class , 'editPrayer']);
Route::post('nearby-mosque' , [MosqueController::class , 'nearby']);
Route::get('all-mosque/{id}' , [MosqueController::class , 'allmosque']);
Route::get('save-mosque/{user_id}/{id}' , [MosqueController::class , 'saveMosque']);
Route::get('saved-mosque-list/{id}' , [MosqueController::class , 'savedList']);
Route::get('delete-mosque/{id}' , [MosqueController::class , 'delete']);
Route::post('prayer/timing' , [MosqueController::class , 'prayerTiming']);


Route::get('dua-categories' , [DuaController::class , 'duaCategory']);
Route::get('dua-list/{user}/{id}' , [DuaController::class , 'listDua']);
Route::get('dua-detail/{user_id}/{id}' , [DuaController::class , 'detailDua']);
Route::get('dua-saved-list/{id}' , [DuaController::class , 'saveList']);
Route::get('save-dua/{user_id}/{dua_id}' , [DuaController::class , 'saveDua']);



Route::get('hadith-books' , [HadithController::class , 'book']);
Route::get('hadith-book-category/{book_id}' , [HadithController::class , 'bookCategory']);
Route::post('hadiths' , [HadithController::class , 'list']);
Route::post('hadith-save-list' , [HadithController::class , 'saveList']);
Route::post('save-hadith' , [HadithController::class , 'savedHadith']);


Route::post('profile' , [UserController::class , 'profile']);
Route::get('stream' , [UserController::class , 'stream']);
Route::post('search-user' , [UserController::class, 'serachUser']);
Route::post('follow-user' , [UserController::class, 'follow']);
Route::get('followers/{id}' , [UserController::class, 'followers']);
Route::get('following/{id}' , [UserController::class, 'following']);
Route::post('block-user', [UserController::class, 'blockUnblock']);
Route::post('block-user', [UserController::class, 'blockUnblock']);
Route::get('block-list/{id}', [UserController::class, 'blockList']);
Route::post('home', [UserController::class, 'home']);
Route::get('faqs' , [UserController::class , 'faqs']);
Route::get('links' , [UserController::class , 'links']);
Route::post('notifications' , [UserController::class , 'notification']);
Route::post('report' , [UserController::class , 'report']);
Route::get('counter/{id}/{type}' , [UserController::class , 'counter']);





Route::post('add-post', [PostController::class, 'addPost']);
Route::get('delete-post/{id}', [PostController::class, 'delete']);
Route::post('edit-post', [PostController::class, 'edit']);
Route::post('like-post', [PostController::class, 'like']);
Route::get('like-list/{id}', [PostController::class, 'likeList']);
Route::post('comment', [PostController::class, 'comment']);
Route::get('comment-list/{id}', [PostController::class, 'commentList']);
Route::post('save-post', [PostController::class, 'savePost']);
Route::post('detail-post', [PostController::class, 'detailPost']);



Route::post('send-message' , [MessageController::class , 'sendMessage']);
Route::post('list-message' , [MessageController::class , 'conversation']);
Route::get('inbox/{id}' , [MessageController::class , 'inbox']);


Route::get('consultation-popular' , [ConsultationController::class , 'popular']);
Route::post('consultation-create' , [ConsultationController::class , 'create']);
Route::post('consultation-edit' , [ConsultationController::class , 'edit']);
Route::get('consultation-detail/{id}' , [ConsultationController::class , 'detail']);
Route::get('consultation-days/{id}' , [ConsultationController::class , 'availableDays']);
Route::get('consultation-day-status/{id}' , [ConsultationController::class , 'dayStatus']);
Route::post('consultation-add-slot' , [ConsultationController::class , 'addSlot']);
Route::get('consultation-delete-slot/{id}' , [ConsultationController::class , 'deleteSlot']);
Route::get('consultation-list-slot/{id}' , [ConsultationController::class , 'listSlot']);
Route::get('consultation' , [ConsultationController::class , 'list']);
Route::get('consultation-availability/{id}/{date}' , [ConsultationController::class , 'availability']);
Route::get('consultant-booking/{id}/{status}' , [ConsultationController::class , 'consultantBooking']);
Route::get('scholar-consultation/{id}' , [ConsultationController::class , 'scholarConsultation']);
Route::post('consultation-booking' , [ConsultationController::class , 'bookingCreate']);
Route::get('consultation-user-booking/{id}/{status}' , [ConsultationController::class , 'userBooking']);
Route::get('consultation-chnage-status/{id}/{status}' , [ConsultationController::class , 'chnageStatus']);
Route::post('consultation-search' , [ConsultationController::class , 'search']);
Route::get('consultation-category-filter/{id}' , [ConsultationController::class , 'categorySearch']);
Route::get('consultation-reviews/{id}' , [ConsultationController::class , 'listReviews']);
Route::get('booking-detail/{id}/{user_id}' , [ConsultationController::class , 'bookingDetail']);
Route::get('consultation-delete/{id}' , [ConsultationController::class , 'consultationDelete']);
Route::get('booking/conversation/{id}' , [ConsultationController::class , 'conversation']);
Route::post('edit-booking' , [ConsultationController::class , 'updateBooking']);
Route::post('reviews' , [ConsultationController::class , 'reviews']);


Route::post('payment-intent' , [PaymentController::class , 'craeteIntent']);

Route::get('list/paras/{id}' , [QuranController::class , 'listPara']);
Route::get('list/surah/{id}' , [QuranController::class , 'listSurah']);
Route::get('save/para/{id}/{para_id}' , [QuranController::class , 'savedPara']);
Route::get('save/surah/{id}/{surah_id}' , [QuranController::class , 'savedSurah']);
Route::get('save/list/para/{id}' , [QuranController::class , 'savedListPara']);
Route::get('save/list/surah/{id}' , [QuranController::class , 'savedListSurah']);


Route::post('user/wallet/company/create' , [WalletController::class , 'create']);
Route::get('user/wallet/detail/{id}' , [WalletController::class , 'detail']);
Route::post('user/wallet/payout' , [WalletController::class , 'payout']);



Route::get('splash' , [SettingController::class , 'splash']);








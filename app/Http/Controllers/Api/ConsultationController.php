<?php

namespace App\Http\Controllers\Api;

use App\Actions\FirebaseNotification;
use App\Actions\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationBooking;
use App\Models\ConsultationCategory;
use App\Models\ConsultationReview;
use App\Models\Day;
use App\Models\Days;
use App\Models\Message;
use App\Models\Review;
use App\Models\Slot;
use App\Models\User;
use App\Models\UserDevice;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{

    public function popular()
    {
        $popular = Consultation::orderBy('price', 'asc')->paginate(12);
        foreach ($popular as $item) {
            $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $item->consultor_id)->first();
            $category = ConsultationCategory::find($item->category_id);
            $item->category = $category->name;
            $item->user = $user;
        }
        return response()->json([
            'status' => true,
            'action' =>  "All Popular",
            'data' => $popular
        ]);
    }
    public function list()
    {

        $popular = Consultation::orderBy('price', 'asc')->take(2)->get();

        $all = Consultation::orderBy('price', 'asc')->paginate(12);

        foreach ($popular as $item) {
            $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $item->consultor_id)->first();
            $category = ConsultationCategory::find($item->category_id);
            $item->category = $category->name;
            $item->user = $user;
        }

        foreach ($all as $item) {
            $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $item->consultor_id)->first();
            $category = ConsultationCategory::find($item->category_id);
            $item->category = $category->name;
            $item->user = $user;
        }

        return response()->json([
            'status' => true,
            'action' =>  "Home",
            'data' => array(
                'popular' => $popular,
                'all' => $all,

            )
        ]);
    }

    public function detail($id)
    {
        $create = Consultation::find($id);
        if ($create) {
            $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $create->consultor_id)->first();
            $category = ConsultationCategory::find($create->category_id);
            $create->category = $category->name;
            $create->user = $user;
            $create->rating = 2.3;
            $create->reviews = 300;



            return response()->json([
                'status' => true,
                'action' => 'Consultation Detail',
                'data' => $create
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'No Consultation found',
        ]);
    }

    public function availability($id, $date)
    {

        $availability = Consultation::find($id);
        if ($availability) {
            $date = new DateTime($date);
            $dayName = $date->format('l');
            $find = Day::where('day', $dayName)->where('consultation_id', $id)->first();


            $slots = Slot::where('day_id', $find->id)->get();
            foreach ($slots as $slot) {
                $booked = ConsultationBooking::where('slot_id', $slot->id)->where('date', $date)->first();
                if ($booked) {
                    $slot->is_booked = true;
                } else {
                    $slot->is_booked = false;
                }
            }
            return response()->json([
                'status' => true,
                'action' =>  'Slots',
                'data' => $slots
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Invalid Consultaion ID',
        ]);
    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consultor_id' => 'required|exists:users,id',
            'title' => 'required',
            'category_id' => 'required|exists:consultation_categories,id',
            'price' => 'required|numeric',
            'description' => 'required',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $create = new Consultation();
        $create->consultor_id = $request->consultor_id;
        $create->title = $request->title;
        $create->category_id = $request->category_id;
        $create->price = $request->price;
        $create->description = $request->description;
        $create->save();

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($daysOfWeek as $dayOfWeek) {
            $day = new Day();
            $day->consultation_id = $create->id;
            $day->day = $dayOfWeek;
            $day->save();
        }



        return response()->json([
            'status' => true,
            'action' => 'Consultation Created',
        ]);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consultation_id' => 'required|exists:consultations,id',
            'title' => 'required',
            'category_id' => 'required|exists:consultation_categories,id',
            'price' => 'required|numeric',
            'description' => 'required',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $create = Consultation::find($request->consultation_id);
        $create->title = $request->title;
        $create->category_id = $request->category_id;
        $create->price = $request->price;
        $create->description = $request->description;
        $create->save();

        return response()->json([
            'status' => true,
            'action' => 'Consultation Edit',
        ]);
    }



    public function availableDays($id)
    {
        $create = Consultation::find($id);
        if ($create) {
            $days = Day::select('id', 'day', 'status')->where('consultation_id', $id)->get();
            foreach ($days as $day) {
                $slot = Slot::where('day_id', $day->id)->first();
                if ($slot) {
                    $day->slot_added = true;
                } else {
                    $day->slot_added = false;
                }
            }
            return response()->json([
                'status' => true,
                'action' => 'Days',
                'data' => $days
            ]);
        }

        return response()->json([
            'status' => false,
            'action' => 'No Consultation found',
        ]);
    }

    public function dayStatus($id)
    {
        $day = Day::find($id);
        if ($day) {
            $day->status == 0 ? $day->status =  1 : $day->status = 0;
            $day->save();
            return response()->json([
                'status' => true,
                'action' => 'Day status chnaged',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Invalid Day id',
        ]);
    }

    public function addSlot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'day_id' => 'required|exists:days,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_timestamp' => 'required',
            'end_timestamp' => 'required',

        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $slot = new Slot();
        $slot->day_id = $request->day_id;
        $slot->start_time = $request->start_time;
        $slot->end_time = $request->end_time;
        $slot->start_timestamp = $request->start_timestamp;
        $slot->end_timestamp = $request->end_timestamp;
        $slot->save();
        return response()->json([
            'status' => true,
            'action' =>  'Slot created successfully',
        ]);
    }
    public function deleteSlot($id)
    {
        $slot = Slot::find($id);
        if ($slot) {
            $slot->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Slot Deleted successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Invalid Slot ID',
        ]);
    }

    public function listSlot($day_id)
    {
        $slots = Slot::where('day_id', $day_id)->get();

        if ($slots) {
            return response()->json([
                'status' => true,
                'action' =>  'List Slot',
                'data' => $slots
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Invalid Slot ID',
        ]);
    }

    public function consultantBooking($con_id, $status)
    {


        $userBookings = ConsultationBooking::where('consultation_id', $con_id)->where('status', $status)->latest()->paginate(12);

        foreach ($userBookings as $booking) {
            $consultation = Consultation::where('id', $booking->consultation_id)->first();
            $slot = Slot::find($booking->slot_id);
            $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $booking->user_id)->first();
            $booking->consultation = $consultation;
            $booking->slot = $slot;
            $booking->user = $user;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Booking List',
            'data' => $userBookings
        ]);



        // $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $user_id)->first();
        // if ($user) {
        //     $scholarConultation = Consultation::where('user_id', $user_id)->pluck('id');

        //     $scholarBookings = ConsultationBooking::where('status', $status)->whereIn('consultation_id', $scholarConultation)->latest()->paginate(12);


        //     foreach ($scholarBookings as $booking) {
        //         $consultation = Consultation::where('id', $booking->consultation_id)->first();
        //         $slot = Slot::find($booking->slot_id);
        //         $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $booking->user_id)->first();
        //         $booking->consultation = $consultation;
        //         $booking->slot = $slot;
        //         $booking->user = $user;
        //     }



        //     return response()->json([
        //         'status' => true,
        //         'action' =>  'Booking List',
        //         'data' => $scholarBookings
        //     ]);
        // }
        // return response()->json([
        //     'status' => false,
        //     'action' =>  'User not Found',
        // ]);
    }

    public function userBooking($user_id, $status)
    {
        $user = User::find($user_id);

        if ($user) {
            $userBookings = ConsultationBooking::where('user_id', $user_id)->where('status', $status)->latest()->paginate(12);

            foreach ($userBookings as $booking) {
                $consultation = Consultation::where('id', $booking->consultation_id)->first();
                $slot = Slot::find($booking->slot_id);
                $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $consultation->consultor_id)->first();
                $booking->consultation = $consultation;
                $booking->slot = $slot;
                $booking->user = $user;
            }
            return response()->json([
                'status' => true,
                'action' =>  'Booking List',
                'data' => $userBookings
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not Found',
        ]);
    }

    public function bookingDetail($booking_id, $user_id)
    {
        $booking = ConsultationBooking::find($booking_id);
        if ($booking) {
            $consultation = Consultation::where('id', $booking->consultation_id)->first();
            $slot = Slot::find($booking->slot_id);
            if ($booking->user_id == $user_id) {
                $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $consultation->consultor_id)->first();
            } else {
                $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $booking->user_id)->first();
            }
            $reviews = ConsultationReview::where('consultation_id', $consultation->id)->where('booking_id', $booking->id)->count();
            $booking->consultation = $consultation;
            $booking->slot = $slot;
            $booking->user = $user;

            if ($reviews == 0) {
                $booking->is_review = false;
            } else {
                $booking->is_review = true;
            }

            return response()->json([
                'status' => true,
                'action' =>  'Booking Deatil',
                'data' => $booking
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' =>  'No Booking found',
            ]);
        }
    }

    public function scholarConsultation($id)
    {
        $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $id)->first();
        if ($user) {
            $consultaion = Consultation::select('id', 'title', 'description', 'price')->where('consultor_id', $id)->get();
            $consultaion_count = Consultation::where('consultor_id', $id)->count();
            $avg = 2.3;
            $reviews = 300;
            return response()->json([
                'status' => true,
                'action' =>  'Scholar Consulations',
                'data' => array(
                    'user' => $user,
                    'consultaion_count' => $consultaion_count,
                    'reviews' => $reviews,
                    'avg' => $avg,
                    'consultaion' => $consultaion,

                )
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not Found',
        ]);
    }

    public function bookingCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'consultation_id' => 'required|exists:consultations,id',
            'slot_id' => 'required|exists:slots,id',
            'date' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $booking = new ConsultationBooking();
        $booking->user_id = $request->user_id;
        $booking->consultation_id = $request->consultation_id;
        $booking->slot_id = $request->slot_id;
        $booking->date = $request->date;
        $booking->payment_id = $request->payment_id;

        $booking->save();

        $other = User::find($request->user_id);
        $consultaion = Consultation::find($request->consultation_id);
        $user = User::find($consultaion->consultor_id);
        NewNotification::handle($user, $other->id, $booking->id, 'has booked a consultation', 'booked', 'consultation');

        $tokens = UserDevice::where('user_id', $user->id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
        FirebaseNotification::handle($tokens, $other->name . ' has booked a consultation', 'Consultation Purchased', ['data_id' => $booking->id, 'type' => 'consultation', 'user_type' => 'consultant']);

        return response()->json([
            'status' => true,
            'action' =>  "Consultation Booked Successfully",
        ]);
    }

    public function chnageStatus($id, $status)
    {
        $booking = ConsultationBooking::find($id);
        if ($booking) {
            $booking->status = $status;
            $booking->save();

            // $other = User::find($request->user_id);
            // $consultaion = Consultation::find($request->consultation_id);
            // $user = User::find($consultaion->consultor_id);
            // NewNotification::handle($user, $other->id, $consultaion->id, 'has booked a consultation', 'booked', 'consultation');

            if ($status == 1) {
                $tokens = UserDevice::where('user_id', $booking->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                FirebaseNotification::handle($tokens, 'Consultant has started the consultation ', 'Consultation Started', ['data_id' => $booking->id, 'type' => 'consultation', 'user_type' => 'user']);
            }
            if ($status == 2) {
                $tokens = UserDevice::where('user_id', $booking->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                FirebaseNotification::handle($tokens, 'Consultant has canceled consultation ', 'Consultation Canceled', ['data_id' => $booking->id, 'type' => 'consultation', 'user_type' => 'user']);
            }
            if ($status == 3) {
                $tokens = UserDevice::where('user_id', $booking->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                FirebaseNotification::handle($tokens, 'Consultant has completed the consultation ', 'Consultation Ended', ['data_id' => $booking->id, 'type' => 'consultation', 'user_type' => 'user']);
            }
            return response()->json([
                'status' => true,
                'action' =>  "Status changed",
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "No Booking found",
        ]);
    }

    public function search(Request $request)
    {
        if ($request->keyword != null || $request->keyword != '') {
            $consultation = Consultation::where("title", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);

            foreach ($consultation as $item) {
                $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $item->consultor_id)->first();
                $category = ConsultationCategory::find($item->category_id);
                $item->category = $category->name;
                $item->user = $user;
            }
            return response()->json([
                'status' => true,
                'action' =>  "Consultation",
                'data' => $consultation
            ]);
        }
    }

    public function categorySearch($id)
    {
        $find = ConsultationCategory::find($id);
        if ($find) {
            $consultation = Consultation::where("category_id", $id)->latest()->paginate(12);

            foreach ($consultation as $item) {
                $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $item->consultor_id)->first();
                $category = ConsultationCategory::find($item->category_id);
                $item->category = $category->name;
                $item->user = $user;
            }
            return response()->json([
                'status' => true,
                'action' =>  "Consultation",
                'data' => $consultation
            ]);
        }

        return response()->json([
            'status' => false,
            'action' =>  "Category not found",
        ]);
    }

    public function listReviews($id)
    {
        $cons = Consultation::find($id);
        if ($cons) {
            $reviews = ConsultationReview::where('consultation_id', $id)->latest()->paginate(12);

            $averageRating = $reviews->avg('rating');
            foreach ($reviews as $item) {
                $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $item->id)->first();
                $item->user = $user;
            }
            return response()->json([
                'status' => true,
                'action' =>  "Reviews",
                'data' =>  $reviews,
                'avg' => $averageRating,


                
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  "Consultation  not found",
        ]);
    }

    public function consultationDelete($id)
    {
        $cons = Consultation::find($id);
        if ($cons) {
            $cons->delete();
            return response()->json([
                'status' => true,
                'action' =>  "Consultation  Deleted",
            ]);
        }

        return response()->json([
            'status' => false,
            'action' =>  "Consultation  not found",
        ]);
    }

    public function conversation($id)
    {
        $ticket = ConsultationBooking::find($id);
        if ($ticket) {
            $messages = Message::where('booking_id', $id)->latest()->paginate(12);
            $user = User::find($ticket->user_id);
            // $category = ReportCategory::find($ticket->category_id);
            foreach ($messages as $message) {
                $message->user_name = $user->name;
                $message->user_image = $user->image;
                // $message->category = $category->name;
            }
            if ($ticket->meeting_link) {
                $link = $ticket->meeting_link;
            } else {
                $link = '';
            }
            return response()->json([
                'status' => true,
                'action' => "Conversation",
                'data' => $messages,
                'meeting_link' => $link
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "No Booking found",
        ]);
    }

    public function updateBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:consultation_bookings,id',
            'meeting_link' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $booking = ConsultationBooking::find($request->booking_id);
        $booking->meeting_link = $request->meeting_link;
        $booking->save();
        return response()->json([
            'status' => true,
            'action' => "Meeting Created",
        ]);
    }


    public function reviews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:consultation_bookings,id',
            'consultation_id' => 'required|exists:consultations,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $review = new ConsultationReview();
        $review->user_id = $request->user_id;
        $review->consultation_id = $request->consultation_id;
        $review->booking_id = $request->booking_id;
        $review->rating = $request->rating;
        $review->description = $request->description ?: '';
        $review->save();

        $review = ConsultationReview::where('user_id', $request->user_id)->where('consultation_id', $request->consultation_id)->latest()->first();
        $user = User::select('id', 'image', 'location', 'name', 'about', 'type', 'verify')->where('id', $review->user_id)->first();
        $review->user = $user;

        $other = User::find($request->user_id);
        $consultaion = Consultation::find($request->consultation_id);
        $user = User::find($consultaion->consultor_id);
        NewNotification::handle($user, $other->id, $consultaion->id, 'has posted a review on your consultation ', 'review', 'consultation');


        return response()->json([
            'status' => true,
            'action' =>  "Review Send",
            'data' => $review
        ]);
    }
}

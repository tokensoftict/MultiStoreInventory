<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BookingReservation;
use App\Models\BookingReservationItem;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Room;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;


class BookingsController extends Controller
{

    public function index(){
        $data['title'] = 'Booking / Reservation List';
        $data['bookings'] = BookingReservation::with(['customer','status','user','booking_reservation_items'])->where(function($query){
            $status_id = Status::whereIn('name',['Active','Paid','Draft','Partial Payment'])->pluck('id');
            $query->orWhereIn('status_id',$status_id);
        })->get();
        return setPageContent('receptionist.bookings.list-reservation', $data);
    }


    public function create(){
        $data['title'] = 'New Booking / Reservation';
        $active = Status::where('name','Active')->first()->id;
        $data['rooms'] = Room::where('status_id',$active)->get();
        $data['booking'] = new BookingReservation();
        $data['customer'] = new Customer();
        $data['selected_rooms'] = [];
        $data['customers'] = Customer::where('id','>',1)->get();
        return setPageContent('receptionist.bookings.new-reservation', $data);
    }


    public function edit($id){
        $data['title'] = 'Update Booking / Reservation';
        $data['booking'] = BookingReservation::with(['customer','status','user','booking_reservation_items'])->findorfail($id);
        $data['customer'] =  $data['booking']->customer;
        $data['customers'] = Customer::where('id','>',1)->get();
        $data['selected_rooms'] = $data['booking']->booking_reservation_items()->pluck('room_id')->toArray();
        $data['rooms'] = Room::where(function($query) use (&$data){
            $active = Status::where('name','Active')->first()->id;
            $query->orwhere('status_id',$active)->orWhereIn('id',$data['selected_rooms']);
        })->get();
        return setPageContent('receptionist.bookings.new-reservation', $data);
    }


    public function store(Request $request){

        $request->validate(BookingReservation::$validation);

        $rooms_informations = Room::wherein('id',$request->room_id)->get();

        $to = Carbon::createFromDate($request->start_date);
        $from = Carbon::createFromDate($request->end_date);

        $noofDays = $to->diffInDays($from);


        $noofDays = ($noofDays == 0 ? 1 : $noofDays);

        $total =0;

        $reservationItems = [];

        if($request->customer_id != "_NEW"){
            $customer_id = $request->customer_id;
        }else{
            $customer_id = Customer::create($request->only(Customer::$fields))->id;
        }

        foreach ($rooms_informations as $rooms_information){
            $total += ($rooms_information->price * $noofDays);
            $reservationItems[] = new BookingReservationItem([
                'customer_id' => $customer_id,
                'user_id' => auth()->id(),
                'status_id' => Status::where('name','Draft')->first()->id,
                'room_id' => $rooms_information->id,
                'noOfDays' => $noofDays,
                'price' => $rooms_information->price,
                'total' => ($rooms_information->price * $noofDays),
                'booking_date' => $request->booking_date,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);
            $rooms_information->status_id = Status::where('name','Occupied')->first()->id;
            $rooms_information->update();
        }


        BookingReservation::create([
            'customer_id' => $customer_id,
            'user_id' => auth()->id(),
            'reservation_number'=>mt_rand(),
            'status_id' =>Status::where('name','Draft')->first()->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'booking_date' => $request->booking_date,
            'total' => $total,
            'total_paid' =>0,
            'customer_identity'=> " "
        ])->booking_reservation_items()->saveMany($reservationItems);

        return redirect()->route('bookings_and_reservation.index')->with('success','Operation Successful!');

    }

    public function show($id){
        $data['title'] = 'View Booking / Reservation';
        $data['booking']  =  BookingReservation::with(['booking_reservation_items'])->findorfail($id);
        return setPageContent('receptionist.bookings.view_booking', $data);
    }

    public function destroy($id){

       $book =  BookingReservation::with(['booking_reservation_items'])->findorfail($id);

       foreach ($book->booking_reservation_items as $item){
           $active = Status::where('name','Active')->first()->id;
           $item->room->status_id = $active;
           $item->room->update();
       }

       $status = Status::whereIn('name',['Paid','Checked-out','Partial Payment'])->pluck('id')->toArray();

       if(in_array($book->status_id,$status)){

           //delete payment
          $book->payments()->delete();
       }

       $book->delete();
        return redirect()->route('bookings_and_reservation.index')->with('success','Operation Successful!');
    }



    public function update(Request $request, $id){

        $book =  BookingReservation::with(['booking_reservation_items'])->findorfail($id);

        foreach ($book->booking_reservation_items as $item){
            $active = Status::where('name','Active')->first()->id;
            $item->room->status_id = $active;
            $item->room->update();
        }

        $status = Status::whereIn('name',['Paid','Checked-out','Partial Payment'])->pluck('id')->toArray();

        if(in_array($book->status_id,$status)){

            $book->payments()->delete();

        }


        $book->booking_reservation_items()->delete();

        //now les update the reservation


        $request->validate(BookingReservation::$validation);

        $rooms_informations = Room::wherein('id',$request->room_id)->get();



        $to = Carbon::createFromDate($request->start_date);
        $from = Carbon::createFromDate($request->end_date);

        $noofDays = $to->diffInDays($from);

        $noofDays = ($noofDays == 0 ? 1 : $noofDays);

        $total =0;

        $reservationItems = [];

        if($request->customer_id != "_NEW"){
            $customer_id = $request->customer_id;
        }else{
            $customer_id = Customer::create($request->only(Customer::$fields))->id;
        }

        foreach ($rooms_informations as $rooms_information){
            $total += ($rooms_information->price * $noofDays);
            $reservationItems[] = new BookingReservationItem([
                'customer_id' => $customer_id,
                'user_id' => auth()->id(),
                'status_id' => Status::where('name','Draft')->first()->id,
                'room_id' => $rooms_information->id,
                'noOfDays' => $noofDays,
                'price' => $rooms_information->price,
                'total' => ($rooms_information->price * $noofDays),
                'booking_date' => $request->booking_date,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);
            $rooms_information->status_id = Status::where('name','Occupied')->first()->id;
            $rooms_information->update();
        }



        $book->update([
            'customer_id' => $customer_id,
            'user_id' => auth()->id(),
            'status_id' =>Status::where('name','Draft')->first()->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'booking_date' => $request->booking_date,
            'total' => $total,
            'total_paid' =>0,
            'customer_identity'=> " "
        ]);
        $book ->booking_reservation_items()->saveMany($reservationItems);

        return redirect()->route('bookings_and_reservation.show',$book->id)->with('success','Booking has been update success');
    }


    public function check_out(Request $request, $id){

       $booking  =  BookingReservation::with(['booking_reservation_items'])->findorfail($id);

       if($booking->total_paid < $booking->total)
       {
           return redirect()->route('bookings_and_reservation.show',$booking->id)->with('error','Please complete booking payment before checking out guest.');
       }

       $checkout = status('Checked-out');
       $active = status('Active');
       $booking->status_id = $checkout;

        $booking->update();

      foreach ( $booking->booking_reservation_items as $item){
          $item->room->status_id = $active;
          $item->room->update();
      }

        return redirect()->route('bookings_and_reservation.show',$booking->id)->with('success','Operation successful');

    }

    public function make_payment(Request $request, $id)
    {
        $data['booking']  =  BookingReservation::with(['booking_reservation_items','customer'])->findorfail($id);

        if($request->getMethod() == "POST"){

            $data['booking']->sub_total = $request->total_amount_paid;

            $payment =  Payment::createPayment(['invoice'=>$data['booking'],'payment_info'=>$request, "type"=>"Reservation"]);

            unset($data['booking']->sub_total);

            if(isset($payment->id)) $data['booking']-> markAsPaid();

            if(isset($payment->id))  return redirect()->route('bookings_and_reservation.index')->with('success','Payment has been made Successfully');

            return redirect()->route('bookings_and_reservation.check_out',$id)->with('success','Payment has been made Successfully');
        }
        $data['title'] = 'Make Reservation Payment';
        $data['payments'] = PaymentMethod::where('status',1)->get();
        $data['banks'] = BankAccount::where('status',1)->get();
        return setPageContent('receptionist.bookings.make_payment', $data);
    }

    public function daily_report(Request $request){
        if($request->get('date') && $request->get('status_id')){
            $data['date']  = $request->get('date');
            $data['status_id'] = $request->get('status_id');
        }else{
            $data['date']  = date('Y-m-d');
            $data['status_id'] =  Status::whereIn('name',['Active','Paid','Draft','Partial Payment'])->pluck('id')->toArray();

        }
        $data['bookings'] = BookingReservation::with(['customer','status','user','booking_reservation_items'])->where('booking_date',$data['date'])->where(function($query) use(&$data){
            $query->orWhereIn('status_id',$data['status_id']);
        })->get();
        $data['statuses'] = Status::all();
        $data['title'] = 'Booking / Reservation List Daily Report';
        return setPageContent('receptionist.bookings.daily_report', $data);
    }

    public function monthly_report(Request $request){
        if($request->get('from') && $request->get('to') && $request->get('status_id')){
            $data['from']  = $request->get('from');
            $data['to']  = $request->get('to');
            $data['status_id'] = $request->get('status_id');
        }else{
            $data['from']  = date('Y-m-01');
            $data['to']  = date('Y-m-t');
            $data['status_id'] = Status::whereIn('name',['Active','Paid','Draft','Partial Payment'])->pluck('id')->toArray();
        }

        $data['bookings'] = BookingReservation::with(['customer','status','user','booking_reservation_items'])->whereBetween('booking_date',[$data['from'],$data['to']])->where(function($query) use(&$data){
            $query->orWhereIn('status_id',$data['status_id']);
        })->get();
        $data['statuses'] = Status::all();
        $data['title'] = 'Booking / Reservation List Monthly Report';
        return setPageContent('receptionist.bookings.monthly_report', $data);
    }

}

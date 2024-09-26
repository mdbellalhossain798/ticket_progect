<?php

namespace App\Http\Controllers;

use App\Models\TicketMst;
use App\Models\TicketDtl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketClosedMail;
use App\Mail\TicketOpenMail;
class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin_dashboard');
    }
    public function customerDashboard(){      
       
        return view('customer_dashboard');
    }

    public function getTickets(){
        $tickets = TicketMst::select('ticket_msts.id','ticket_msts.subject','ticket_msts.ticket_details','ticket_msts.ticket_type','ticket_msts.open_date','ticket_msts.closed_by','ticket_msts.closed_date', 'users.name as closed_by_name')
        ->leftJoin('users', 'ticket_msts.closed_by', '=', 'users.id')
        ->where('ticket_msts.user_id', auth()
        ->id())->orderBy('ticket_msts.id','desc')
        ->get();
        // dd($tickets);
            return response()->json([
                'tickets' => $tickets->map(function($ticket) {
                    return [
                        'id' => $ticket->id,
                        'ticket_subject' => $ticket->subject,
                        'ticket_details' => $ticket->ticket_details,
                        'status' => ucfirst($ticket->ticket_type),
                        'open_date' => $ticket->open_date ? date('d/m/Y h:i a', strtotime($ticket->open_date)) : null,
                        'closed_by' => $ticket->closed_by,
                        'closed_by_name' => $ticket->closed_by_name,
                        'closed_date' => $ticket->closed_date ? date('d/m/Y h:i a', strtotime($ticket->closed_date)) : null,
                    ];
                }),
            ]);
    }
    public function getAdminTickets(){
        $tickets = TicketMst::select('ticket_msts.id','ticket_msts.subject','ticket_msts.ticket_details','ticket_msts.ticket_type','ticket_msts.open_date','ticket_msts.user_id','ticket_msts.closed_by','ticket_msts.closed_date', 'users.name as closed_by_name','open_by.name as open_by')
        ->leftJoin('users', 'ticket_msts.closed_by', '=', 'users.id')
        ->leftJoin('users as open_by', 'ticket_msts.user_id', '=', 'open_by.id')
        // ->where('user_id', auth()->id())
        ->orderBy('ticket_msts.id','desc')
        ->get();
            return response()->json([
                'tickets' => $tickets->map(function($ticket) {
                    return [
                        'id' => $ticket->id,
                        'ticket_subject' => $ticket->subject,
                        'ticket_details' => $ticket->ticket_details,
                        'status' => ucfirst($ticket->ticket_type),
                        'open_date' => $ticket->open_date ? date('d/m/Y h:i a', strtotime($ticket->open_date)) : null,
                        'closed_by' => $ticket->closed_by_name,
                        'user_id' => $ticket->open_by,
                        'closed_date' => $ticket->closed_date ? date('d/m/Y h:i a', strtotime($ticket->closed_date)) : null,
                    ];
                }),
            ]);
    }
    public function ticketComment($id){
        // dd($id);
        $tickets = TicketMst::findOrFail($id);
        $reply_name=$tickets->user;
       
        $tickets_comment=$tickets->details()->orderBy('created_at','asc')->get();
       
            return response()->json([
                'tickets_comment' => $tickets_comment->map(function($ticket_cmt) {
                    return [
                        'id' => $ticket_cmt->id,
                        'reply_details' => $ticket_cmt->reply_details,
                        'reply_by' => $ticket_cmt->reply_by,
                        'created_at' => $ticket_cmt->created_at ? date('d/m/Y h:i a', strtotime($ticket_cmt->created_at)) : null,                       
                    ];
                }),
                'reply_name'=> $reply_name->name
            ]);
    }

    public function saveTicket(Request $request){
        $request->validate([
            'ticket_subject' => 'required|string|max:255',
            'ticket_details' => 'required|string',
        ]);
        DB::beginTransaction();
        try {
            $data=[
                'subject'=>$request->ticket_subject,
                'ticket_details'=>$request->ticket_details,
                'ticket_type'=>'OPEN',
                'user_id'=>Auth::id(),
                'open_date'=>now()
            ];
            $ticket = TicketMst::create($data);
            
            $adminEmail = User::where('user_type', 'admin')->first()->email;
            Mail::to($adminEmail)->send(new TicketOpenMail($ticket));
            DB::commit();
            return response()->json(['message' => 'Save successful!']);
        } catch (\Throwable $th) {            
            DB::rollBack();          
            return response()->json(['error' => 'Failed to save reply!'], 500);
        }
       
        // dd($request->all());
    }
    public function deleteTicket(Request $request){
        $request->validate([
            'ticket_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data=[               
                'ticket_type'=>'CLOSED',
                'closed_by'=>Auth::id(),
                'closed_date'=>now()
            ];
            $ticket = TicketMst::where('id', $request->ticket_id)->firstOrFail();
            $ticket->update($data);
             // Send email notification after closing the ticket
            Mail::to($ticket->user->email)->send(new TicketClosedMail($ticket));
            DB::commit();
            return response()->json(['message' => 'Closed successful!']);
        } catch (\Throwable $th) {            
            DB::rollBack();
            return response()->json(['error' => 'Failed to Close !'], 500);
        }
       
        // dd($request->all());
    }
    public function sendRrepy(Request $request){
        $request->validate([
            'reply' => 'required|string',
            'ticket_mst_id' => 'required|exists:ticket_msts,id', 
           
        ]);
        DB::beginTransaction();
        // try {
            $data=[
                'reply_details'=>$request->reply,
                'ticket_mst_id'=>$request->ticket_mst_id,
                'reply_by'=>Auth::id(),
                'created_at'=>now()
            ];
           $reply_data= TicketDtl::create($data);         
            DB::commit();
            return response()->json(['message' => 'Save successful!','ticket_id'=>$reply_data->id]);
        // } catch (\Throwable $th) {
            
        //     DB::rollBack();
        //     return response()->json(['error' => 'Failed to save reply!'], 500);
        // }
       
        // dd($request->all());
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TicketMst;
use App\Models\TicketDtl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
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
        $tickets = TicketMst::select('id','subject','ticket_details','ticket_type','open_date','closed_by','closed_date')->where('user_id', auth()->id())->get();
            return response()->json([
                'tickets' => $tickets->map(function($ticket) {
                    return [
                        'id' => $ticket->id,
                        'ticket_subject' => $ticket->subject,
                        'ticket_details' => $ticket->ticket_details,
                        'status' => ucfirst($ticket->ticket_type),
                        'open_date' => $ticket->open_date ? date('d/m/Y', strtotime($ticket->open_date)) : null,
                        'closed_by' => $ticket->closed_by,
                        'closed_date' => $ticket->closed_date ? date('d/m/Y', strtotime($ticket->closed_date)) : null,
                    ];
                }),
            ]);
    }
    public function ticketComment($id){
        $tickets = TicketMst::findOrFail($id);
        $tickets_comment=$tickets->details;
       
            return response()->json([
                'tickets_comment' => $tickets_comment->map(function($ticket_cmt) {
                    return [
                        'id' => $ticket_cmt->id,
                        'reply_details' => $ticket_cmt->reply_details,
                        'reply_by' => $ticket_cmt->reply_by,
                        'created_at' => $ticket_cmt->created_at ? date('d/m/Y', strtotime($ticket_cmt->created_at)) : null,                       
                    ];
                }),
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
            TicketMst::insert($data);
            DB::commit();
            return redirect()->back()->with('message', 'Save successful!');
        } catch (\Throwable $th) {
            
            DB::rollBack();
            return response()->json(['error' => 'Failed to save reply!'], 500);
        }
       
        // dd($request->all());
    }
    public function sendRrepy(Request $request){
        $request->validate([
            'reply' => 'required|string',
            'ticket_mst_id' => 'required|exists:ticket_msts,id', 
           
        ]);
        DB::beginTransaction();
        try {
            $data=[
                'reply_details'=>$request->reply,
                'ticket_mst_id'=>$request->ticket_mst_id,
                'reply_by'=>Auth::id(),
                'created_at'=>now()
            ];
           
            TicketDtl::insert($data);
            DB::commit();
            return redirect()->back()->with('message', 'Save successful!');
        } catch (\Throwable $th) {
            
            DB::rollBack();
            return response()->json(['error' => 'Failed to save reply!'], 500);
        }
       
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

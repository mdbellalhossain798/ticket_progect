<?php

namespace App\Http\Controllers;

use App\Models\TicketMst;
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

    public function saveTicket(Request $request){
        DB::beginTransaction();
        try {
            $data=[
                'subject'=>$request->ticket_subject,
                'ticket_details'=>$request->ticket_details,
                'ticket_type'=>'customer',
                'user_id'=>Auth::id(),
                'open_date'=>now()
            ];
            TicketMst::insert($data);
            DB::commit();
            return redirect()->back()->with('message', 'Save successful!');
        } catch (\Throwable $th) {
            
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to save.']);
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

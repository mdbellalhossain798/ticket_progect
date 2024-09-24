<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
          $user = Auth::user(); // Get the authenticated user

        // Redirect based on user type
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard'); // Change to your admin dashboard route
        } elseif ($user->user_type === 'customer') {
            return redirect()->route('customer.dashboard'); // Change to your customer dashboard route
        }

        // Optional: Handle other user types or show a default view
        return view('home'); // Default view if no specific user type found
    }
        // return view('home');
   
}

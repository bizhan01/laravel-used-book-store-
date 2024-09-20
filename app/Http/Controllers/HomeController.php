<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Advertisement;
use App\Book;
use DB;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
      $advertisements= Advertisement::latest()->get();
      $books = Book::latest()->get();
      $latestBooks = Book ::latest()
        ->orderByRaw('(id)desc LIMIT 6')
        ->get();
      return view('home', compact('advertisements', 'books', 'latestBooks'));
    }

}

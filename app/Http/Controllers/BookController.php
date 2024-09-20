<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Book;
use DB;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(){
      $books = Book::latest()->get();
      return view('book.addBook', compact('books',));
    }

    public function newBooks(){
      $books = Book::where('type', 1)->latest()->get();
      return view('book.newBooks', compact('books',));
    }

    public function usedBooks(){
      $books = Book::where('type', 0)->latest()->get();
      return view('book.usedBooks', compact('books',));
    }


    public function inventory()
      {
        // $products = Product::latest()->get();
        $products = DB::table('sell_items as pr')
              ->rightJoin('books as sup', 'sup.id', 'pr.product_id')
              ->select(["product_id as product_id", DB::raw("SUM(product_qty) as buy_tot"), 'sup.*'])
              ->groupBy('sup.id')
              ->get();

        return view('book.inventory', [  'products' => $products]);
      }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
          $this->validate(request(),[
            'book_name'=>'required',
            'author'=>'required',
            'edition'=>'nullable',
            'publisher'=>'nullable',
            'publish_date'=>'nullable',
            'ISBN'=>'nullable',
            'category'=>'required',
            'type'=>'required',
            'quantity'=>'required',
            'price'=>'required',
            'image' => 'image|mimes:jpeg,jpeg,png,jpg,gif|max:1999'
        ]);
        if($image = $request->file('image')){
          $new_name =rand() . '.' . $image-> getClientOriginalExtension();
          $image -> move(public_path("UploadedImages/books"), $new_name);
        }
        else {
          $new_name = 'logo.png';
        }
          Book::create([
              'user_id' => Auth::user()->id,
              'book_name' => request('book_name'),
              'author' => request('author'),
              'edition' => request('edition'),
              'publisher' => request('publisher'),
              'publish_date' => request('publish_date'),
              'ISBN' => request('ISBN'),
              'category' => request('category'),
              'type' => request('type'),
              'quantity' => request('quantity'),
              'price' => request('price'),
              'image' => $new_name,
              'created_at'=>carbon::now(),
              'updated_at'=>carbon::now(),

            ]);
            try {
             session()->flash('success', 'عملیات موافقانه اجرا شد ');
             return back();
             } catch(Exception $ex) {
             session()->flash('failed', 'خطا! دوباره کوشش کنید');
             return back();
           }
    }

          public function details($id)
          {
            $book = DB::select('select * from books where id = ?',[$id]);
            return view('book.details',['book'=>$book]);
        }



        public function show($id) {
         $book = DB::select('select * from books where id = ?',[$id]);
         return view('book.editBook',['book'=>$book]);
      }



      public function edit(Request $request,$id) {
         $user_id = $request->input('user_id');
         $book_name = $request->input('book_name');
         $author = $request->input('author');
         $edition = $request->input('edition');
         $publisher = $request->input('publisher');
         $publish_date = $request->input('publish_date');
         $ISBN = $request->input('ISBN');
         $category = $request->input('category');
         $type = $request->input('type');
         $quantity = $request->input('quantity');
         $price = $request->input('price');

         if($image = $request->file('image')){
           $new_name =rand() . '.' . $image-> getClientOriginalExtension();
           $image -> move("UploadedImages/books", $new_name);
         }
         else {
           $new_name = $request->input('image');
         };

         DB::update('update books set user_id = ? where id = ?',[$user_id, $id]);
         DB::update('update books set book_name = ? where id = ?',[$book_name, $id]);
         DB::update('update books set author = ? where id = ?',[$author, $id]);
         DB::update('update books set edition = ? where id = ?',[$edition, $id]);
         DB::update('update books set publisher = ? where id = ?',[$publisher, $id]);
         DB::update('update books set publish_date = ? where id = ?',[$publish_date, $id]);
         DB::update('update books set ISBN = ? where id = ?',[$ISBN, $id]);
         DB::update('update books set category = ? where id = ?',[$category, $id]);
         DB::update('update books set type = ? where id = ?',[$type, $id]);
         DB::update('update books set quantity = ? where id = ?',[$quantity, $id]);
         DB::update('update books set price = ? where id = ?',[$price, $id]);
         DB::update('update books set image = ? where id = ?',[$new_name, $id]);
         return redirect('/profile');
      }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
     public function destroy($id) {
     DB::delete('delete from books where id = ?',[$id]);
     return back();
  }
}

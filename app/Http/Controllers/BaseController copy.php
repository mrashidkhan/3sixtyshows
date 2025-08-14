<?php

namespace App\Http\Controllers;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;




class BaseController extends Controller
{
    public function home(){
        $products=Product::get();
        $new_products=Product::limit(6)->latest()->get();
        return view('front.home',compact('products','new_products'));
    }

    public function shop(){
           $products=Product::get();
        return view('front.shop', compact('products'));
    }

    public function aboutus(){
        return view('front.aboutus');
    }

    public function contactus(){
        return view('front.contactus');
    }


    // public function cart(){
    //     return view('front.cart');
    // }



public function productview(Request $request){
    $id = $request->id;
    $product = Product::with('discounts')->find($id);
    $category_id=$product->category_id;
    $related_products = Product::where('category_id',$category_id)->get();

    return view('front.productview', compact('product','related_products'));
}

// public function user_login()
// {
//     if (Auth::check()) {
//         return redirect()->route('activeevents');
//     }

//     return view('front.login');
// }

public function logout()
{
    Auth::logout(); // Log the user out
    session()->flush(); // Clear all session data
    return redirect()->route('logincheck'); // Redirect to the login route
}

public function user_store(Request $request)
{
    $data = array(
        'name'     => $request->first_name . ' ' . $request->last_name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => 'user'
    );

    $user = User::create($data);

    // Flash a success message to the session
    session()->flash('success', 'Welcome to 3Sixtyshows. Login please to explore our Entertainment Events');

    return redirect()->route('user_login');
}
public function loginCheck(Request $request)
{
    // Handle GET request
    if ($request->isMethod('get')) {
        return view('front.login'); // Return the login view
    }

    // Handle POST request
    if ($request->isMethod('post')) {
        // Validate the incoming request data
        $request->validate([
            'emaillogin' => 'required|email',
            'passwordlogin' => 'required|string',
        ]);

        $credentials = [
            'email' => $request->emaillogin,
            'password' => $request->passwordlogin,
        ];

        // Attempt to log the user in
        if (Auth::attempt($credentials)) {
            // Get the authenticated user
            $user = Auth::user();

            // Check if the user has the 'admin' role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard'); // Redirect to admin dashboard
            }

            return redirect()->route('index'); // Redirect to home for non-admin users
        }

        // If login fails, redirect back with an error message
        return back()->withErrors(['message' => 'Invalid email or password']);
    }

    // Fallback in case of unsupported request method
    return redirect()->route('index');
}



}


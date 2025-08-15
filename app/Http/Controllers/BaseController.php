<?php

namespace App\Http\Controllers;
use App\Models\Discount;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

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

    public function productview(Request $request){
        $id = $request->id;
        $product = Product::with('discounts')->find($id);
        $category_id=$product->category_id;
        $related_products = Product::where('category_id',$category_id)->get();

        return view('front.productview', compact('product','related_products'));
    }

    public function logout()
    {
        Auth::logout(); // Log the user out
        session()->flush(); // Clear all session data
        return redirect()->route('user_login'); // Fixed: redirect to login page
    }

    // Update your existing user_store method:

public function user_store(Request $request)
{
    // Your existing validation code...
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email|max:255',
        'password' => 'required|string|min:6|max:255',
    ], [
        // Your existing custom messages...
        'email.unique' => 'This email address is already registered. Please use a different email or try logging in.',
        'email.required' => 'Email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'password.min' => 'Password must be at least 6 characters long.',
        'first_name.required' => 'First name is required.',
        'last_name.required' => 'Last name is required.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput($request->except('password'));
    }

    try {
        // Your existing user creation logic...
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return redirect()->back()
                ->withErrors(['email' => 'This email address is already registered. Please use a different email or try logging in.'])
                ->withInput($request->except('password'));
        }

        $data = [
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ];

        $user = User::create($data);

        // LOGIN USER AFTER REGISTRATION
        Auth::login($user);

        // CHECK FOR INTENDED BOOKING URL
        $intendedUrl = session('intended_booking_url');
        if ($intendedUrl) {
            session()->forget(['intended_booking_url', 'booking_message']);
            return redirect($intendedUrl)
                ->with('success', 'Account created successfully! Please complete your booking.');
        }

        // Default success redirect
        session()->flash('success', 'Welcome to 3Sixtyshows! Registration successful. Please login to explore our Entertainment Events.');
        return redirect()->route('user_login');

    } catch (QueryException $e) {
        // Your existing error handling...
        if ($e->errorInfo[1] == 1062) {
            return redirect()->back()
                ->withErrors(['email' => 'This email address is already registered. Please use a different email or try logging in.'])
                ->withInput($request->except('password'));
        }

        return redirect()->back()
            ->withErrors(['error' => 'An error occurred during registration. Please try again.'])
            ->withInput($request->except('password'));

    } catch (\Exception $e) {
        \Log::error('User registration error: ' . $e->getMessage());

        return redirect()->back()
            ->withErrors(['error' => 'An unexpected error occurred. Please try again later.'])
            ->withInput($request->except('password'));
    }
}

public function loginCheck(Request $request)
{
    // Handle GET request - show login form
    if ($request->isMethod('get')) {
        // If user is already authenticated, check for intended booking URL
        if (Auth::check()) {
            $intendedUrl = session('intended_booking_url');
            if ($intendedUrl) {
                session()->forget('intended_booking_url');
                return redirect($intendedUrl);
            }
            return redirect()->route('index');
        }
        return view('front.login');
    }

    // Handle POST request - process login
    if ($request->isMethod('post')) {
        // Your existing validation code...
        $validator = Validator::make($request->all(), [
            'emaillogin' => 'required|email|max:255',
            'passwordlogin' => 'required|string|max:255',
        ], [
            'emaillogin.required' => 'Email address is required.',
            'emaillogin.email' => 'Please enter a valid email address.',
            'passwordlogin.required' => 'Password is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('emaillogin'));
        }

        try {
            $credentials = [
                'email' => $request->emaillogin,
                'password' => $request->passwordlogin,
            ];

            // Attempt to log the user in
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::user();

                // CHECK FOR INTENDED BOOKING URL FIRST
                $intendedUrl = session('intended_booking_url');
                if ($intendedUrl) {
                    session()->forget(['intended_booking_url', 'booking_message']);
                    return redirect($intendedUrl)
                        ->with('success', 'Welcome back! Please complete your booking.');
                }

                // Default redirects (your existing logic)
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                session()->flash('success', 'Welcome back, ' . $user->name . '!');
                return redirect()->route('index');
            }

            // If login fails
            return redirect()->back()
                ->withErrors(['login_error' => 'Invalid email or password. Please check your credentials and try again.'])
                ->withInput($request->only('emaillogin'));

        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred during login. Please try again.'])
                ->withInput($request->only('emaillogin'));
        }
    }

    return redirect()->route('user_login');
}
}

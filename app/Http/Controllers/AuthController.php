<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;


class AuthController extends Controller
{
     /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'verify_code' => rand(1000,9999),
            'password' => Hash::make($request->password),
        ]);

        //send email to user for verfication
        try {
            sendMail($user);
          } catch (\Exception $e) {
            return response()->json(['message' => 'Kindly add email credentials into your .env file','status' => 'error']);
          }
        

        return response()->json([
            'message' => 'User created successfully. Verification code is send to your email',
            'user' => $user,
        ]);
    }
    
     /**
     * Resend Verification Code.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function resendCode(Request $request)
     {
         $user = User::where('email',$request->email)->first();
         if(!$user){
            return response()->json(['message' => 'Incorrect email','status' => 'error']);
         }

         //send email to user for verfication
         try {
            sendMail($user);
          } catch (\Exception $e) {
            return response()->json(['message' => 'Kindly add email credentials into your .env file','status' => 'error']);
          }

        return response()->json([
            'message' => 'Verification code is send to your email',
            'user' => $user,
        ]);
     }

     /**
     * Validate Verification Code.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function validateCode(Request $request)
     {
         $user = User::where([['email',$request->email],['verify_code',$request->code]])->first();
         if(!$user){
            return response()->json(['message' => 'Incorrect email or code','status' => 'error']);
         }

         $user->email_verified_at = Carbon::now();
         $user->save();
        $token = Auth::login($user);
 
        return response()->json([
            'message' => 'You are successgully verified',
            'user' => $user,
            'token' => $token,
        ]);
     }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json(['status' => 'error','message' => 'Unauthorized',], 401);
        }
        $user = Auth::user();
        if($user->email_verified_at == null)
        {
            return response()->json(['status' => 'error','message' => 'Email is not verfied','email' => $user->email]);  
        }
        return $this->createNewToken($token);
    }

     /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user()
        ]);
    }
  
}

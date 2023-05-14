<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Mail\ResetPasswordMail;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ChangePasswordRequest;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','signup','sendPasswordResetLink','test']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Email or Password doesn\'t exist'], 401);
        }

        return $this->respondWithToken($token);
    }


    public function signup(SignUpRequest $request){
        $user = User::create($request->all());

        return $this->login($request);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()->name,
        ]);
    }


    public function sendPasswordResetLink(Request $request){

        if(!$this->validateEmail($request->email)){
            return $this->failedResponse();
        }

        $this->sendMail($request->email);

        return $this->successResponse();
    }

    public function sendMail($email){

        $token = $this->createToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }

    public function createToken($email){
        $oldToken = DB::table('password_reset_tokens')->where('email',$email)->first();

        if($oldToken){
            return $oldToken->token;
        }

        $token = Str::random(60);

        $this->saveToken($token, $email);

        return $token;
    }

    public function saveToken($token, $email){
        DB::table('password_reset_tokens')->insert([
           'email' => $email,
           'token' => $token,
           'created_at' => Carbon::now()
        ]);
    }

    public function validateEmail($email){
        return !!User::where('email',$email)->first();
    }

    public function failedResponse(){
        return response()->json([
            'error' => 'Email not found in database'
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse(){
        return response()->json([
            'error' => 'Reset email sent'
        ], Response::HTTP_OK);
    }




    public function test(){
        $token = Str::random(60);
        $email = 'gabriel@manifestghana.com';
        Mail::to($email)->send(new ResetPasswordMail($token));
    }

}

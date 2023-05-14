<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ChangePasswordRequest;

class ChangePasswordController extends Controller
{
    public function proccess(ChangePasswordRequest $request){
        return $this->getPasswordResetTableRow($request)->count() > 0 ? $this->changePassword($request) : $this->tokenNotFoundResponse();
    }

    private function getPasswordResetTableRow($request){
        return DB::table('password_reset_tokens')->where(['email' => $request->email, 'token' => $request->resetToken]);
    }

    private function changePassword($request){
        $user = User::whereEmail($request->email)->first();

        $user->update(['password' =>$request->password]);
        $this->getPasswordResetTableRow($request)->delete();
        return response()->json(['data' => 'Password Successfully Changed'], Response::HTTP_CREATED);
    }

    private function tokenNotFoundResponse(){
        return response()->json(['error' => 'Token or Email is incorrect']);
    }

}

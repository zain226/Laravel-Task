<?php

use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Mail;

if (! function_exists('sendMail')) {
    function sendMail($user)
    {
        $data = [
            'name' => $user->name,
            'code' => $user->verify_code,
        ];
        Mail::to($user->email)->send(new VerificationMail($data));
    }
}
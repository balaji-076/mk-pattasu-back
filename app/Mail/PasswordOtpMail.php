<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordOtpMail extends Mailable
{
    public string $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this
            ->subject('Your OTP to Set Password')
            ->view('emails.admin-otp');
    }
}

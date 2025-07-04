<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class GlobleHelper
{
	public static function sendMail(?array $mailData = [])
    {
    	$mail_flag = 0;
        try{
            
            Mail::raw($mailData['body'], function ($message) use ($mailData) {
                $message->to($mailData['to'])
                ->subject($mailData['subject']);
            });

            $mail_flag = 1;
            if(count(Mail::failures()) > 0){
                $mail_flag = 0;
            }
        }catch(\Exception $e) {
            $mail_flag = 0;
        }
        return $mail_flag;
    }
}
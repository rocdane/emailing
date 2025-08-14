<?php

namespace App\Services;

use App\Mail\NewsLetter;
use Mail;

class MailService
{
    public function send($email): bool
    {
        /*if(Mail::send(new NewsLetter($email))){
            return true;
        }else{
            return false;
        }*/
        return true;
    }
}
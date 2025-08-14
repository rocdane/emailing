<?php

namespace App\Mail;

use http\Env\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsLetter extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data)
    {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        $tracker = route('email.open', ['email' => $this->data['address']]);
        $target = route('email.click', ['email' => $this->data['address']]);
        $link = route('email.unsubscribe', ['email' => $this->data['address']]);
        
        return $this->from(env('MAIL_FROM_ADDRESS'),env('APP_NAME'))
            ->to($this->data['address'], $this->data['name'])
            ->view('mail.letter',[
                'name' => $this->data['name'],
                'subject' => $this->data['subject'],
                'content' => $this->data['content'], 
                'tracker' => $tracker, 
                'target' => $target,
                'link' => $link
                ])
            ->subject($this->data['subject']);
    }
}
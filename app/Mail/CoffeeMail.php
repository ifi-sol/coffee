<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CoffeeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'info@cupcardcafe.com';
        $name    = 'CupCard';

        if($this->user->for == 'forgot_password') {
            $email = $this->view('mail.forgot_password')
                ->from($address, $name)
                ->replyTo($address, $name)
                ->subject($this->user->subject)
                ->with(['user' => $this->user]);
        }elseif($this->user->for == 'approve_user'){
            $email = $this->view('mail.approve_user')
                ->from($address, $name)
                ->replyTo($address, $name)
                ->subject($this->user->subject)
                ->with(['user' => $this->user]);
        }elseif($this->user->for == 'admin_email'){
            $email = $this->view('mail.admin_email')
                ->from($address, $name)
                ->replyTo($address, $name)
                ->subject($this->user->subject)
                ->with(['user' => $this->user]);
        }else{
            $email = $this->view('mail.signup')
                ->from($address, $name)
                ->replyTo($address, $name)
                ->subject($this->user->subject)
                ->with(['user' => $this->user]);
        }
        /*if (count($this->user->files) > 0) {
            foreach ($this->user->files as $filePath) {
                $email->attach($filePath);
            }
        }*/

        return $email;
    }
}

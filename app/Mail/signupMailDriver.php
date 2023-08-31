<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class signupMailDriver extends Mailable
{
    use Queueable, SerializesModels;
    public $driver;
    public $id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($driver, $id)
    {
        $this->driver = $driver;
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.signupDriver');
    }
}


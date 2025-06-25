<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $type, $data;

    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Reminder to Update Your Assets')
            ->view('emails.reminder');
    }
}

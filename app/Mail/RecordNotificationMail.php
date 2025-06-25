<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecordNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $messageContent; // Changed name to avoid confusion with Illuminate\Mail\Message

    public function __construct($title, $messageContent)
    {
        $this->title = $title;
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        return $this->view('emails.record_delete_email')
            ->with([
                'title' => $this->title,
                'message' => $this->messageContent // Pass as string
            ]);
    }
}

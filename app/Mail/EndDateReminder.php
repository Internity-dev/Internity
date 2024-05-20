<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EndDateReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $teacher;
    public $student;
    public $company;
    public $today;
    public $end;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($teacher, $student, $company, $today, $end)
    {
        $this->teacher = $teacher;
        $this->student = $student;
        $this->company = $company;
        $this->today = $today;
        $this->end = $end;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Pengingat Kepulangan PKL dari ' . $this->company->name,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.end-date-reminder',
            with: [
                'teacher' => $this->teacher,
                'student' => $this->student,
                'company' => $this->company,
                'today' => $this->today,
                'end' => $this->end,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}

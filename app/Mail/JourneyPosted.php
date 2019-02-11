<?php

namespace App\Mail;


use App\journey;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class JourneyPosted extends Mailable
{
    use Queueable, SerializesModels;

    protected $journey;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Journey $journey)
    {
        //
        $this->subject($journey->name.' Saved');

        $this->journey = $journey;
        $this->link = url("/journey/{$journey->UJID}/edit?key={$journey->key}");
        $this->share_link = url("/journey/{$journey->UJID}");
        $this->currnetTime = date('Y-m-d H:i:s', time());

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.JourneyCreated')
                    ->with([
                        'journey' => $this->journey,
                        'link' => $this->link,
                        'share_link' => $this->share_link,
                        'currnetTime' => $this->currnetTime,
                    ]);

    }
}

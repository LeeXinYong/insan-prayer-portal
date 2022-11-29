<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $params = ["email_contents" => $this->data['email_contents'], "subject" => $this->data['subject'], "header_color" => $this->data["header_color"] ?? "#FBD738"];
        if(isset($this->data['buttons'])) {
            $params['buttons'] = $this->data['buttons'];
        }
        return $this->view('pages.settings.emailtemplate.mailview')
                    ->from($this->data['email']['address'], $this->data['email']['name'])
                    ->subject($this->data['subject'])
                    ->with($params);
    }
}

<?php

/**
 * Mail Queue
 *
 * @package     Makent Space
 * @subpackage  Mailable
 * @category    Mail Queue
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailQueue extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * View Data for the Email
     *
     * @var string
     **/
    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
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
        \App::setLocale($this->data['locale']);
        return $this->view($this->data['view_file'])
                    ->subject($this->data['subject'])
                    ->with($this->data);
    }
}

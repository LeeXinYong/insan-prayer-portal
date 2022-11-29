<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Settings\EmailSettingController;

class SendEmail implements ShouldQueue
{
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $data;
    protected $type;
    protected $extrasubject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $data, $type, $extrasubject = null)
    {
        $this->user = $user;
        $this->data = $data;
        $this->type = $type;
        $this->extrasubject = $extrasubject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new EmailSettingController)->sendEmail($this->user, $this->data, $this->type, $this->extrasubject);
    }
}

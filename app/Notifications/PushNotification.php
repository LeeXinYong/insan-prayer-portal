<?php

namespace App\Notifications;

use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Traits\Conditionable;

class PushNotification extends Notification implements ShouldQueue
{
    use Queueable, Conditionable;

    private $channels;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public string  $title,
        public string  $body,
        public array   $data = [],
        public ?string $imagePath = null,
        public ?string $largeIcon = null
    )
    {
        $this->setChannels(...App::call([app(PushNotificationService::class), 'via']));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->channels;
    }

    public function setChannels(...$channels)
    {
        $this->channels = $channels;
    }

    public function unsetChannels(...$channels): static
    {
        foreach ($channels as $channel) {
            if (in_array($channel, $this->channels)) {
                $this->channels = array_diff($this->channels, [$channel]);
            }
        }

        return $this;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'data'  => $this->data,
            'imagePath' => $this->imagePath,
            'largeIcon' => $this->largeIcon,
        ];
    }
}

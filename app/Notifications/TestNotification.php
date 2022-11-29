<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\Pure;

class TestNotification extends PushNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(string $title = null, string $body = null, array $data = [], ?string $imagePath = null, ?string $largeIcon = null)
    {
        $message = \Illuminate\Foundation\Inspiring::quote();

        $explode = explode("-", $message);
        $author = trim(array_pop($explode));

        $title = $title ?: "Let $author power your day!";
        $body = $body ?: $message;
        $data = empty($data) ? $data : ["author" => $author];
        $imagePath = $imagePath ?: "https://picsum.photos/200/300/?random";
        $largeIcon = $largeIcon ?: "https://picsum.photos/200/300/?random";

        parent::__construct(
            title: $title,
            body: $body,
            data: $data,
            imagePath: $imagePath,
            largeIcon: $largeIcon);
    }
}

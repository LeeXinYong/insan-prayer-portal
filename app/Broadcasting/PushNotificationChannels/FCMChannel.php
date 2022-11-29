<?php

namespace App\Broadcasting\PushNotificationChannels;

use App\Services\NotificationType;
use App\Traits\ChannelGetConfig;
use Illuminate\Notifications\Notification;

class FCMChannel
{
    use ChannelGetConfig {
        ChannelGetConfig::init as initChannelGetConfig;
    }

    private bool $doNotSend = false;

    private $messaging;

    public function __construct()
    {
        $this->initChannelGetConfig(NotificationType::FCM);

        try {
            include_once \Kreait\Firebase\Factory::class;
            include_once \Kreait\Firebase\Contract\Messaging::class;
            include_once \Kreait\Firebase\Messaging\ApnsConfig::class;
            include_once \Kreait\Firebase\Messaging\CloudMessage::class;

            $factory = (new \Kreait\Firebase\Factory())->withServiceAccount(storage_path(self::getConfig('credential_path')));
            $this->messaging = $factory->createMessaging();
        } catch (\Exception|\Throwable $e) {
            $this->doNotSend = true;
        }
    }

    public function shouldNotSend() : bool
    {
        return $this->doNotSend;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return array
     * @throws \Exception
     */
    public function send(mixed $notifiable, Notification $notification): array
    {
        $messaging = $this->messaging;

        $message = $this->prepareMessage($notification, notifiable: $notifiable);

        if (!method_exists($notifiable, 'getNotificationIdentifiers')) {
            throw new \Exception('The notifiable must have a getNotificationIdentifiers method.');
        }

        $tokens = $notifiable->getNotificationIdentifiers(self::$channel);

        $validTokens = $messaging->validateRegistrationTokens($tokens)["valid"];

        $results = [];

        foreach ($validTokens as $chunkedValidTokens) {
            // validate and send
            $messaging->validate($message);
            $results[] = $messaging->sendMulticast($message, $chunkedValidTokens);
        }

        return $results;
    }

    private function getNotificationFields(Notification $notification, $notifiable): array
    {
        $method = 'toArray';
        if (method_exists($notification, 'toFCM')) {
            $method = 'toFCM';
        }
        $message = $notification->{$method}($notifiable);

        return [
            "title" => $message["title"],
            "body" => $message["body"],
            "data" => $message["data"] ?? [],
            "imagePath" => $message["imagePath"] ?? null,
            "largeIcon" => $message["largeIcon"] ?? null,
        ];
    }

    /**
     * Prepare the clod message used to send notification
     *
     * @param Notification $notification
     * @param null $notifiable
     * @return mixed
     */
    private function prepareMessage(Notification $notification, $notifiable = null): mixed
    {
        $message = \Kreait\Firebase\Messaging\CloudMessage::new();

        $fields = $this->getNotificationFields($notification, $notifiable);

        $notification = \Kreait\Firebase\Messaging\Notification::create($fields['title'], $fields['body']);

        $apnsConfig = [];
        $androidConfig = [];

        // set image
        if (!is_null($fields['imagePath'])) {
            $imageUrl = url(str_replace("public/", "storage/", $fields['imagePath']));
            $notification = $notification->withImageUrl($imageUrl);

            $apnsConfig['payload'] = [
                'aps' => [
                    'mutable-content' => 1,
                ],
            ];

            $apnsConfig['fcm_options'] = [
                "image" => $imageUrl
            ];
        }

        if (!is_null($fields['largeIcon'])) {
            $iconUrl = url(str_replace("public/", "storage/", $fields['largeIcon']));

            $androidConfig['notification'] = [
                'icon' => $iconUrl,
            ];
        }

        if (!empty($apnsConfig)) {
            $message = $message->withApnsConfig($apnsConfig);
        }

        if (!empty($androidConfig)) {
            $message = $message->withAndroidConfig($androidConfig);
        }

        return $message->withNotification($notification)->withData($fields['data']);
    }
}

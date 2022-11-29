<?php

namespace App\Broadcasting\PushNotificationChannels;

use App\Services\NotificationType;
use Illuminate\Notifications\Notification;
use App\Traits\ChannelGetConfig;

class OneSignalChannel
{
    use ChannelGetConfig {
        ChannelGetConfig::init as initChannelGetConfig;
    }

    private bool $doNotSend = false;

    public function __construct()
    {
        $this->initChannelGetConfig(NotificationType::OneSignal);

        if (
            !self::hasConfig('app_id') ||
            !self::hasConfig('api_endpoint') ||
            !self::hasConfig('app_secret')
        ) {
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
     * @return bool|string
     * @throws \Exception
     */
    public function send(mixed $notifiable, Notification $notification): bool|string
    {
        $fields = self::getNotificationFields($notification, notifiable: $notifiable);
        if (!method_exists($notifiable, 'getNotificationIdentifiers')) {
            throw new \Exception('The notifiable must have a getNotificationIdentifiers method.');
        }

        $playerId = $notifiable->getNotificationIdentifiers(self::$channel);

        $fields['include_player_ids'] = $playerId;

        return self::callCreateNotificationAPI($fields);
    }

    /**
     * Send the given notification to platforms.
     *
     * @param iterable $platforms
     * @param Notification $notification
     * @return bool|string
     */
    public static function sendToPlatform(iterable $platforms, Notification $notification): bool|string
    {
        $fields = self::getNotificationFields($notification);

        $fields['included_segments'] = ["Subscribed Users"];

        foreach ($platforms as $platform) {
            if ($platform === 'ios') {
                $fields['isIos'] = true;
            } else if ($platform === 'android') {
                $fields['isAndroid'] = true;
            } else if ($platform === 'huawei') {
                $fields['isHuawei'] = true;
            }
        }

        return self::callCreateNotificationAPI($fields);
    }

    private static function getNotificationFields(Notification $notification, mixed $notifiable = null): array
    {
        $method = 'toArray';
        if (method_exists($notification, 'toOneSignal')) {
            $method = 'toOneSignal';
        }
        $message = $notification->{$method}($notifiable);

        return self::prepareMessage(
            $message['title'],
            $message['body'],
            data: $message['data'] ?? [],
            imagePath: $message['imagePath'] ?? null,
            largeIcon: $message['largeIcon'] ?? null,
        );
    }

    /**
     * Prepare message to be sent to OneSignal.
     *
     * @param $title
     * @param $body
     * @param $imagePath
     * @param array $data
     * @return array
     */
    private static function prepareMessage($title, $body, array $data = [], $imagePath = null, $largeIcon = null): array
    {
        $headings = [
            "en" => $title
        ];

        $content = [
            "en" => $body
        ];

        $fields = [
            'app_id' => self::getConfig('app_id'),
            'headings' => $headings,
            'contents' => $content,
        ];

        if (empty($data)) {
            $fields["data"] = (object)$data;
        }

        if (!is_null($imagePath)) {
            $fields["ios_attachments"] = json_encode(["image" => $imagePath]);
            $fields["big_picture"] = $imagePath;
            $fields["huawei_big_picture"] = $imagePath;
            $fields["chrome_web_image"] = $imagePath;
            $fields["adm_big_picture"] = $imagePath;
            $fields["chrome_big_picture"] = $imagePath;
        }

        if (!is_null($largeIcon)) {
            $fields["large_icon"] = $largeIcon;
            $fields["huawei_large_icon"] = $largeIcon;
        }

        return $fields;
    }

    /**
     * Call OneSignal API to send notification
     *
     * @param $fields
     * @return bool|string
     */
    private static function callCreateNotificationAPI($fields): bool|string
    {
        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::getConfig('api_endpoint'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . self::getConfig('app_secret')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}

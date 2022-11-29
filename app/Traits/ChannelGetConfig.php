<?php

namespace App\Traits;

use App\Services\NotificationType;
use Illuminate\Support\Facades\Config;

trait ChannelGetConfig
{
    private static NotificationType $channel;

    public function init($channel)
    {
        self::$channel = $channel;
    }

    public static function getConfig($config)
    {
        return config('notifications.channels.'.self::$channel->toString().".".$config);
    }

    public static function hasConfig($config): bool
    {
        return Config::has('notifications.channels.'.self::$channel->toString().".".$config);
    }
}

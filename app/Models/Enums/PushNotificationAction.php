<?php

namespace App\Models\Enums;

use Illuminate\Support\Facades\Storage;
use Nette\NotImplementedException;

enum PushNotificationAction
{
    use EnumTrait;

    case Default;

    case Video;

    public static function getAdminConfigurationActions(): array
    {
        return collect([
            self::Default,
            self::Video
        ])
        ->mapWithKeys(fn ($action) => [$action->name => $action])
        ->toArray();
    }

    public function getDefaultTitle(array $data = []): string
    {
        $key =  match ($this) {
            self::Video => "push_notification.default_text.video",
            default => throw new NotImplementedException()
        };

        return __($key, $data);
    }

    public function getDefaultMessage(array $data = []): string
    {
        $key = match ($this) {
            self::Video => "push_notification.default_message.video",
            default => throw new NotImplementedException()
        };

        return __($key, $data);
    }

    public function getIcon(): ?string
    {
        $filePath = match ($this) {
            self::Video,
            self::Default => "default.png",
            default => throw new NotImplementedException(),
        };

        return $filePath ? asset(theme()->getCustomizeUrlPath() . "media/icons/$filePath") : null;
    }

    public static function getDefault(): self
    {
        return self::Default;
    }
}

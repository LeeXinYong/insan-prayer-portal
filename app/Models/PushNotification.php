<?php

namespace App\Models;

use App\Models\Enums\PushNotificationAction;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PushNotification extends Model
{
    use HasFactory, ModelTrait;

    private PushNotificationAction $actionType;

    public function withAction(PushNotificationAction $actionType, ...$args): self
    {
        $this->actionType = $actionType;

        $this->action = [
            "class" => $actionType->name,
            ...$args,
        ];

        return $this;
    }

    protected $fillable = [
        'title',
        'body',
        'action',
        'image',
        'icon',
        'sent_by',
        'sender_ip',
    ];

    protected $casts = [
        'action' => 'array'
    ];

    public function mapToPushNotification(): \App\Notifications\PushNotification
    {
        $data = $this->action ?? [];

        $image = !is_null($this->image) && Storage::exists($this->image) ? Storage::url($this->image) : null;
        $icon = !is_null($this->icon) && Storage::exists($this->icon) ? Storage::url($this->icon) : null;

        $this->title = $this->title ?? $this->actionType->getDefaultTitle();
        $this->body = $this->body ?? $this->actionType->getDefaultMessage();
        $this->icon = $icon ?? $this->actionType->getIcon();

        return new \App\Notifications\PushNotification($this->title, $this->body, data: $data, imagePath: $image, largeIcon: $icon);
    }
}

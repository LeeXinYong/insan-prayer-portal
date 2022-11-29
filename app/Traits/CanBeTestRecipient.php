<?php

namespace App\Traits;

use App\Models\TestRecipient;

trait CanBeTestRecipient
{
    public function testRecipient(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(TestRecipient::class, 'notifiable');
    }

    public function isTestRecipient(): bool
    {
        return $this->testRecipient()->exists();
    }

    public function becomeTestRecipient(): TestRecipient
    {
        if (! $this->isTestRecipient()) {
            $this->testRecipient()->create();
        }

        return $this->testRecipient;
    }

    /**
     * @throws \Exception
     */
    public function resignTestRecipient(): mixed
    {
        if ($this->isTestRecipient()) {
            $testRecipient = $this->testRecipient;
            $this->testRecipient()->delete();
            return $testRecipient;
        } else {
            throw new \Exception(__("notification.manage_test_recipients.not_a_test_recipient", ["user" => $this->name]));
        }
    }
}

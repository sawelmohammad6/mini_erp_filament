<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        foreach (static::getActivitiesToLog() as $event) {
            static::{$event}(function ($model) use ($event) {
                $model->logActivity($event);
            });
        }
    }

    protected static function getActivitiesToLog(): array
    {
        return ['created', 'updated', 'deleted'];
    }

    public function logActivity(string $event): void
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return;
            }

            $description = match ($event) {
                'created' => class_basename($this) . ' ' . ($this->getLogIdentifier() ?? $this->getKey()) . ' was created',
                'updated' => class_basename($this) . ' ' . ($this->getLogIdentifier() ?? $this->getKey()) . ' was updated',
                'deleted' => class_basename($this) . ' ' . ($this->getLogIdentifier() ?? $this->getKey()) . ' was deleted',
                default => class_basename($this) . ' was ' . $event,
            };

            ActivityLog::create([
                'user_id' => $user->id,
                'loggable_type' => get_class($this),
                'loggable_id' => $this->getKey(),
                'action' => $event,
                'description' => $description,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    protected function getLogIdentifier(): ?string
    {
        if (isset($this->name)) {
            return $this->name;
        }
        if (isset($this->order_number)) {
            return $this->order_number;
        }
        if (isset($this->sku)) {
            return $this->sku;
        }

        return null;
    }

    public function activityLogs(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}

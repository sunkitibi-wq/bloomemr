<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPractice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $practice_id
 * @property string $name
 * @property string $endpoint_url
 * @property string|null $secret
 * @property array $events
 * @property string $status
 * @property int $failure_count
 * @property Carbon|null $paused_until
 */
class WebhookSubscription extends Model
{
    use BelongsToPractice;

    protected $fillable = [
        'practice_id',
        'name',
        'endpoint_url',
        'secret',
        'events',
        'status',
        'last_sent_at',
        'failure_count',
        'paused_until',
    ];

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'last_sent_at' => 'datetime',
            'paused_until' => 'datetime',
        ];
    }

    /** @param Builder<WebhookSubscription> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('paused_until')->orWhere('paused_until', '<=', now()));
    }

    public function markFailed(): void
    {
        $this->increment('failure_count');

        if ($this->failure_count >= 10) {
            $this->update(['status' => 'paused', 'paused_until' => now()->addHour()]);
        }
    }

    public function markSent(): void
    {
        $this->update(['last_sent_at' => now(), 'failure_count' => 0]);
    }
}

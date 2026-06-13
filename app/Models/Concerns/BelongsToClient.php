<?php

namespace App\Models\Concerns;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Scopes a model to the authenticated user's client (tenant).
 *
 * While a tenant user is authenticated, every query is automatically filtered to
 * their client and new records inherit the client id. Super admins and console
 * processes (no client context) are unaffected, so seeders and reports still see
 * every row.
 */
trait BelongsToClient
{
    public static function bootBelongsToClient(): void
    {
        static::addGlobalScope('client', function (Builder $builder): void {
            if ($clientId = self::currentClientId()) {
                $builder->where($builder->getModel()->getTable().'.client_id', $clientId);
            }
        });

        static::creating(function (Model $model): void {
            if (empty($model->client_id) && $clientId = self::currentClientId()) {
                $model->client_id = $clientId;
            }
        });
    }

    private static function currentClientId(): ?int
    {
        return Auth::check() ? Auth::user()->client_id : null;
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}

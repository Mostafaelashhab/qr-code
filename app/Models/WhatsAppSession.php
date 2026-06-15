<?php

namespace App\Models;

use App\Enums\WhatsAppStatus;
use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSession extends Model
{
    use BelongsToClient;

    protected $table = 'whatsapp_sessions';

    protected $fillable = [
        'client_id',
        'auth_key',
        'device_uuid',
        'app_key',
        'status',
        'phone',
        'last_connected_at',
    ];

    protected $hidden = [
        'auth_key',
        'app_key',
    ];

    protected $attributes = [
        'status' => WhatsAppStatus::Disconnected->value,
    ];

    protected function casts(): array
    {
        return [
            'status' => WhatsAppStatus::class,
            'last_connected_at' => 'datetime',
        ];
    }

    public function isConnected(): bool
    {
        return $this->status->isConnected();
    }

    /**
     * Whether the super admin has provisioned this center's waapi account and
     * device — i.e. it can show a QR to link. Until then the center sees a
     * "being prepared" state with no QR.
     */
    public function isProvisioned(): bool
    {
        return filled($this->auth_key) && filled($this->device_uuid);
    }
}

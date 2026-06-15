<?php

namespace App\Models;

use App\Enums\MessageChannel;
use App\Enums\SmsType;
use App\Models\Concerns\BelongsToClient;
use Database\Factories\SmsMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsMessage extends Model
{
    /** @use HasFactory<SmsMessageFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'student_id',
        'to',
        'type',
        'channel',
        'body',
        'status',
        'sent_at',
    ];

    protected $attributes = [
        'type' => SmsType::General->value,
        'channel' => MessageChannel::WhatsApp->value,
        'status' => 'sent',
    ];

    protected function casts(): array
    {
        return [
            'type' => SmsType::class,
            'channel' => MessageChannel::class,
            'sent_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

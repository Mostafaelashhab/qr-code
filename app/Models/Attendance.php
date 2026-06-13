<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Models\Concerns\BelongsToClient;
use Database\Factories\AttendanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    /** @use HasFactory<AttendanceFactory> */
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
    ];

    protected $attributes = [
        'status' => AttendanceStatus::Present->value,
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
        ];
    }

    /**
     * @return BelongsTo<AttendanceSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

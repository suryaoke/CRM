<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['nama', 'no_tlp', 'alamat', 'user_id', 'perusahaan_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // One-to-Many Relationship with Karyawan
    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class);
    }
}

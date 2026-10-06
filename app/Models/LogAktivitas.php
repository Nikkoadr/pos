<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $guarded = [];
    protected $table = 'activity_logs';
    public $timestamps = false;

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public static function catat(string $aksi, string $model, $modelId, string $keterangan, array $properties = []): void
    {
        try {
            $user = auth()->user();
            self::create([
                'user_id' => $user->id ?? null,
                'nama_user' => $user->nama ?? $user->name ?? 'sistem',
                'aksi' => $aksi,
                'model' => $model,
                'model_id' => $modelId,
                'keterangan' => $keterangan,
                'properties' => $properties ?: null,
                'ip' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal catat aktivitas: ' . $e->getMessage());
        }
    }
}

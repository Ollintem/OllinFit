<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Ajustes del sistema editables desde el panel de Integraciones.
 * Los valores sensibles se guardan cifrados.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'is_encrypted'];

    protected $casts = ['is_encrypted' => 'boolean'];

    /** Memo por petición para no consultar la tabla varias veces. */
    protected static $memo = null;

    protected static function keyed()
    {
        if (static::$memo === null) {
            static::$memo = static::all()->keyBy('key');
        }
        return static::$memo;
    }

    public static function get(string $key, $default = null)
    {
        $row = static::keyed()->get($key);
        if (! $row) {
            return $default;
        }

        $value = $row->value;
        if ($row->is_encrypted && $value !== null && $value !== '') {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Throwable $e) {
                return $default;
            }
        }

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function put(string $key, $value, bool $encrypt = false): void
    {
        $stored = ($encrypt && $value !== null && $value !== '')
            ? Crypt::encryptString((string) $value)
            : $value;

        static::updateOrCreate(['key' => $key], ['value' => $stored, 'is_encrypted' => $encrypt]);
        static::$memo = null; // invalidar memo
    }
}

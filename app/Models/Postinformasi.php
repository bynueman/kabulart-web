<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postinformasi extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'image',
        'deskripsi',
        'deskripsi_id',
        'deskripsi_en',
        'translation_source',
        'translation_manual',
    ];

    protected $casts = [
        'translation_manual' => 'boolean',
    ];

    /**
     * Localized description accessor.
     * Automatically returns the appropriate language based on active app locale
     * with graceful fallback to the opposite locale and original description.
     */
    public function getDeskripsiAttribute($value)
    {
        $locale = app()->getLocale();

        if ($locale === 'en') {
            if (!empty($this->attributes['deskripsi_en'])) {
                return $this->attributes['deskripsi_en'];
            }
            if (!empty($this->attributes['deskripsi_id'])) {
                return $this->attributes['deskripsi_id'];
            }
            return $value;
        }

        // Default or 'id'
        if (!empty($this->attributes['deskripsi_id'])) {
            return $this->attributes['deskripsi_id'];
        }
        if (!empty($this->attributes['deskripsi_en'])) {
            return $this->attributes['deskripsi_en'];
        }
        return $value;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postgalery extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'nama',
        'nama_id',
        'nama_en',
        'dimensi',
        'dimensi_id',
        'dimensi_en',
        'link',
        'translation_source',
        'translation_manual',
    ];

    protected $casts = [
        'translation_manual' => 'boolean',
    ];

    /**
     * Localized artwork name accessor.
     * Automatically returns the appropriate language based on active app locale
     * with graceful fallback to the opposite locale and original name.
     */
    public function getNamaAttribute($value)
    {
        $locale = app()->getLocale();

        if ($locale === 'en') {
            if (!empty($this->attributes['nama_en'])) {
                return $this->attributes['nama_en'];
            }
            if (!empty($this->attributes['nama_id'])) {
                return $this->attributes['nama_id'];
            }
            return $value;
        }

        // Default or 'id'
        if (!empty($this->attributes['nama_id'])) {
            return $this->attributes['nama_id'];
        }
        if (!empty($this->attributes['nama_en'])) {
            return $this->attributes['nama_en'];
        }
        return $value;
    }

    /**
     * Localized dimension accessor.
     */
    public function getDimensiAttribute($value)
    {
        $locale = app()->getLocale();

        if ($locale === 'en') {
            if (!empty($this->attributes['dimensi_en'])) {
                return $this->attributes['dimensi_en'];
            }
            if (!empty($this->attributes['dimensi_id'])) {
                return $this->attributes['dimensi_id'];
            }
            return $value;
        }

        // Default or 'id'
        if (!empty($this->attributes['dimensi_id'])) {
            return $this->attributes['dimensi_id'];
        }
        if (!empty($this->attributes['dimensi_en'])) {
            return $this->attributes['dimensi_en'];
        }
        return $value;
    }

    /**
     * Formatted order link that guarantees a valid, fully qualified WhatsApp or web URL.
     * Appends an informative prefilled message if the link is a standard WhatsApp chat link.
     */
    public function getOrderLinkAttribute(): string
    {
        $link = trim($this->attributes['link'] ?? '');
        $artworkName = $this->nama;
        $artworkDim  = $this->dimensi;

        $locale = app()->getLocale();
        $defaultMsg = ($locale === 'en')
            ? "Hello Kabul Art Gallery, I would like to inquire about ordering the artwork '{$artworkName}' ({$artworkDim}). Could you please share more information?"
            : "Halo Kabul Art Gallery, saya tertarik memesan karya lukisan '{$artworkName}' ({$artworkDim}). Mohon informasi ketersediaan dan detailnya.";

        if (empty($link)) {
            return 'https://wa.me/6282223242071?text=' . rawurlencode($defaultMsg);
        }

        // WhatsApp Business Product Catalog link: preserve exact product ID
        if (preg_match('/wa\.me\/p\/[0-9]+\/[0-9]+/i', $link)) {
            if (!preg_match('/^https?:\/\//i', $link)) {
                return 'https://' . ltrim($link, '/');
            }
            return $link;
        }

        // If it's a wa.me chat link without pre-filled text
        if (preg_match('/^https?:\/\/wa\.me\/([0-9]+)\/?$/i', $link, $m) || preg_match('/^wa\.me\/([0-9]+)\/?$/i', $link, $m)) {
            $phone = $m[1];
            return "https://wa.me/{$phone}?text=" . rawurlencode($defaultMsg);
        }

        // If it's already an http/https link (e.g. marketplace or customized wa.me with text)
        if (preg_match('/^https?:\/\//i', $link)) {
            return $link;
        }

        // If it starts with wa.me/
        if (str_starts_with($link, 'wa.me/')) {
            return 'https://' . $link;
        }

        // If it's just raw phone digits
        $cleanPhone = preg_replace('/[^\d]/', '', $link);
        if (!empty($cleanPhone)) {
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            return "https://wa.me/{$cleanPhone}?text=" . rawurlencode($defaultMsg);
        }

        return $link;
    }
}

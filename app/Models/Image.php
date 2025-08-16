<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image_path',
        'imageable_id',
        'imageable_type',
    ];

    /**
     * Obtenir le modèle parent imageable.
     */
    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Obtenir l'URL complète de l'image.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Obtenir l'URL de l'image redimensionnée.
     *
     * @param string $size
     * @return string
     */
    public function getUrl(string $size = 'thumb')
    {
        $path = pathinfo($this->image_path);
        $filename = $path['filename'];
        $extension = $path['extension'];
        
        // Supposons que vous avez des variations d'images stockées dans des dossiers comme 'thumb', 'medium', etc.
        $sizedPath = str_replace(
            $path['basename'],
            "{$filename}_{$size}.{$extension}",
            $this->image_path
        );
        
        return asset('storage/' . $sizedPath);
    }
}

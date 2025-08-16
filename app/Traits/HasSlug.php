<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the trait.
     */
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->generateSlug();
        });

        static::updating(function ($model) {
            $model->generateSlug();
        });
    }

    /**
     * Generate a slug for the model.
     */
    protected function generateSlug()
    {
        if (empty($this->slug) || $this->isDirty($this->slugSource())) {
            $slug = Str::slug($this->{$this->slugSource()});
            $originalSlug = $slug;
            $count = 2;

            while (static::where('slug', $slug)->where('id', '!=', $this->id ?? null)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $this->slug = $slug;
        }
    }

    /**
     * Get the source field for the slug.
     *
     * @return string
     */
    protected function slugSource()
    {
        return 'titre';
    }
}

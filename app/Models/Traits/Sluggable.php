<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot the Sluggable trait.
     */
    protected static function bootSluggable(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });

        static::updating(function ($model) {
            if ($model->shouldRegenerateSlug()) {
                $model->slug = $model->generateSlug();
            }
        });
    }

    /**
     * Determine if the slug should be regenerated.
     */
    protected function shouldRegenerateSlug(): bool
    {
        // Check if name or title is dirty
        return $this->isDirty('name') || $this->isDirty('title');
    }

    /**
     * Generate a unique slug for the model.
     */
    protected function generateSlug(): string
    {
        $source = $this->getSlugSource();
        $slug = Str::slug($source);

        // Ensure uniqueness
        return $this->makeSlugUnique($slug);
    }

    /**
     * Get the source attribute for the slug.
     */
    protected function getSlugSource(): string
    {
        // Check if model has custom sluggable source method
        if (method_exists($this, 'sluggableSource')) {
            return $this->sluggableSource();
        }

        // Default to 'name' or 'title'
        if (isset($this->attributes['name'])) {
            return $this->attributes['name'];
        }

        if (isset($this->attributes['title'])) {
            return $this->attributes['title'];
        }

        return '';
    }

    /**
     * Make the slug unique by appending a number if necessary.
     */
    protected function makeSlugUnique(string $slug): string
    {
        $originalSlug = $slug;
        $count = 1;

        // Get the column names that form a unique constraint with slug
        $uniqueColumns = $this->getSluggableUniqueColumns();

        while ($this->slugExists($slug, $uniqueColumns)) {
            $slug = $originalSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists.
     */
    protected function slugExists(string $slug, array $uniqueColumns = []): bool
    {
        $query = static::where('slug', $slug);

        // Add unique column constraints (e.g., organization_id for scoped uniqueness)
        foreach ($uniqueColumns as $column) {
            if (isset($this->attributes[$column])) {
                $query->where($column, $this->attributes[$column]);
            }
        }

        // Exclude current model when updating
        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        return $query->exists();
    }

    /**
     * Get columns that should be used for unique slug constraint.
     * Models can override this method to specify scoped uniqueness.
     */
    protected function getSluggableUniqueColumns(): array
    {
        if (method_exists($this, 'sluggableUniqueColumns')) {
            return $this->sluggableUniqueColumns();
        }

        return [];
    }
}

<?php

namespace App\Models;

use App\BaseModel;
use App\Constants\Tables;
use Illuminate\Support\Str;

class MasterTemplate extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Tables::MASTER_TEMPLATES;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'json',
        'preview_image',
        'is_featured',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'json' => 'array',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->slug) && !empty($model->name)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Relationship: Category of this template.
     */
    public function category()
    {
        return $this->belongsTo(MasterTemplateCategory::class, 'category_id');
    }

    /**
     * Scope a query to only include active records.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to only include featured templates.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query by category ID.
     */
    public function scopeByCategory($query, $categoryId)
    {
        if (!empty($categoryId)) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }

    /**
     * Scope a query to order by sort_order and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }
}

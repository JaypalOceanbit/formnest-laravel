<?php

namespace App\Models;

use App\BaseModel;
use App\Constants\Tables;
use Illuminate\Support\Str;

class MasterTemplateCategory extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Tables::MASTER_TEMPLATE_CATEGORIES;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
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
     * Relationship: Templates under this category.
     */
    public function templates()
    {
        return $this->hasMany(MasterTemplate::class, 'category_id');
    }

    /**
     * Scope a query to only include active records.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to order by sort_order and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }
}

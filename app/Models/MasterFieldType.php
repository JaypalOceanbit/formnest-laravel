<?php

namespace App\Models;

use App\BaseModel;
use App\Constants\Tables;

class MasterFieldType extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Tables::MASTER_FIELD_TYPES;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type_key',
        'category',
        'icon',
        'description',
        'default_config',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'default_config' => 'array',
        'sort_order' => 'integer',
        'status' => 'integer',
    ];

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
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Scope a query by component category.
     */
    public function scopeByCategory($query, $category)
    {
        if (!empty($category)) {
            return $query->where('category', $category);
        }
        return $query;
    }
}

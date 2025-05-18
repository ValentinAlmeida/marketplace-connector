<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Import extends Model
{
    use SoftDeletes;

    public const TABLE = 'imports';
    public const STATUS = 'status';
    public const DESCRIPTION = 'description';
    public const PROCESSED_ITEMS = 'processed_items';
    public const TOTAL_ITEMS = 'total_items';
    public const ERROR = 'error';
    public const SCHEDULED_AT = 'scheduled_at';
    public const STARTED_AT = 'started_at';
    public const COMPLETED_AT = 'completed_at';
    public const METADATA = 'metadata';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    protected $table = self::TABLE;

    protected $fillable = [
        self::STATUS,
        self::DESCRIPTION,
        self::PROCESSED_ITEMS,
        self::TOTAL_ITEMS,
        self::ERROR,
        self::SCHEDULED_AT,
        self::STARTED_AT,
        self::COMPLETED_AT,
        self::METADATA
    ];

    protected $casts = [
        self::STATUS => 'string',
        self::SCHEDULED_AT => 'datetime',
        self::STARTED_AT => 'datetime',
        self::COMPLETED_AT => 'datetime',
        self::METADATA => 'array'
    ];
}
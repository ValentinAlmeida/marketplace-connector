<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Eloquent model representing an import record in the database.
 *
 * Corresponds to the 'imports' table and defines mass-assignable attributes,
 * attribute casting, and uses soft deletes.
 */
class Import extends Model
{
    use SoftDeletes;

    /** @var string The name of the database table. */
    public const TABLE = 'imports';
    /** @var string Database column for the import's status. */
    public const STATUS = 'status';
    /** @var string Database column for the import's description. */
    public const DESCRIPTION = 'description';
    /** @var string Database column for the count of successfully processed items. */
    public const PROCESSED_ITEMS = 'processed_items';
    /** @var string Database column for the total number of items in the import. */
    public const TOTAL_ITEMS = 'total_items';
    /** @var string Database column for the count of items that failed to process. */
    public const FAILED_ITEMS = 'failed_items';
    /** @var string Database column for storing the main error message, if any. */
    public const ERROR = 'error';
    /** @var string Database column for the timestamp when the import is scheduled to start. */
    public const SCHEDULED_AT = 'scheduled_at';
    /** @var string Database column for the timestamp when the import process actually started. */
    public const STARTED_AT = 'started_at';
    /** @var string Database column for the timestamp when the import process completed. */
    public const COMPLETED_AT = 'completed_at';
    /** @var string Database column for storing additional metadata (usually as JSON). */
    public const METADATA = 'metadata';
    /** @var string The name of the "created at" column. */
    public const CREATED_AT = 'created_at';
    /** @var string The name of the "updated at" column. */
    public const UPDATED_AT = 'updated_at';
    /** @var string The name of the "deleted at" column for soft deletes. */
    public const DELETED_AT = 'deleted_at';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = self::TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::STATUS,
        self::DESCRIPTION,
        self::PROCESSED_ITEMS,
        self::TOTAL_ITEMS,
        self::FAILED_ITEMS,
        self::ERROR,
        self::SCHEDULED_AT,
        self::STARTED_AT,
        self::COMPLETED_AT,
        self::METADATA
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        self::STATUS => 'string',
        self::SCHEDULED_AT => 'datetime',
        self::STARTED_AT => 'datetime',
        self::COMPLETED_AT => 'datetime',
        self::METADATA => 'array'
    ];
}
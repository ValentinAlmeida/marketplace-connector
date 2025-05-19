<?php

return [
    'per_page' => env('IMPORTER_PER_PAGE', 100),
    'max_retries' => env('IMPORTER_MAX_RETRIES', 3),
    'retry_delay_ms' => env('IMPORTER_RETRY_DELAY_MS', 100),
    'marketplace_cache_ttl' => env('IMPORTER_CACHE_TTL', 60),
];

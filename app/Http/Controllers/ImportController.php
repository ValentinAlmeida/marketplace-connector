<?php

namespace App\Http\Controllers;

use App\Domain\Import\Services\ImportServiceInterface;
use App\Http\Requests\ImportCreateRequest;
use App\Http\Serializers\ImportSerializer;
use Illuminate\Http\JsonResponse;

/**
 * Class ImportController
 *
 * Handles HTTP requests related to import operations.
 */
class ImportController extends Controller
{
    /**
     * Constructor.
     *
     * @param ImportServiceInterface $service
     * @param ImportSerializer $serializer
     */
    public function __construct(
        private ImportServiceInterface $service,
        private ImportSerializer $serializer
    ) {}

    /**
     * Store a newly created import in storage.
     *
     * @param ImportCreateRequest $request
     * @return JsonResponse
     */
    public function store(ImportCreateRequest $request): JsonResponse
    {
        $import = $this->service->createImport($request->getDto());
        return response()->json($this->serializer->toArray($import), 201);
    }

    /**
     * Display the specified import.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $import = $this->service->findImport($id);
        return response()->json($this->serializer->toArray($import));
    }
}

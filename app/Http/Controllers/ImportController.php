<?php

namespace App\Http\Controllers;

use App\Domain\Import\Services\ImportServiceInterface;
use App\Http\Requests\ImportCreateRequest;
use App\Http\Serializers\ImportSerializer;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{
    public function __construct(
        private ImportServiceInterface $service,
        private ImportSerializer $serializer
    ) {}

    public function store(ImportCreateRequest $request): JsonResponse
    {
        $import = $this->service->createImport($request->getDto());
        return response()->json($this->serializer->toArray($import), 201);
    }

    public function show(string $id): JsonResponse
    {
        $import = $this->service->getImportStatus($id);
        return response()->json($this->serializer->toArray($import));
    }
}
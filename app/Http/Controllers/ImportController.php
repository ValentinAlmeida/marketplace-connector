<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportCreateRequest;
use App\Http\Serializers\ImportSerializer;
use App\UseCase\Contracts\Import\IImportProcessor;
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
        private IImportProcessor $service,
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
        $this->service->createImport($request->getDto());
        return response()->json("Importação agendada com sucesso!", 201);
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

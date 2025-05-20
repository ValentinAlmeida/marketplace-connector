<?php

namespace App\Http\Requests;

use App\Constants\Format;
use App\Domain\Import\Dto\ImportCreateDto;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ImportCreateRequest
 *
 * Handles validation and transformation of import creation input.
 */
class ImportCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if authorized
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for the request.
     *
     * @return array Validation rules
     */
    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date_format:' . Format::DATE_TIME, 'after_or_equal:now']
        ];
    }

    /**
     * Transform the request data into a DTO.
     *
     * @return ImportCreateDto The DTO containing validated request data
     */
    public function getDto(): ImportCreateDto
    {
        return new ImportCreateDto(
            description: $this->input('description'),
            scheduledAt: $this->filled('scheduled_at') 
                ? Carbon::createFromFormat(Format::DATE_TIME, $this->input('scheduled_at'))
                : null
        );
    }
}

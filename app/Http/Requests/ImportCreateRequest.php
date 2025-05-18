<?php

namespace App\Http\Requests;

use App\Constants\Format;
use App\Domain\Import\Dto\ImportCreateDto;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ImportCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date_format:' . Format::DATE_TIME, 'after_or_equal:now']
        ];
    }

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
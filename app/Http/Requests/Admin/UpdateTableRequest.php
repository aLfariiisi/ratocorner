<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tableId = $this->route('table')?->id;

        return [
            'table_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tables', 'table_number')->ignore($tableId),
            ],
            'name' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:available,occupied,reserved'],
            'is_active' => ['boolean'],
        ];
    }
}

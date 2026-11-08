<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PropertyStatus;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'external_code' =>  ['required', 'string', 'max:50', 'unique:properties,external_code'],
            'property_type_id' => ['required', 'integer', 'exists:property_types,id'],
            'status' => ['sometimes', Rule::enum(PropertyStatus::class)],
            'price' => ['nullable','numeric', 'min:0', 'max:9999999999.99'],
            'area_m2' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:255'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:255'],
            'garage_spaces' => ['nullable', 'integer', 'min:0', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}

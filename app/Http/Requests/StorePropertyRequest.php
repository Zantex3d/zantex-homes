<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('create_property');
    }

    public function rules(): array
    {
        return [
            'estate_id' => 'required|exists:estates,id',
            'property_code' => 'required|unique:properties|string|max:50',
            'name' => 'required|string|max:255',
            'property_type_id' => 'required|exists:property_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'property_code.unique' => 'This property code is already in use.',
            'estate_id.exists' => 'The selected estate does not exist.',
        ];
    }
}

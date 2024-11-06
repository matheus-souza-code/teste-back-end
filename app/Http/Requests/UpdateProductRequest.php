<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'price' => ['nullable', 'numeric', 'between:0,999999.99'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['nullable', 'exists:categories,name'],
            'image_url' => ['nullable', 'url', 'max:2048'],
        ];
    }
}

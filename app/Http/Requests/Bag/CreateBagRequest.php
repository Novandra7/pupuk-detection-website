<?php

namespace App\Http\Requests\Bag;

use Illuminate\Foundation\Http\FormRequest;

class CreateBagRequest extends FormRequest
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
            'bag_type'  => ['required','string'],
            'weight_in_kilogram'  => ['required','integer','min:1','max:9999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
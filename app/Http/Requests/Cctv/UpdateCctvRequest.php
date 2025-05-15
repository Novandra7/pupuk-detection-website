<?php

namespace App\Http\Requests\Cctv;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCctvRequest extends FormRequest
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
            'ms_warehouse_id'  => ['required','integer'],
            'source_name'  => ['required','string'],
            'url_streaming'  => ['required','string'],
        ];
    }
}
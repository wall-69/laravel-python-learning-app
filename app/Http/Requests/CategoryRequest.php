<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Str;

class CategoryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|max:255",
            "description" => "required|max:255",
            "slug" => "required|max:255"
        ];
    }


    public function prepareForValidation(): void
    {
        if ($this->has("title")) {
            $this->merge([
                "slug" => Str::slug($this->input("title"))
            ]);
        }
    }
}

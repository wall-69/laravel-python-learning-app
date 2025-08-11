<?php

namespace App\Http\Requests;

use App\Enums\LectureStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Str;

class LectureRequest extends FormRequest
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
        $lecture = $this->route("lecture");

        return [
            "title" => "required|max:255",
            "description" => "required|max:255",
            "slug" => ["required", "max:255", Rule::unique("lectures", "slug")->ignore($lecture?->id)],
            "status" => ["required", Rule::enum(LectureStatus::class)],
            "blocks" => "required|json"
        ];
    }

    public function prepareForValidation(): void
    {
        // Create slug
        if ($this->has("title")) {
            $this->merge([
                "slug" => Str::slug($this->input("title"))
            ]);
        }
    }
}

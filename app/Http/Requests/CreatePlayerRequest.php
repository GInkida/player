<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PlayerPosition;
use App\Enums\PlayerSkill;
use Illuminate\Validation\Rule;

class CreatePlayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Assuming all authenticated users can create a player
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'position' => ['required', Rule::in(PlayerPosition::cases())],
            'playerSkills' => 'required|array|min:1',
            'playerSkills.*.skill' => ['required', Rule::in(PlayerSkill::cases())],
            'playerSkills.*.value' => 'required|integer|min:0|max:100',
        ];
    }
}

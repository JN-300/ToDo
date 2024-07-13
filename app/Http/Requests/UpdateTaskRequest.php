<?php

namespace App\Http\Requests;

use App\Enums\TaskStatusEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
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
        $minDate = Carbon::now()
            ->modify('+ 30minutes')
            ->format('Y-m-d H:i:s');
        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'sometimes',
                'required',
                'string'
            ],
            'deadline' => [
                'sometimes',
                'required',
                'date',
                'after:'.$minDate
            ],
            'status' => [
                'sometimes',
                'required',
                Rule::enum(TaskStatusEnum::class)
            ],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\TaskStatusEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
        $rules =  [
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
                'date'
            ],
            'status' => [
                'sometimes',
                'required',
                Rule::enum(TaskStatusEnum::class)
            ],
        ];

        if (!Auth::user()->isAdmin()){
            $rules['deadline'][] = 'after:'.$minDate;
        }

        return $rules;
    }
}

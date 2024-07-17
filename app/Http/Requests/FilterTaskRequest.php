<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('with')){
            $relations = $this->get('with');
            if (!is_array($relations))
            {
                $relations = explode(',', $relations);
                array_walk($relations, function(&$item) { $item =  trim($item);});
                $this->merge([
                    'with' => $relations
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filter' => 'sometimes|array',
            'filter.overdue' => 'sometimes|boolean',
            'filter.users' => 'sometimes|array',
            'filter.users.*' => [
                'sometimes',
                Rule::exists('users', 'id')
            ],
            'filter.projects' => 'sometimes|array',
            'filter.projects.*' => [
                'sometimes',
                'uuid',
                Rule::exists('projects', 'id')
            ],
            'with' => 'sometimes|array',
            'with.*' => Rule::in(['project','owner'])
        ];
    }
}

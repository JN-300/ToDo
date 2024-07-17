<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterProjectRequest extends FormRequest
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
            'limit' => 'sometimes|integer',
            'with' => 'sometimes|array',
            'with.*' => Rule::in(['tasks'])
        ];
    }
}

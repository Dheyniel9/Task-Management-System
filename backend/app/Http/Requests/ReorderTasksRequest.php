<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReorderTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tasks' => ['required', 'array'],
            'tasks.*' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'tasks.required' => 'Task order data is required.',
            'tasks.array' => 'Task order must be an array.',
            'tasks.*.integer' => 'Each task order must be a number.',
        ];
    }

    /**
     * Additional validation to ensure task IDs belong to the user.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('tasks')) {
                $taskIds = array_keys($this->tasks);
                $userTaskIds = $this->user()->tasks()->pluck('id')->toArray();

                foreach ($taskIds as $taskId) {
                    if (!in_array($taskId, $userTaskIds)) {
                        $validator->errors()->add(
                            'tasks',
                            'You can only reorder your own tasks.'
                        );
                        break;
                    }
                }
            }
        });
    }
}

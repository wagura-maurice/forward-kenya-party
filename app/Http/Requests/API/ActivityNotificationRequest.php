<?php

namespace App\Http\Requests\API;

use App\Models\ActivityNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the controller and policy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'activity_id' => ['required', 'exists:activities,id'],
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'data' => ['sometimes', 'array'],
            '_status' => ['sometimes', 'integer', Rule::in([
                ActivityNotification::PENDING,
                ActivityNotification::SENT,
                ActivityNotification::FAILED,
                ActivityNotification::READ,
            ])],
            'error_message' => ['nullable', 'string', 'max:1000'],
            'metadata' => ['sometimes', 'array'],
        ];

        // For update requests, make fields optional
        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            $rules = array_map(function ($rule) {
                // Convert required to sometimes for updates
                if (($key = array_search('required', $rule)) !== false) {
                    unset($rule[$key]);
                    $rule[] = 'sometimes';
                }
                return $rule;
            }, $rules);
        }

        return $rules;
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Set the user_id to the authenticated user if not provided
        if (!$this->has('user_id') && $this->user()) {
            $this->merge([
                'user_id' => $this->user()->id,
            ]);
        }
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'activity_id.required' => 'The activity ID is required.',
            'activity_id.exists' => 'The selected activity does not exist.',
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'title.required' => 'The notification title is required.',
            'message.required' => 'The notification message is required.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}

<?php

namespace App\Http\Requests\MasterData;

use App\Models\CompanySetting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $companyId = CompanySetting::getSettings()->id;
        $branchId = $this->route('branch') ? $this->route('branch')->id : null;

        return [
            'branch_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('branches', 'branch_code')
                    ->where('company_id', $companyId)
                    ->ignore($branchId),
            ],
            'branch_name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'is_main_branch' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'assigned_staff' => ['nullable', 'array'],
            'assigned_staff.*' => ['exists:users,id'],
        ];
    }

    /**
     * Custom attribute names for clear error messages.
     */
    public function attributes(): array
    {
        return [
            'branch_code' => 'Branch Code',
            'branch_name' => 'Branch Name',
            'address' => 'Address',
            'phone' => 'Contact Number',
            'email' => 'Email Address',
            'manager_id' => 'Branch Manager',
            'is_main_branch' => 'Main Branch Flag',
            'is_active' => 'Status',
            'assigned_staff' => 'Assigned Staff',
        ];
    }
}

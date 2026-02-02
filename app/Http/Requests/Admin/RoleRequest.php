<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('id')
            ? decrypt($this->route('id'))
            : null;

        return [

            // Role name
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'admin')
                    ->ignore($roleId),
            ],

            // Permissions
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,id'],

        ];
    }

    public function messages(): array
    {
        return [

            // Name
            'name.required' => 'Role name is required.',
            'name.string'   => 'Role name must be valid text.',
            'name.max'      => 'Role name may not exceed 100 characters.',
            'name.unique'   => 'This role name already exists.',

            // Permissions
            'permissions.array' => 'Invalid permissions format.',
            'permissions.*.exists' => 'One or more selected permissions are invalid.',
        ];
    }
}

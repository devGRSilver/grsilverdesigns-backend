<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $couponId = $this->route('coupon') ? decrypt($this->route('coupon')) : null;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('coupons', 'code')->ignore($couponId)
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => ['required', Rule::in(['percentage', 'fixed_amount'])],
            'value' => 'required|numeric|min:0.01',
            'usage_limit' => 'nullable|integer|min:1',
            'user_limit' => 'nullable|integer|min:1',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'min_items' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'status' => 'boolean',
            'first_order_only' => 'boolean',
            'free_shipping' => 'boolean',
            'included_categories' => 'nullable|array',
            'included_categories.*' => 'exists:categories,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'This coupon code already exists.',
            'code.regex' => 'Coupon code can only contain uppercase letters, numbers, hyphens, and underscores.',
            'value.min' => 'The discount value must be at least 0.01.',
            'expires_at.after' => 'Expiry date must be after start date.',
            'included_products.*.exists' => 'One or more selected products do not exist.',
            'included_categories.*.exists' => 'One or more selected categories do not exist.',
            'included_users.*.exists' => 'One or more selected users do not exist.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->code) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }
    }
}

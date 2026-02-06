<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // $this->merge([
        //     'category_id' => $this->category_id ? decrypt($this->category_id) : null,
        // ]);
    }

    public function rules(): array
    {
        $productId = $this->route('id') ? decrypt($this->route('id')) : null;
        $isUpdate  = in_array($this->method(), ['PUT', 'PATCH']);

        $rules = [
            'category_id'      => 'required|integer|exists:categories,id',
            'product_name'     => 'required|string|max:255',
            'deleted_variants' => 'nullable|array',
            'deleted_variants.*' => 'integer|exists:product_variants,id',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],

            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($productId),
            ],

            /* -------------------------
             | IMAGES
             -------------------------*/
            'main_image' => [
                $isUpdate ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:min_width=300,min_height=300',
            ],

            'secondary_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'seo_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            /* -------------------------
             | DESCRIPTION
             -------------------------*/
            'short_description' => 'required|string',
            'description'       => 'required|string',

            /* -------------------------
             | STATUS
             -------------------------*/
            'tax_percentage'          => 'nullable|max:200',
            'status'          => 'required|boolean',
            'personalize'     => 'required|boolean',
            'is_featured'     => 'required|boolean',
            'marketing_label' => 'nullable|in:new,trending,hot,sale,limited,exclusive,popular,top_rated',

            /* -------------------------
             | SEO
             -------------------------*/
            'seo_title'       => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_keywords'    => 'nullable|array',
            'seo_keywords.*'  => 'string|max:50',
            'parent_mrp_price' => 'required|numeric|min:1',
            'parent_selling_price' => [
                'required',
                'numeric',
                'min:1',
                'lt:parent_mrp_price',
            ],
            'parent_cost_price' => [
                'nullable',
                'numeric',
                'min:1',
                'lt:parent_mrp_price',
            ],

            // Fixed: Added proper variant validation
            'variants' => 'required|array|min:1',
            'variants.*.id'   => 'nullable|integer|exists:product_variants,id',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.sku'  => [
                'required',
                'string',
                'max:100',
                'distinct',
            ],


            'variants.*.mrp_price' => 'required|numeric|min:1',
            'variants.*.selling_price' => [
                'required',
                'numeric',
                'min:1',
                'lt:variants.*.mrp_price', // Fixed: Compare with variant's own mrp_price
            ],
            'variants.*.cost_price' => [
                'required',
                'numeric',
                'min:1',
                'lt:variants.*.selling_price', // Fixed: Compare with variant's own mrp_price
            ],

            'variants.*.quantity' => 'required|integer|min:0',
            'variants.*.weight' => 'required|numeric|min:0',


            'variants.*.images'   => 'nullable',
            'variant_options' => 'required',
            'variant_options.*.name' => 'required|string|max:50',
            'variant_options.*.values' => 'required|array|min:1',
            'variant_options.*.values.*' => 'string|max:50',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            // Fixed: Correct variant validation messages
            'variants.required' => 'At least one variant is required.',
            'variants.min' => 'At least one variant is required.',

            'variants.*.selling_price.lt' =>
            'Variant selling price must be less than variant MRP price.',
            'parent_selling_price.lt' =>
            'Parent selling price must be less than MRP price.',

            'main_image.required' =>
            'Main product image is required.',
            'main_image.dimensions' =>
            'Main image must be at least 300x300 pixels.',

            // Added variant-specific messages
            'variants.*.sku.distinct' =>
            'Variant SKU must be unique across all variants.',
            'variants.*.sku.required' =>
            'Variant SKU is required.',
            'variants.*.name.required' =>
            'Variant name is required.',
            'variants.*.mrp_price.required' =>
            'Variant MRP price is required.',
            'variants.*.selling_price.required' =>
            'Variant selling price is required.',
            'variants.*.quantity.required' =>
            'Variant quantity is required.',
            'variants.*.weight.required' =>
            'Variant weight is required.',
        ];
    }

    public function attributes(): array
    {
        return [
            'product_name' => 'product name',
            'main_image' => 'main image',
            'secondary_image' => 'secondary image',
            'seo_image' => 'SEO image',
            'short_description' => 'short description',
            'description' => 'description',

            // Variant attributes
            'variants' => 'variants',
            'variants.*.name' => 'variant name',
            'variants.*.sku' => 'variant SKU',
            'variants.*.mrp_price' => 'variant MRP price',
            'variants.*.selling_price' => 'variant selling price',
            'variants.*.quantity' => 'variant quantity',
            'variants.*.weight' => 'variant weight',

            // Variant options
            'variant_options' => 'variant options',
            'variant_options.*.name' => 'option name',
            'variant_options.*.values' => 'option values',
        ];
    }
}

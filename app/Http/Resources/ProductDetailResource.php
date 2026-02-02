<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $attributes = [];
        $variants = [];

        foreach ($this->variants as $variant) {

            $variantAttributes = [];

            foreach ($variant->attributeValues as $value) {

                $attrName  = $value->attribute->name;
                $attrValue = $value->value;

                // Collect attributes for dropdown
                $attributes[$attrName][$attrValue] = $attrValue;

                // Variant attribute mapping
                $variantAttributes[$attrName] = $attrValue;
            }

            $variants[] = [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'selling_price' => (float) $variant->selling_price,
                'stock' => $variant->stock_quantity,
                'status' => $variant->stock_status,
                'attributes' => $variantAttributes,
            ];
        }

        // Convert attribute values to indexed arrays
        foreach ($attributes as $key => $values) {
            $attributes[$key] = array_values($values);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'product_type' => $this->product_type,

            'main_image' => $this->main_image,
            'secondary_image' => $this->secondary_image,
            'short_description' => $this->short_description,
            'description' => $this->description,

            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],

            'sub_category' => [
                'id' => $this->subCategory?->id,
                'name' => $this->subCategory?->name,
                'slug' => $this->subCategory?->slug,
            ],

            // 👇 FRONTEND CORE DATA
            'attributes' => $attributes,
            'variants' => $variants,
        ];
    }
}

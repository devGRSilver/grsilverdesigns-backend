<?php

namespace App\Observers;

use App\Constants\Constant;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

class CategoryObserver
{
    public function updated(Category $category)
    {
        if (!$category->wasChanged('status')) return;
        if ((int)$category->status === Constant::IN_ACTIVE) {
            $this->categoryStatusUpdate($category->id, Constant::IN_ACTIVE);
        } else {
            $this->categoryStatusUpdate($category->id, Constant::ACTIVE);
        }
    }

    /**
     * Recursively set category, products, and variants status
     */
    private function categoryStatusUpdate(int $categoryId, int $status): void
    {
        Category::where('id', $categoryId)->update(['status' => $status]);
        $productIds = Product::where('category_id', $categoryId)->pluck('id');
        if ($productIds->isNotEmpty()) {
            Product::whereIn('id', $productIds)->update(['status' => $status]);
            ProductVariant::whereIn('product_id', $productIds)->update(['status' => $status]);
        }
        $childCategoryIds = Category::where('parent_id', $categoryId)->pluck('id');
        foreach ($childCategoryIds as $childId) {
            $this->categoryStatusUpdate($childId, $status);
        }
    }
}

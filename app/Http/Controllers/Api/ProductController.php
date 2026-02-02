<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::query()
                ->with([
                    'category',
                    'variants' => function ($q) {
                        $q->where('stock_quantity', '>', 0);
                    }
                ])
                ->where(['status' => 1, 'id' => 1]);

            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('sub_category_id')) {
                $query->where('sub_category_id', $request->sub_category_id);
            }

            if ($request->has('is_featured')) {
                $query->where('is_featured', (bool) $request->is_featured);
            }

            if ($request->filled('min_price')) {
                $query->whereHas('variants', function ($q) use ($request) {
                    $q->where('selling_price', '>=', $request->min_price);
                });
            }

            if ($request->filled('max_price')) {
                $query->whereHas('variants', function ($q) use ($request) {
                    $q->where('selling_price', '<=', $request->max_price);
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Sorting (Safe)
            |--------------------------------------------------------------------------
            */
            $allowedSorts = ['created_at', 'product_name', 'id'];
            $sortBy = in_array($request->get('sort_by'), $allowedSorts)
                ? $request->get('sort_by')
                : 'created_at';

            $sortOrder = $request->get('sort_order') === 'asc' ? 'asc' : 'desc';

            $query->orderBy($sortBy, $sortOrder);

            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */
            $perPage = (int) $request->get('per_page', 20);
            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Products fetched successfully',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($slug)
    {
        $product = Product::with([
            'category',
            'subCategory',
            'variants.attributeValues.attribute',
        ])
            ->where('slug', $slug)
            ->where('status', 1)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product details fetched successfully',
            'data' => new ProductDetailResource($product)
        ]);
    }
}

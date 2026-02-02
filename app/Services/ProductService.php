<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantAttributeCombination;
use App\Models\AttributeValue;
use App\Models\TempImage;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;


class ProductService
{
    protected $module = 'products';

    public function getDataForDataTable($request)
    {
        $columns = ['id', 'image', 'name', 'category', 'total_variant', 'status', 'created_at'];

        $query = Product::with('category')->withCount('variants');

        /** SEARCH */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        /** FILTERS */
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->filled('stock_quantity')) {
            $stock_quantity = $request->stock_quantity;
            $query->whereHas('variants', function ($q) use ($stock_quantity) {
                if ($stock_quantity == '1') { // In stock
                    $q->where('stock_quantity', '>', 0);
                } elseif ($stock_quantity == '0') { // Out of stock
                    $q->where('stock_quantity', '<=', 0);
                }
            });
        }

        if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
            $query->where('status', $request->status);
        }

        /** ORDER */
        $orderCol = $columns[$request->input('order.0.column', 0)] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderCol, $orderDir);

        /** Get total filtered count before pagination */
        $recordsFiltered = (clone $query)->count();

        /** PAGINATION */
        $datas = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $datas->map(function ($product, $index) use ($request) {
            // Check permissions for this user
            $canView = checkPermission('products.view');
            $canEdit = checkPermission('products.update');
            $canDelete = checkPermission('products.delete');
            $canUpdateStatus = checkPermission('products.update.status');

            // Product name with link (only if can view)
            $productName = $product->name;
            if ($canView) {
                $productName = redirect_to_link(route($this->module . '.show', encrypt($product->id)), $product->name);
            }

            // Category with link (only if can view categories)
            $categoryName = $product->category ? $product->category->name : 'N/A';
            if ($product->category && checkPermission('categories.view')) {
                $categoryName = redirect_to_link(route('categories.index'), $product->category->name);
            }

            // Variants count with link (only if can view products)
            $variantsHtml = $product->variants_count;
            if ($canView && $product->variants_count > 0) {
                $variantsHtml = '<a href="' . route('products.variants', encrypt($product->id)) . '" class="modal_open">' . $product->variants_count . '</a>';
            }

            // Status dropdown (only if can update status)
            $statusHtml = $product->status
                ? '<span class="badge bg-label-success">Active</span>'
                : '<span class="badge bg-label-danger">Inactive</span>';

            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($product->status, [
                    'id'     => $product->id,
                    'url'    => route($this->module . '.status', encrypt($product->id)),
                    'method' => 'PUT',
                ]);
            }

            $actionButtons = [];

            if ($canEdit) {
                $actionButtons[] = btn_edit(route($this->module . '.edit', encrypt($product->id)), false);
            }

            if ($canView) {
                $actionButtons[] = btn_view(route($this->module . '.show', encrypt($product->id)), false);
            }

            if ($canDelete) {
                $actionButtons[] = btn_delete(route($this->module . '.delete', encrypt($product->id)), true);
            }

            return [
                'id'            => $request->start + $index + 1,
                'image'         => image_show($product->main_image, 50, 50),
                'product_name'  => $productName,
                'category'      => $categoryName,
                'total_variant' => $variantsHtml,
                'status'        => $statusHtml,
                'created_at'    => $product->created_at->format('d M y'),
                'action'        => !empty($actionButtons) ? button_group($actionButtons) : 'No actions',
            ];
        });

        return [
            'draw'            => intval($request->draw),
            'recordsTotal'    => Product::count(),
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }


    /**********************************************
     * Find Product by ID
     **********************************************/
    public function findById($id)
    {
        return Product::with([
            'category',
            'variants',
            'variants.images',
            'variants.attributeValues.attribute'
        ])
            ->findOrFail($id);
    }

    public function getVariants($id)
    {
        return ProductVariant::with(['attributeValues.attribute'])
            ->where('product_id', $id)
            ->get();
    }

    /**********************************************
     * CREATE PRODUCT (WITH ATTRIBUTE COMBINATIONS)
     **********************************************/
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {

            $slug        = $this->generateSlug($data['product_name']);
            $productType = 'with_variant';

            $variantOptions = !empty($data['variant_options'])
                ? json_decode($data['variant_options'], true)
                : [];

            $seoKeywords = $this->prepareSeoKeywords($data['seo_keywords'] ?? null);

            $minPrice = $maxPrice = null;
            $stockStatus = 'in_stock';

            if (!empty($data['variants']) && is_array($data['variants'])) {
                $variantPrices = array_column($data['variants'], 'selling_price');

                if (!empty($variantPrices)) {
                    $minPrice = min($variantPrices);
                    $maxPrice = max($variantPrices);
                }
            }

            $product = Product::create([
                'category_id'       => $data['category_id'],
                'sub_category_id'   => $data['sub_category_id'] ?? null,
                'metal_id'          => $data['metal_id'] ?? null,

                'product_type'      => $productType,
                'name'              => $data['product_name'],
                'slug'              => $slug,
                'sku'               => $data['sku'],

                'cost_price'        => $data['parent_cost_price'] ?? null,
                'mrp_price'         => $data['parent_mrp_price'] ?? null,
                'selling_price'     => $data['parent_selling_price'] ?? null,
                'tax_percentage'    => $data['tax_percentage'] ?? 0,

                'stock_status'      => $stockStatus,

                'min_price'         => $minPrice,
                'max_price'         => $maxPrice,

                'personalize'       => $data['personalize'] ?? false,
                'marketing_label'   => $data['marketing_label'] ?? null,
                'is_featured'       => $data['is_featured'] ?? false,
                'status'            => $data['status'] ?? Constant::ACTIVE,

                'description'       => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,

                'seo_title'         => $data['seo_title'] ?? $data['product_name'],
                'seo_description'   => Str::limit($data['seo_description'] ?? '', 160),
                'seo_keywords'      => $seoKeywords,

                // Images
                'main_image'        => !empty($data['main_image'])
                    ? $this->uploadImage($data['main_image'], 'uploads/products/main', 1200, 1200)
                    : null,

                'secondary_image'   => !empty($data['secondary_image'])
                    ? $this->uploadImage($data['secondary_image'], 'uploads/products/main', 1200, 1200)
                    : null,

                'seo_image'         => !empty($data['seo_image'])
                    ? $this->uploadImage($data['seo_image'], 'uploads/products/seo', 800, 800)
                    : null,

                'variant_attributes' => !empty($variantOptions)
                    ? json_encode($variantOptions)
                    : null,
            ]);

            if (!empty($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as &$variant) {
                    $variant['tax_percentage'] = $data['tax_percentage'] ?? 0;
                }

                $this->insertVariantsWithAttributes(
                    $product->id,
                    $data['variants'],
                    $variantOptions
                );
            }

            return $product;
        });
    }



    private function insertVariantsWithAttributes(int $productId, array $variants, array $variantOptions): void
    {
        $variantRows = [];
        $attributeCombinationRows = [];
        $now = now();

        /** ----------------------------
         * 1. PREPARE VARIANT DATA
         * ---------------------------- */
        foreach ($variants as $index => $variant) {
            $quantity = $variant['quantity'] ?? 0;
            $stockStatus = $quantity > 0 ? 'in_stock' : 'out_of_stock';

            $variantRows[] = [
                'product_id'     => $productId,
                'variant_name'   => $variant['name'] ?? 'Default',
                'sku'            => $variant['sku'],
                'mrp_price'      => $variant['mrp_price'] ?? $variant['price'] ?? 0,
                'selling_price'  => $variant['selling_price'] ?? $variant['price'] ?? 0,
                'cost_price'     => $variant['cost_price'] ?? null,
                'weight'         => $variant['weight'] ?? 0,
                'tax_percentage' => $variant['tax_percentage'] ?? $variant['tax'] ?? 0,
                'stock_quantity' => $quantity,
                'is_default'     => $index === 0, // First variant as default
                'stock_status'   => $stockStatus,
                'status'         => Constant::ACTIVE,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        ProductVariant::insert($variantRows);

        $variantIds = ProductVariant::where('product_id', $productId)
            ->orderBy('id')
            ->pluck('id', 'sku')
            ->toArray();

        /** ----------------------------
         * 2. ATTRIBUTE COMBINATIONS
         * ---------------------------- */
        foreach ($variants as $variant) {
            $variantId = $variantIds[$variant['sku']] ?? null;
            if (!$variantId) continue;

            $valueParts = explode(' / ', $variant['name'] ?? '');

            foreach ($variantOptions as $optIndex => $option) {
                $attributeId = $option['attribute_id'];
                $valueText = $valueParts[$optIndex] ?? null;

                if ($valueText) {
                    $attributeValue = AttributeValue::firstOrCreate(
                        ['attribute_id' => $attributeId, 'value' => trim($valueText)],
                        ['created_at' => $now, 'updated_at' => $now]
                    );

                    $attributeCombinationRows[] = [
                        'variant_id'         => $variantId,
                        'attribute_id'       => $attributeId,
                        'attribute_value_id' => $attributeValue->id,
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ];
                }
            }
        }

        if (!empty($attributeCombinationRows)) {
            VariantAttributeCombination::insert($attributeCombinationRows);
        }

        /** ----------------------------
         * 3. VARIANT IMAGES
         * ---------------------------- */
        foreach ($variants as $variant) {
            $variantId = $variantIds[$variant['sku']] ?? null;
            if (!$variantId || empty($variant['images'])) continue;

            $this->processVariantImages($productId, $variantId, $variant['images']);
        }
    }




    /**********************************************
     * EDIT / UPDATE PRODUCT
     **********************************************/
    public function update(int $productId, array $data): Product
    {
        return DB::transaction(function () use ($productId, $data) {
            $product = Product::findOrFail($productId);
            $slug = $this->generateSlug($data['product_name'], $productId);

            $variantOptions = json_decode($data['variant_options'] ?? '[]', true);
            $seoKeywords = $this->prepareSeoKeywords($data['seo_keywords'] ?? null);

            $updateData = [
                'category_id'       => $data['category_id'],
                'sub_category_id'   => $data['sub_category_id'] ?? null,
                'metal_id'          => $data['metal_id'] ?? null,
                'name'              => $data['product_name'],
                'slug'              => $slug,
                'sku'               => $data['sku'],
                'marketing_label'             => $data['marketing_label'] ?? null,
                'is_featured'       => $data['is_featured'] ?? 0,
                'status'            => $data['status'] ?? Constant::ACTIVE,
                'description'       => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'seo_title'         => $data['seo_title'] ?? $data['product_name'],
                'seo_description'   => Str::limit($data['seo_description'] ?? '', 160),
                'seo_keywords'      => $seoKeywords,
                'variant_config'    => !empty($variantOptions) ? json_encode($variantOptions) : null,
            ];

            /** ----------------------------
             * UPDATE IMAGES
             * ---------------------------- */
            if (!empty($data['main_image'])) {
                $this->deleteFile($product->main_image);
                $updateData['main_image'] = $this->uploadImage($data['main_image'], 'uploads/products', 1200, 1200);
            }

            if (!empty($data['secondary_image'])) {
                $this->deleteFile($product->secondary_image);
                $updateData['secondary_image'] = $this->uploadImage($data['secondary_image'], 'uploads/products', 1200, 1200);
            }

            if (!empty($data['seo_image'])) {
                $this->deleteFile($product->seo_image);
                $updateData['seo_image'] = $this->uploadImage($data['seo_image'], 'uploads/seo', 800, 800);
            }

            $product->update($updateData);

            /** ----------------------------
             * UPDATE VARIANTS
             * ---------------------------- */


            if (isset($data['variants'])) {
                $this->updateVariantsWithAttributes($productId, $data['variants'], $variantOptions);
            }

            if (isset($data['deleted_variants']) && !empty($data['deleted_variants'])) {
                ProductVariant::whereIn('id', $data['deleted_variants'])->delete();
            }

            return $product->fresh(['variants.images', 'variants.attributeValues.attribute']);
        });
    }

    /**********************************************
     * UPDATE VARIANTS + ATTRIBUTE COMBINATIONS + IMAGES
     **********************************************/
    private function updateVariantsWithAttributes(int $productId, array $variants, array $variantOptions): void
    {
        $existingVariants = ProductVariant::where('product_id', $productId)
            ->get()
            ->keyBy('sku');

        $now = now();

        foreach ($variants as $variant) {
            $variantSku = $variant['sku'];

            if (isset($existingVariants[$variantSku])) {
                $existingVariant = $existingVariants[$variantSku];
                $existingVariant->update([
                    'variant_name'   => $variant['name'] ?? 'Default',
                    'weight'         => $variant['weight'] ?? 0,
                    'cost_price'          => $variant['cost_price'] ?? 0,
                    'mrp_price'          => $variant['mrp_price'] ?? 0,
                    'selling_price'  => $variant['selling_price'] ?? $variant['price'] ?? 0,
                    'tax_percentage'            => $variant['tax_percentage'] ?? 0,
                    'stock_quantity' => $variant['quantity'] ?? 0,
                    'stock_status'   => ($variant['quantity'] ?? 0) > 0 ? Constant::IN_STOCK : Constant::OUT_OF_STOCK,
                    'updated_at'     => $now,
                ]);

                $variantId = $existingVariant->id;
            } else {
                $newVariant = ProductVariant::create([
                    'product_id'     => $productId,
                    'variant_name'   => $variant['name'] ?? 'Default',
                    'sku'            => $variantSku,
                    'weight'         => $variant['weight'] ?? 0,
                    'cost_price'          => $variant['cost_price'] ?? 0,
                    'mrp_price'          => $variant['mrp_price'] ?? 0,
                    'selling_price'  => $variant['selling_price'] ?? $variant['price'] ?? 0,
                    'tax_percentage'            => $variant['tax_percentage'] ?? 0,
                    'stock_quantity' => $variant['quantity'] ?? 0,
                    'stock_status'   => ($variant['quantity'] ?? 0) > 0 ? Constant::IN_STOCK : Constant::OUT_OF_STOCK,
                    'status'         => Constant::ACTIVE,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);


                $variantId = $newVariant->id;
            }

            /** ----------------------------
             * ATTRIBUTE COMBINATIONS
             * ---------------------------- */
            // VariantAttributeCombination::where('variant_id', $variantId)->delete();

            if (!empty($variant['name']) && !empty($variantOptions)) {
                $valueParts = explode(' / ', $variant['name']);
                foreach ($variantOptions as $optIndex => $option) {
                    $attributeId = $option['attribute_id'];
                    $valueText = $valueParts[$optIndex] ?? null;

                    if ($valueText) {
                        $attributeValue = AttributeValue::firstOrCreate(
                            ['attribute_id' => $attributeId, 'value' => trim($valueText)],
                            ['created_at' => $now, 'updated_at' => $now]
                        );

                        VariantAttributeCombination::create([
                            'variant_id'          => $variantId,
                            'attribute_id'        => $attributeId,
                            'attribute_value_id'  => $attributeValue->id,
                            'created_at'          => $now,
                            'updated_at'          => $now,
                        ]);
                    }
                }
            }

            /** ----------------------------
             * VARIANT IMAGES
             * ---------------------------- */
            if (isset($variant['images']) && is_array($variant['images'])) {
                // ProductImage::where('product_variant_id', $variantId)->delete();
                $this->processVariantImagesFile($productId, $variantId, $variant['images']);
            }
            unset($existingVariants[$variantSku]);
        }



        /** ----------------------------
         * DELETE REMOVED VARIANTS
         * ---------------------------- */
        foreach ($existingVariants as $removedVariant) {
            $images = ProductImage::where('product_variant_id', $removedVariant->id)->get();
            foreach ($images as $image) {
                $this->deleteFile($image->image_url);
            }

            ProductImage::where('product_variant_id', $removedVariant->id)->delete();
            VariantAttributeCombination::where('variant_id', $removedVariant->id)->delete();
            $removedVariant->delete();
        }
    }

    /**********************************************
     * IMAGE UPLOAD HELPER
     **********************************************/
    private function uploadImage($file, $path, $width = null, $height = null): string
    {
        if (!function_exists('imageUpload')) {
            throw new Exception('imageUpload function not found. Check your helpers.');
        }

        if (is_string($file) && file_exists($file)) {
            return $file;
        }
        return imageUpload($file, $path, $width, $height);
    }


    private function deleteFile($filePath)
    {
        if ($filePath && file_exists(public_path($filePath))) {
            @unlink(public_path($filePath));
        }
    }



    private function processVariantImages(int $productId, int $variantId, $images)
    {
        $sort = 0;
        $now  = now();

        $images = array_filter(explode(',', $images));

        Log::info('Variant image processing started', [
            'product_id'  => $productId,
            'variant_id'  => $variantId,
            'image_ids'   => $images,
            'image_count' => count($images),
        ]);

        foreach ($images as $imageId) {

            try {

                /** 1️⃣ Get temp image */
                $imgObject = TempImage::where('id', $imageId)
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$imgObject) {
                    Log::warning('Temp image not found', ['image_id' => $imageId]);
                    continue;
                }

                $tempUrl = $imgObject->image_url;

                /** 2️⃣ Extract path */
                $path      = ltrim(parse_url($tempUrl, PHP_URL_PATH), '/');
                $fromPath  = dirname($path);
                $imageName = basename($path);

                $toPath   = "uploads/products/product_{$productId}";
                $imageUrl = "{$toPath}/{$imageName}";

                /** 3️⃣ MOVE IMAGE ONLY ONCE */
                if (!file_exists(public_path($imageUrl))) {

                    $moved = moveProductImage($fromPath, $toPath, $imageName);

                    if (!$moved) {
                        Log::error('Image move failed', [
                            'image_id' => $imageId,
                            'image'    => $imageName,
                        ]);
                        continue;
                    }
                }

                $newUrl = asset($imageUrl);
                /** 4️⃣ SAVE DB (same image_url for all variants) */
                ProductImage::create([
                    'product_id'         => $productId,
                    'product_variant_id' => $variantId,
                    'image_url'          => $newUrl,
                    'sort_order'         => ++$sort,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);

                Log::info('Variant image saved', [
                    'variant_id' => $variantId,
                    'image_url'  => $newUrl,
                ]);
            } catch (\Throwable $e) {

                Log::error('Variant image processing failed', [
                    'image_id' => $imageId,
                    'message'  => $e->getMessage(),
                    'file'     => $e->getFile(),
                    'line'     => $e->getLine(),
                ]);
            }
        }

        Log::info('Variant image processing completed', [
            'variant_id'  => $variantId,
            'total_saved' => $sort,
        ]);
    }






    private function processVariantImagesFile(int $productId, int $variantId, array $images)
    {
        $sort = 0;
        $now = now();

        foreach ($images as $image) {
            if (empty($image)) continue;

            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $imageUrl = $this->uploadImage($image, 'uploads/products', 1200, 1200);
            } elseif (is_string($image)) {
                $imageUrl = $image;
            } else {
                continue;
            }

            ProductImage::create([
                'product_id'         => $productId,
                'product_variant_id' => $variantId,
                'image_url'          => $imageUrl,
                'sort_order'         => ++$sort,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }
    }

    /**********************************************
     * DELETE PRODUCT
     **********************************************/
    public function deleteRecordById($id): bool
    {
        DB::beginTransaction();
        try {
            $product = Product::with(['variants.images'])->findOrFail($id);

            $this->deleteFile($product->main_image);
            $this->deleteFile($product->secondary_image);
            $this->deleteFile($product->seo_image);

            foreach ($product->variants as $variant) {
                foreach ($variant->images as $image) {
                    $this->deleteFile($image->image_url);
                }
                VariantAttributeCombination::where('variant_id', $variant->id)->delete();
            }

            ProductImage::where('product_id', $id)->delete();
            ProductVariant::where('product_id', $id)->delete();
            $product->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Product delete failed. ID: {$id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**********************************************
     * UPDATE PRODUCT STATUS
     **********************************************/
    public function updateStatusById($id, $status): Product
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $product->update(['status' => $status]);
            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Product status update failed. ID: {$id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateVariantStatusById($id, $status): ProductVariant
    {
        DB::beginTransaction();
        try {
            $variant = ProductVariant::findOrFail($id);
            $variant->update(['status' => $status]);
            DB::commit();
            return $variant;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Product variant status update failed. ID: {$id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getVariantConfig($productId)
    {
        $product = Product::find($productId);
        return $product ? json_decode($product->variant_config, true) : [];
    }

    /**********************************************
     * HELPER: SEO KEYWORDS & SLUG
     **********************************************/
    private function prepareSeoKeywords($keywords)
    {
        if (empty($keywords)) return null;

        if (is_array($keywords)) return json_encode($keywords);

        if (is_string($keywords)) return json_encode(explode(',', $keywords));

        return null;
    }

    private function generateSlug(string $name, $ignoreId = null): string
    {
        $slug = Str::slug($name);

        $query = Product::where('slug', $slug);
        if ($ignoreId) $query->where('id', '!=', $ignoreId);

        if ($query->exists()) {
            $slug .= '-' . uniqid();
        }

        return $slug;
    }
}

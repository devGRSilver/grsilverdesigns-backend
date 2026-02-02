<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Models\ProductImage;
use App\Models\TempImage;
use App\Models\VariantAttributeCombination;
use App\Services\AttributeService;
use App\Services\CategoriesService;
use App\Services\MetalsService;
use App\Services\ProductService;
use App\Services\SubCategoriesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\Image;

class ProductController extends ResponseController
{
    protected $resource     = 'products';
    protected $resourceName = 'Product';

    protected $productService;
    protected $metalService;
    protected $categoriesService;
    protected $attributeService;
    protected $subCategoriesService;

    public function __construct(
        ProductService $productService,
        MetalsService $metalService,
        CategoriesService $categoriesService,
        AttributeService $attributeService,
        SubCategoriesService $subCategoriesService
    ) {
        $this->productService = $productService;
        $this->metalService = $metalService;
        $this->categoriesService = $categoriesService;
        $this->subCategoriesService = $subCategoriesService;
        $this->attributeService = $attributeService;
    }

    public function index(Request $request)
    {
        // Permission check already handled by middleware: products.view.any
        if ($request->ajax()) {
            $data = $this->productService->getDataForDataTable($request);
            return response()->json($data);
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} List",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
            'categories'   => $this->categoriesService->getActiveParentCategories(),
        ]);
    }







    public function create()
    {
        return view("admin.{$this->resource}.add", [
            'title'      => "Add {$this->resourceName}",
            'categories' => $this->categoriesService->getActiveParentCategories(),
            'attributes' => $this->attributeService->getActiveAttributes(),
        ]);
    }

    public function edit($encryptedId)
    {
        // Permission: products.update (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $product = $this->productService->findById($id);

            $existingOptions = VariantAttributeCombination::whereHas('variant', fn($q) => $q->where('product_id', $product->id))
                ->with(['attribute', 'attributeValue'])
                ->get()
                ->unique('attribute_value_id')
                ->groupBy('attribute_id');

            return view("admin.{$this->resource}.edit", [
                'title'           => "Edit {$this->resourceName}",
                'product'         => $product,
                'categories'      => $this->categoriesService->getActiveParentCategories(),
                'attributes'      => $this->attributeService->getActiveAttributes(),
                'subCategories'   => $this->subCategoriesService->getActiveSubCategories($product->category_id),
                'existingOptions' => $existingOptions,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', "{$this->resourceName} not found.");
        }
    }

    public function show($encryptedId)
    {
        // Permission: products.view (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $product = $this->productService->findById($id);

            return view("admin.{$this->resource}.show", [
                'title'        => "{$this->resourceName} Details",
                'product'      => $product,
                'resource'     => $this->resource,
                'resourceName' => $this->resourceName,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', "{$this->resourceName} not found.");
        }
    }

    // public function store(ProductRequest $request)
    public function store(ProductRequest $request)
    {

        Log::info('Product Store Request', [
            'request' => $request->validated(),
            'ip'      => $request->ip(),
        ]);


        try {
            $data =   $this->productService->create($request->validated());
            return $this->successResponse([], "{$this->resourceName} created successfully.", route('products.index'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(ProductRequest $request, $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $data =  $this->productService->update($id, $request->validated());

            return $this->successResponse([], "{$this->resourceName} updated successfully.");
        } catch (\Exception $e) {

            dd($e);
            return $this->errorResponse("Failed to update {$this->resourceName}.", 500);
        }
    }

    public function delete($encryptedId)
    {
        // Permission: products.delete (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $this->productService->deleteRecordById($id);

            return $this->successResponse([], "{$this->resourceName} deleted successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to delete {$this->resourceName}.", 500);
        }
    }

    public function updateStatus(StatusUpdateRequest $request, $encryptedId)
    {
        // Permission: products.update.status (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $this->productService->updateStatusById($id, $request->status);

            return $this->successResponse([], 'Status updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update status.', 500);
        }
    }

    public function updateVariantStatus(Request $request, $encryptedId)
    {
        // Permission: products.update (handled by middleware - using same as variant update)
        try {
            $id = decrypt($encryptedId);
            $this->productService->updateVariantStatusById($id, $request->status);

            return $this->successResponse([], 'Variant status updated successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update variant status.', 500);
        }
    }

    public function variants($encryptedId)
    {
        // Permission: products.view (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $variants = $this->productService->getVariants($id);

            return view("admin.{$this->resource}.variants", [
                'title'    => "Product Variants",
                'variants' => $variants,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'Failed to load product variants.');
        }
    }

    public function deleteImage(string $encryptedId)
    {
        try {
            $imageId = decrypt($encryptedId);
            $image = ProductImage::findOrFail($imageId);

            if ($image->image_url && file_exists(public_path($image->image_url))) {
                unlink(public_path($image->image_url));
            }

            $image->delete();

            return $this->successResponse([], 'Image deleted successfully.');
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return $this->errorResponse('Invalid image ID.', 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete image.', 500);
        }
    }

    // public function importForm()
    // {
    //     return view("admin.{$this->resource}.import", [
    //         'title' => "Import Products",
    //     ]);
    // }

    // public function import(Request $request)
    // {
    //     try {
    //         return $this->successResponse([], 'Products imported successfully.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Failed to import products.', 500);
    //     }
    // }

    // public function export(Request $request)
    // {
    //     try {
    //         return $this->successResponse([], 'Export started successfully.');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Failed to export products.', 500);
    //     }
    // }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
        ]);


        try {
            $file = $request->file('image');

            $imageId  = time() . '_' . Str::random(8);

            // Upload image using helper
            $imageUrl = imageUpload(
                $file,
                'uploads/products/temp_file',
                1200,
                1200
            );

            // Save in DB
            TempImage::create([
                'user_id'    => Auth::id(),
                'uniq_id'    => $request->uid,
                'image_url'  => $imageUrl,
                'model_type' => 'product',
            ]);

            $imageData = [
                'id'          => $imageId,
                'name'        => $file->getClientOriginalName(),
                'type'        => $file->getMimeType(),
                'size'        => $file->getSize(),
                'url'         => $imageUrl,
                'uploaded_at' => now()->timestamp,
            ];
            $images[$imageId] = $imageData;
            return response()->json([
                'success' => true,
                'image'   => $imageData,
            ]);
        } catch (\Throwable $e) {

            Log::error('Image Upload Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Image upload failed',
            ], 500);
        }
    }

    public function uploadImageList(Request $request)
    {
        // 1️⃣ Validate UID exists
        if (!$request->filled('uid')) {
            return response()->json([
                'success' => false,
                'message' => 'UID missing',
            ], 400);
        }


        $uid = $request->uid;


        // 3️⃣ Fetch images
        $images = TempImage::where('uniq_id', $uid)
            ->orderBy('created_at', 'desc')
            ->get();

        $imagesWithTimestamp = $images->map(function ($image) {
            return [
                'id'         => $image->id,
                'image_url'  => $image->image_url,
                'created_at' => $image->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $imagesWithTimestamp->count(),
            'images'  => $imagesWithTimestamp->values(),
        ]);
    }

    public function getImage($id)
    {
        $images = session()->get('product_images', []);

        if (!isset($images[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'image'   => $images[$id],
        ]);
    }

    public function deleteImages($id)
    {
        $images = session()->get('product_images', []);

        if (!isset($images[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        // Delete the physical file if path exists
        if (isset($images[$id]['url'])) {
            $imagePath = str_replace(asset(''), '', $images[$id]['url']);
            $imagePath = ltrim($imagePath, '/');

            // Try to delete the file
            try {
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                // Try to delete thumbnail if exists
                $pathInfo = pathinfo($imagePath);
                $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to delete image file: ' . $e->getMessage());
            }
        }

        unset($images[$id]);
        session()->put('product_images', $images);

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    public function clearImages()
    {
        $images = session()->get('product_images', []);

        // Delete all physical files
        foreach ($images as $img) {
            if (isset($img['url'])) {
                $imagePath = str_replace(asset(''), '', $img['url']);
                $imagePath = ltrim($imagePath, '/');

                try {
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }

                    // Try to delete thumbnail if exists
                    $pathInfo = pathinfo($imagePath);
                    $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
                    if (Storage::disk('public')->exists($thumbnailPath)) {
                        Storage::disk('public')->delete($thumbnailPath);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to delete image file: ' . $e->getMessage());
                }
            }
        }

        session()->forget('product_images');

        return response()->json([
            'success' => true,
            'message' => 'All images cleared',
            'count' => count($images)
        ]);
    }

    public function imageCount()
    {
        $images = session()->get('product_images', []);
        return response()->json([
            'success' => true,
            'count' => count($images)
        ]);
    }

    // ------------------------
    // Helper: Move Session Images to Product
    // ------------------------
    private function saveSessionImagesToProduct($productId)
    {
        $images = session()->get('product_images', []);

        foreach ($images as $img) {
            if (!isset($img['url'])) {
                continue;
            }

            // Get the current path from URL
            $currentPath = str_replace(asset(''), '', $img['url']);
            $currentPath = ltrim($currentPath, '/');

            $filename = basename($currentPath);
            $newPath = "uploads/products/{$filename}";

            try {
                // Move main image
                if (Storage::disk('public')->exists($currentPath)) {
                    Storage::disk('public')->move($currentPath, $newPath);
                }

                // Move thumbnail if exists
                $pathInfo = pathinfo($currentPath);
                $thumbnailOldPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
                $thumbnailNewPath = "uploads/products/thumbnails/{$filename}";

                if (Storage::disk('public')->exists($thumbnailOldPath)) {
                    Storage::disk('public')->move($thumbnailOldPath, $thumbnailNewPath);
                }

                // Save to database
                ProductImage::create([
                    'product_id' => $productId,
                    'image_url'  => $newPath,
                    'name'       => $img['name'] ?? $filename,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to move session image to product: ' . $e->getMessage());
            }
        }

        session()->forget('product_images');
    }
}

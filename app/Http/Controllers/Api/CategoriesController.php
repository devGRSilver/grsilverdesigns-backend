<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResponseController;
use App\Services\CategoriesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends ResponseController
{
    public function __construct(
        protected CategoriesService $categoriesService
    ) {}

    public function categories(Request $request): JsonResponse
    {
        try {
            $categories = $this->categoriesService->getActiveCategories($request->all());
            return $this->successResponse(
                data: $categories,
                message: 'Categories fetched successfully.',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}

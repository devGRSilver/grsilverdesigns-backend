<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResponseController;
use App\Services\ContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends ResponseController
{
    protected ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function getContent(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'slug' => 'required|string|exists:contents,slug'
            ]);


            $content = $this->contentService->getContent($request->slug);

            if (!$content) {
                return $this->errorResponse(
                    message: 'Content not found.',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }

            return $this->successResponse(
                data: $content,
                message: 'Content fetched successfully.',
                statusCode: Response::HTTP_OK
            );
        } catch (\Throwable $e) {

            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Services\DashboardService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends ResponseController
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display dashboard page
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $stats = $this->dashboardService->getStats($request->all());
            return $this->successResponse($stats, 'Statistics loaded successfully');
        } catch (Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Get top countries by sales
     */
    public function getTopCountries(Request $request): JsonResponse
    {
        try {
            $countries = $this->dashboardService->getTopCountries($request->all());
            return $this->successResponse($countries, 'Top countries loaded successfully');
        } catch (Exception $e) {
            Log::error('Top countries error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Failed to load top countries', 500);
        }
    }

    /**
     * Get revenue chart data
     */
    public function getRevenueChart(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboardService->getRevenueChart($request->all());
            return $this->successResponse($data, 'Revenue chart data loaded successfully');
        } catch (Exception $e) {
            dd($e);
            Log::error('Revenue chart error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to load revenue chart data', 500);
        }
    }

    /**
     * Get trending products
     */
    public function getTrendingProducts(Request $request): JsonResponse
    {
        try {
            $products = $this->dashboardService->getTrendingProducts($request->all());
            return $this->successResponse($products, 'Trending products loaded successfully');
        } catch (Exception $e) {
            Log::error('Trending products error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to load trending products', 500);
        }
    }

    /**
     * Get best selling products
     */
    public function getBestSellingProducts(Request $request): JsonResponse
    {
        try {
            $products = $this->dashboardService->getBestSellingProducts($request->all());
            return $this->successResponse($products, 'Best selling products loaded successfully');
        } catch (Exception $e) {
            dd($e);


            Log::error('Best selling products error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to load best selling products', 500);
        }
    }

    /**
     * Get top selling categories
     */
    public function getTopCategories(Request $request): JsonResponse
    {
        try {
            $categories = $this->dashboardService->getTopCategories($request->all());
            return $this->successResponse($categories, 'Top categories loaded successfully');
        } catch (Exception $e) {

            Log::error('Top categories error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to load top categories', 500);
        }
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(Request $request): JsonResponse
    {
        try {
            $transactions = $this->dashboardService->getRecentTransactions($request->all());
            return $this->successResponse($transactions, 'Recent transactions loaded successfully');
        } catch (Exception $e) {
            Log::error('Recent transactions error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to load recent transactions', 500);
        }
    }

    /**
     * Get top customers
     */
    public function getTopCustomers(Request $request): JsonResponse
    {
        try {
            $customers = $this->dashboardService->getTopCustomers($request->all());
            return $this->successResponse($customers, 'Top customers loaded successfully');
        } catch (Exception $e) {

            Log::error('Top customers error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to load top customers', 500);
        }
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders(Request $request): JsonResponse
    {
        try {
            $orders = $this->dashboardService->getRecentOrders($request->all());
            return $this->successResponse($orders, 'Recent orders loaded successfully');
        } catch (Exception $e) {

            Log::error('Recent orders error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to load recent orders', 500);
        }
    }
}

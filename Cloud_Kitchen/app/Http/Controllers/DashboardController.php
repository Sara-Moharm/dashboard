<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\DashboardService;
use App\Http\Requests\DashboardRequest;

use Exception;
class DashboardController extends Controller
{

    protected $dashboardService;
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function GeneralSentimentDistribution(DashboardRequest $request)
    {
        try {
                $validated = $request->validated();
                $period = $validated['period'] ?? 'today';
                $feedbackDistribution = $this->dashboardService->getGeneralSentimentDistribution($period);
                return response()->json([
                    'status' => 'success',
                    'data' => $feedbackDistribution
                ]);
            } catch (Exception $e) {
                Log::error('Failed to fetch general sentiment distribution: ' . $e->getMessage());
                throw new Exception('Failed to fetch general sentiment distribution');
            }
    }

    public function TrendOverTime(DashboardRequest $request)
    {
        try{
            $validated = $request->validated();
             $period = $validated['period'] ?? 'today';
             $sentiments = $validated['sentiments'] ?? ['positive', 'negative', 'neutral'];
            $result = $this->dashboardService->getTrendOverTime($period,$sentiments);
            return response()->json([
                'status' => 'success',
                'data' => $result
            ]);
        }catch (Exception $e) {
            Log::error('Failed to fetch trend over time: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch trend over time'
            ], 500);
        }
    }

    public function AspectSentimentBreakdown(DashboardRequest $request)
    {
        try{
            $validated = $request->validated();
            $period = $validated['period'] ?? 'today';
            $aspects = $this->dashboardService->getAspectSentimentBreakdown($period);
            return response()->json([
                'status' => 'success',
                'data' => $aspects
            ]);
        }catch (Exception $e) {
            Log::error('Failed to fetch aspect sentiment breakdown: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch aspect sentiment breakdown'
            ], 500);
        }
    }

    public function TopComplainedAspects(DashboardRequest $request)
    {
        try{
            $validated = $request->validated();
            $period = $validated['period'] ?? 'today';
            $topComplainedAspects = $this->dashboardService->getTopComplainedAspects($period);
            return response()->json([
                'status' => 'success',
                'data' => $topComplainedAspects
            ]);
        }catch (Exception $e) {
            Log::error('Failed to fetch top complained aspects: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch top complained aspects'
            ], 500);
        }
    }
     public function getMenuAnalytics(Request $request)
    {
        $limit = $request->query('limit', 5);
        $order = $request->query('order', 'desc'); // asc | desc
        $categoryId = $request->query('category_id');
        $groupBy = $request->query('groupByCategory') === 'true';
        $onlyUnordered = $request->query('onlyUnordered') === 'true';

        $dishes = $this->dashboardService->getCategoryDishesByAnalytics(
            $limit,
            $order,
            $categoryId,
            $groupBy,
            $onlyUnordered
        );

        return !$dishes->isEmpty()
            ?$this->successResponse([
                "count"             => $dishes->count(),
                'rated_category_dishes'   => $dishes
            ], 'Menu analytics data retrieved successfully.', 200)
            :$this->errorResponse(
                'There is no rated dishes to display.',
            404, []);
    }

    public function getCoreOperationalKPIs()
    {
        try {
            $kpiData = $this->dashboardService->getCoreOperationalKPIs();

            return $this->successResponse([
                'KPI data'   => $kpiData
            ], 'Operational KPIs retrieved successfully.', 200);
           
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch KPIs.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
     
    public function getSalesPerformanceKPIs()
    {
        try {
            $data = $this->dashboardService->getSalesPerformanceKPIs();

            return $this->successResponse([
                'success' => true,
                'data'    => $data,
            ],'Sales performance KPIs retrieved successfully.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve KPIs.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getTeamEfficienyKPIs()
    {
        try {
            $data = $this->dashboardService->getTeamEfficienyKPIs();

            return $this->successResponse([
                'success' => true,
                'data'    => $data,
            ], 'Team Efficieny KPIs retrieved successfully.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team Efficieny KPIs.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
 
    public function topRatedDishes(){
        try {
            $topRatedDishes = $this->dashboardService->getTopDishes("quantity", 10, 'desc')->map(function ($dish) {
                return [
                    'id' => $dish->id,
                    'name' => $dish->title,
                ];
            });

            return $this->successResponse([
                'topRatedDishes' => $topRatedDishes
            ], 'Top rated dishes retrieved successfully.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve top rated dishes.',
                500,
                $e->getMessage()
            );
        }
        
    }

    /**
     * Success response format.
     */
    private function successResponse(array $data = [], string $message = "Success", int $status = 200)
    {
        return response()->json([
            "success" => true,
            "message" => $message,
            ...$data,
        ], $status);
    }
    /**
     * Error response format.
     */
    private function errorResponse(string $message, int $status = 500, $errors = null)
    {
        return response()->json([
            "success" => false,
            "message" => $message,
            "errors" => $errors,
        ], $status);
    }
}


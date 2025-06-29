<?php

namespace App\Services;

use App\Models\Feedback;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\FeedbackAspect;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\{Order};
class DashboardService
{
     private function getInPeriod(string $period, Builder $query)
    {
        switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'last_3_months':
                    $query->whereBetween('created_at', [now()->subMonths(3)->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'last_6_months':
                    $query->whereBetween('created_at', [now()->subMonths(6)->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                }
        return $query;
    }

   public function getGeneralSentimentDistribution(string $period = 'today')
{
    try {
        // 1. Start from Feedback model
        $query = Feedback::query();

        // 2. Apply period filter
        $query = $this->getInPeriod($period, $query);

        // 3. Group and count
        $feedbacks = $query->select('sentiment', DB::raw('count(*) as count'))
            ->groupBy('sentiment')
            ->get()
            ->keyBy('sentiment')
            ->map(function ($item) {
                return $item->count;
            });

        if ($feedbacks->isEmpty()) {
            return [
                'positive' => 0,
                'negative' => 0,
                'neutral'  => 0,
            ];
        }

        return [
            'positive' => $feedbacks->get('positive', 0),
            'negative' => $feedbacks->get('negative', 0),
            'neutral'  => $feedbacks->get('neutral', 0),
        ];
    } catch (Exception $e) {
        Log::error('Failed to fetch general sentiment distribution: ' . $e->getMessage());
        throw new Exception('Failed to fetch general sentiment distribution');
    }
}


   
    
    public function getTrendOverTime(string $period = 'today', array $sentiments = ['positive', 'negative', 'neutral']): array
{
    try {
        $query = Feedback::query();

        // Apply period filter
        $query = $this->getInPeriod($period, $query);

        // Group by date and sentiment
        $rawData = $query->select(
                DB::raw('DATE(created_at) as date'),
                'sentiment',
                DB::raw('count(*) as count')
            )
            ->whereIn('sentiment', $sentiments)
            ->groupBy(DB::raw('DATE(created_at)'), 'sentiment')
            ->orderBy('date', 'asc')
            ->get();

        if ($rawData->isEmpty()) {
            return [];
        }

        // Reshape data
        $grouped = [];

        foreach ($rawData as $row) {
            $date = $row->date;
            $sentiment = $row->sentiment;
            $count = $row->count;

            if (!isset($grouped[$date])) {
                $grouped[$date] = [
                    'date' => $date,
                    'positive' => 0,
                    'negative' => 0,
                    'neutral' => 0
                ];
            }

            $grouped[$date][$sentiment] = (int) $count;
        }

        return array_values($grouped);

    } catch (Exception $e) {
        Log::error('Failed to fetch trend over time: ' . $e->getMessage());
        throw new Exception('Failed to fetch trend over time');
    }
}


    public function getAspectSentimentBreakdown(string $period = 'today')
    {
        try {
            $query = FeedbackAspect::query();    
            $query = $this->getInPeriod($period, $query);

           $query = $query->select('aspect')
                ->selectRaw("
                    SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) as positive_count,
                    SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) as negative_count,
                    SUM(CASE WHEN sentiment = 'neutral'  THEN 1 ELSE 0 END) as neutral_count
                ")
                ->groupBy('aspect');

            if ($query->count() === 0) {
                return [];
            }
            $aspects = $query->get()->map(function ($item) {
            $total = $item->positive_count + $item->negative_count + $item->neutral_count;

            return [
                'aspect' => $item->aspect,
                'positive' => $total > 0 ? round(($item->positive_count / $total) * 100, 2) : 0,
                'negative' => $total > 0 ? round(($item->negative_count / $total) * 100, 2) : 0,
                'neutral'  => $total > 0 ? round(($item->neutral_count / $total) * 100, 2) : 0,
            ];
         });
            return $aspects;


        }catch (Exception $e) {
            Log::error('Failed to fetch aspect sentiment breakdown: ' . $e->getMessage());
            throw new Exception('Failed to fetch aspect sentiment breakdown');
        }
    }

    public function getTopComplainedAspects(string $period = 'today'): array
    {
        try
        {   
            $query = FeedbackAspect::query();
            $query = $this->getInPeriod($period, $query);
            $query = $query->select('aspect')->where('sentiment', 'negative')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('aspect')
                ->orderBy('count', 'desc')
                ->limit(5);
            
            $results = $query->get();

            if ($results->isEmpty()) {
                return [];
            }

            return $results->map(function ($item) {
                return [
                    'aspect' => $item->aspect,
                    'count'  => $item->count,
                ];
            })->toArray();
        } catch (Exception $e) {
            Log::error('Failed to fetch top complained aspects: ' . $e->getMessage());
            throw new Exception('Failed to fetch top complained aspects: ' . $e->getMessage(), 0, $e);
        }
    }

      public function getCategoryDishesByAnalytics(
        $limit = 5,
        $order = 'desc',
        $categoryId = null,
        $groupByCategory = false,
        $onlyUnordered = false
    ) {
        $query = DB::table('category_dishes')
                    ->leftJoin('order_items', 'category_dishes.id', '=', 'order_items.category_dish_id')
                    ->join('categories', 'category_dishes.category_id', '=', 'categories.id')
                    ->select(
                        'category_dishes.id as dish_id',
                        'category_dishes.title as dish_title',
                        'categories.id as category_id',
                        'categories.title as category_title',
                        DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_quantity')
                    )
                    ->groupBy('category_dishes.id', 'category_dishes.title', 'categories.id', 'categories.title');

        // Filter by category if needed
        if ($categoryId) {
            $query->where('category_dishes.category_id', $categoryId);
        }

        // 💥 Filter for unordered dishes only
        if ($onlyUnordered) {
            $query->having('total_quantity', '=', 0);
        } else {
            $query->having('total_quantity', '>', 0);
        }

        $query->orderBy('total_quantity', $order);

        if (!$groupByCategory) {
            return $query->take($limit)->get();
        }

        // Group by category if required
        $results = $query->get();
        return $results->groupBy('category_title')->map(fn($group) => $group->take($limit)->values());
    }

    private function getAverageTimeDiffBetweenColumns(string $startCol, string $endCol, string $alias)
    {
        return Order::whereNotNull($startCol)
            ->whereNotNull($endCol)
            ->whereDate('created_at', today())
            ->selectRaw("AVG(TIMESTAMPDIFF(MINUTE, $startCol, $endCol)) as $alias")
            ->value($alias);
    }

    public function getCoreOperationalKPIs()
    {
        $today           = Carbon::today();

        $ordersToday     = Order::whereDate('created_at', $today)->count();

        $completedOrders = Order::where('status', 'delivered')->whereDate('created_at', $today)->count();

        $activeStatuses  = ['pending', 'confirmed', 'preparing', 'ready', 'delivering'];

        $activeOrders    = Order::whereIn('status', $activeStatuses)->whereDate('created_at', $today)->count();

        $lateOrders      = Order::whereIn('status', $activeStatuses)->whereDate('created_at', $today)
                                ->where('created_at', '<', now()->subMinutes(60))
                                ->count();
        
        $avgPrepTime     = $this->getAverageTimeDiffBetweenColumns('preparing_at', 'ready_at', 'avg_prep_time');

        $avgDeliveryTime = $this->getAverageTimeDiffBetweenColumns('ready_at', 'delivered_at', 'avg_delivery_time');

        $totalOrderTime  = $this->getAverageTimeDiffBetweenColumns('created_at', 'delivered_at', 'total_order_time');


        return [
            'orders_today'         => $ordersToday,
            'completed_orders'     => $completedOrders,
            'active_orders'        => $activeOrders,
            'late_orders'          => $lateOrders,
            'avg_preparation_time' => round($avgPrepTime ?? 0, 2),
            'avg_delivery_time'    => round($avgDeliveryTime ?? 0, 2),
            'total_order_time'     => round($totalOrderTime ?? 0, 2),
        ];
    }


    public function getTopDishes($metric = 'quantity', $limit = 5, $order = 'desc')
    {
        if ($metric === 'sales') {
            $aggregationColumn = DB::raw('SUM(order_items.quantity * order_items.price) as total_value');
        } else {
            $aggregationColumn = DB::raw('SUM(order_items.quantity) as total_value');
        }

        return DB::table('order_items')
            ->join('category_dishes', 'order_items.category_dish_id', '=', 'category_dishes.id')
            ->select('category_dishes.id', 'category_dishes.title','category_dishes.image_url', 'category_dishes.calories', $aggregationColumn)
            ->groupBy('category_dishes.id', 'category_dishes.title', 'category_dishes.image_url', 'category_dishes.calories')
            ->orderBy('total_value', $order)
            ->take($limit)
            ->get();
    }



    public function getSalesPerformanceKPIs()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalSalesToday = Order::whereDate('created_at', $today)->sum('total_price');
        $totalSalesMonth = Order::whereDate('created_at', '>=', $startOfMonth)->sum('total_price');

        $ordersLast7Days = Order::whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total_orders')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $avgOrderValue = Order::avg('total_price');


        $topSellingDishes = $this->getTopDishes('sales');

        $topRatedDishes = $this->getTopDishes('quantity');


        return [
            'total_sales_today'    => round($totalSalesToday, 2),
            'total_sales_month'    => round($totalSalesMonth, 2),
            'avg_order_value'      => round($avgOrderValue ?? 0, 2),
            'orders_last_7_days'   => $ordersLast7Days,
            'top_selling_dishes'   => $topSellingDishes,
            'top_rated_dishes'     => $topRatedDishes
        ];
    }

    // public function getChefOrdersCount($status = null)
    // {
    //     $query = DB::table('order_items')
    //         ->join('staff', 'order_items.staff_id', '=', 'staff.id')
    //         ->whereDate('order_items.created_at', today())
    //         ->whereNotNull('order_items.staff_id');

    //     if ($status) {
    //         $query->where('order_items.status', $status);
    //     }

    //     return $query->select('staff.id', 'staff.name', DB::raw('COUNT(*) as completed_orders'))
    //                 ->groupBy('staff.id', 'staff.name')
    //                 ->get();
    // }

    public function getChefPerformance($type = 'count', $status = null, $alias = "All_order_items")
    {
        $query = DB::table('order_items')
            ->join('staff', 'order_items.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->whereDate('order_items.created_at', today())
            ->whereNotNull('order_items.staff_id');

        if ($status) {
            $query->where('order_items.status', $status);
        }

        if ($type === 'avg_time') {
            return $query->whereNotNull('preparing_at')
                        ->whereNotNull('ready_at')
                        ->select(
                            'staff.id',
                            'users.fname as staff_name',
                            DB::raw('AVG(TIMESTAMPDIFF(MINUTE, preparing_at, ready_at)) as avg_prep_time')
                        )
                        ->groupBy('staff.id', 'staff_name')
                        ->get();
        }

        // Default: count
        return $query->select(
                        'staff.id',
                        'users.fname as staff_name',
                        DB::raw("COUNT(*) as $alias")
                    )
                    ->groupBy('staff.id', 'staff_name')
                    ->get();
    }

    public function getDeliveryPerformance($type = 'count', $status = null, $alias = "All_orders")
    {
        $query = DB::table('orders')
            ->join('staff', 'orders.delivery_personnel_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->whereDate('orders.created_at', today())
            ->whereNotNull('orders.delivery_personnel_id');

        if ($status) {
            $query->where('orders.status', $status);
        }

        if ($type === 'avg_time') {
            return $query->whereNotNull('delivering_at')
                        ->whereNotNull('delivered_at')
                        ->select(
                            'staff.id',
                            'users.fname as delivery_personnel_name',
                            DB::raw('AVG(TIMESTAMPDIFF(MINUTE, delivering_at, delivered_at)) as avg_delivery_time')
                        )
                        ->groupBy('staff.id', 'delivery_personnel_name')
                        ->get();
        }

        // Default: count of delivered orders
        return $query->select(
                        'staff.id',
                        'users.fname as delivery_personnel_name',
                        DB::raw("COUNT(*) as $alias")
                    )
                    ->groupBy('staff.id', 'delivery_personnel_name')
                    ->get();
    }

    public function getTeamEfficienyKPIs()
    {
        $chefCompletedOrderItemsToday = $this->getChefPerformance('count','ready', "Completed_order_items");
        $chefAllOrderItemsToday = $this->getChefPerformance();
        $chefPreparingOrderItemsToday = $this->getChefPerformance('count','preparing', "Preparing_order_items");
        // Calculate efficiency as a percentage
        // Efficiency = (Completed Orders / All Orders) * 100
        // If there are no orders, set efficiency to 0 to avoid division by zero
        $efficiency = ($chefCompletedOrderItemsToday->count() > 0 && $chefAllOrderItemsToday->count() > 0) ? 
            round($chefCompletedOrderItemsToday->count() / $chefAllOrderItemsToday->count(), 2) * 100 : 0;
            
     
        // $avgPrepTimePerChef = DB::table('order_items')
        //                             ->join('staff', 'order_items.staff_id', '=', 'staff.id')
        //                             ->whereNotNull('preparing_at')
        //                             ->whereNotNull('ready_at')
        //                             ->whereDate('order_items.created_at', today())
        //                             ->select('staff.id', 'staff.name', DB::raw('AVG(TIMESTAMPDIFF(MINUTE, preparing_at, ready_at)) as avg_prep_time'))
        //                             ->groupBy('staff.id', 'staff.name')
        //                             ->get();

        $avgPrepTimePerChef = $this->getChefPerformance('avg_time');

        $deliveredOrdersToday = $this->getDeliveryPerformance('count', 'delivered', "Delivered_orders");
        $deliveringOrdersToday = $this->getDeliveryPerformance('count', 'delivering', "Delivering_orders");
        $allDeliveryOrdersToday = $this->getDeliveryPerformance();

        
        $avgDeliveryTimePerDelivery = $this->getDeliveryPerformance('avg_time');

        return [
            'chef_order_items_today'                  => $chefCompletedOrderItemsToday,
            'chef_all_order_items_today'              => $chefAllOrderItemsToday,
            'chef_preparing_order_items_today'        => $chefPreparingOrderItemsToday,
            'chef_efficiency'                         => $efficiency,
            'avg_prep_time_per_chef'                  => $avgPrepTimePerChef,
            'overall_avg_prep_time'                   => round($avgPrepTimePerChef->avg('avg_prep_time') ?? 0, 2),
            'delivered_orders_today'                  => $deliveredOrdersToday,
            'delivering_orders_today'                 => $deliveringOrdersToday,
            'all_delivery_orders_today'               => $allDeliveryOrdersToday,
            'avg_delivery_time_per_delivery'          => $avgDeliveryTimePerDelivery,
            'overall_avg_delivery_time'               => round($avgDeliveryTimePerDelivery->avg('avg_delivery_time') ?? 0, 2)
        ];
    }


}
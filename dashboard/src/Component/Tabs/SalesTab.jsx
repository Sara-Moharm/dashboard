import React, { useEffect, useState } from "react";
import axios from "axios";
import LineChart from "../Charts/Sales/LineChart";
import BarChart from "../Charts/Sales/BarChart";
import KpiCard from "../Charts/Sales/KPICard";

const SalesTab = () => {
  const [data, setData] = useState(null);

  useEffect(() => {
    axios
      .get("http://127.0.0.1:8000/api/admin/analytics/saleskpis", {
        headers: {
          Authorization: 'Bearer 2|xZk93wzstYeAtrqKAX320tQVKRCTfym09tJdK8FL168f4a12',
        },
      })
      .then((res) => setData(res.data.data));
  }, []);

  if (!data) return <p>Loading...</p>;

  return (
    <div className="space-y-6">
      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <KpiCard label="Today's Sales" value={data.total_sales_today + " EGP"} />
        <KpiCard label="This Month's Sales" value={data.total_sales_month + " EGP"} />
        <KpiCard label="Avg. Order Value" value={data.avg_order_value + " EGP"} />
      </div>

<div className="grid grid-cols-1 md:grid-cols-2 gap-6">

      {/* Line Chart */}
      <LineChart
        labels={data.orders_last_7_days.map((d) => d.day)}
        data={data.orders_last_7_days.map((d) => d.total_orders)}
        label="Orders"
      />

      {/* Bar Chart - Top Selling Dishes */}
      <BarChart
        labels={data.top_selling_dishes.map((d) => d.title)}
        data={data.top_selling_dishes.map((d) => d.total_value)}
        label="Top Selling Dishes"
      />
</div>

      {/* Table - Top Rated Dishes */}
      {data?.top_rated_dishes?.length > 0 ? (
  <table className="w-full text-sm text-left">
    <thead>
      <tr className="bg-gray-100 text-gray-700">
        <th className="py-2 px-4 border-b">Dish</th>
        <th className="py-2 px-4 border-b">Quantity</th>
      </tr>
    </thead>
    <tbody>
      {data.top_rated_dishes.map((dish, i) => (
        <tr key={i} className="hover:bg-gray-50">
          <td className="py-2 px-4 border-b">{dish.title}</td>
          <td className="py-2 px-4 border-b">{dish.total_value}</td>
        </tr>
      ))}
    </tbody>
  </table>
) : (
  <p className="text-gray-500">No top-rated dishes found.</p>
)}

    </div>
  );
};

export default SalesTab;

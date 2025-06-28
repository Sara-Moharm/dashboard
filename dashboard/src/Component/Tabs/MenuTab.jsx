import React, { useEffect, useState } from "react";
import axios from "axios";
import { Bar, Pie } from "react-chartjs-2";

const MenuAnalyticsTab = () => {
  const [topDishes, setTopDishes] = useState([]);
  const [bottomDishes, setBottomDishes] = useState([]);
  const [categoryData, setCategoryData] = useState([]);
  const [tableData, setTableData] = useState([]);
  const [loading, setLoading] = useState(true);

  const token = "YOUR_TOKEN_HERE"; // Replace with real token

  useEffect(() => {
    const fetchData = async () => {
      try {
        const config = {
          headers: { Authorization: `Bearer ${token}` },
        };

        const topRes = axios.get(
          "http://127.0.0.1:8000/api/admin/analytics/menu_analytics?order=desc&limit=5",
          config
        );
        const bottomRes = axios.get(
          "http://127.0.0.1:8000/api/admin/analytics/menu_analytics?order=asc&limit=5",
          config
        );
        const categoryRes = axios.get(
          "http://127.0.0.1:8000/api/admin/analytics/menu_analytics?groupByCategory=true",
          config
        );

        setTopDishes(topRes.data.rated_category_dishes);
        setBottomDishes(bottomRes.data.rated_category_dishes);

        const grouped = categoryRes.data.rated_category_dishes;
        const formatted = Object.keys(grouped).map((cat) => ({
          label: cat,
          total: grouped[cat].reduce((sum, d) => sum + d.total_quantity, 0),
        }));
        setCategoryData(formatted);

        // Full Table Data: top + bottom + category
        const merged = [
          ...topRes.data.rated_category_dishes,
          ...bottomRes.data.rated_category_dishes,
        ];
        setTableData(merged);
      } catch (err) {
        console.error("Error fetching menu analytics", err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  if (loading) return <p className="text-center">Loading...</p>;

  const barData = (list) => ({
    labels: list.map((d) => d.dish_title),
    datasets: [
      {
        label: "Sales Qty",
        data: list.map((d) => d.total_quantity),
        backgroundColor: "#10b981",
      },
    ],
  });

  const pieData = {
    labels: categoryData.map((c) => c.label),
    datasets: [
      {
        label: "Category Contribution",
        data: categoryData.map((c) => c.total),
        backgroundColor: ["#34d399", "#60a5fa", "#fbbf24", "#f87171", "#c084fc"],
      },
    ],
  };

  return (
    <div className="space-y-6">
      {/* Top + Bottom Dishes */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="bg-white p-4 rounded-lg shadow">
          <h3 className="font-semibold text-lg mb-2">Top 5 Dishes</h3>
          <Bar data={barData(topDishes)} />
        </div>
        <div className="bg-white p-4 rounded-lg shadow">
          <h3 className="font-semibold text-lg mb-2">Bottom 5 Dishes</h3>
          <Bar data={barData(bottomDishes)} />
        </div>
      </div>

      {/* Category Pie Chart */}
      <div className="bg-white p-4 rounded-lg shadow">
        <h3 className="font-semibold text-lg mb-2">Category Contribution</h3>
        <Pie data={pieData} />
      </div>

      {/* Dish Table */}
      <div className="bg-white p-4 rounded-lg shadow overflow-x-auto">
        <h3 className="font-semibold text-lg mb-4">Dish Sales Table</h3>
        <table className="min-w-full text-sm">
          <thead>
            <tr className="border-b font-semibold text-left">
              <th className="p-2">Dish</th>
              <th className="p-2">Category</th>
              <th className="p-2">Total Quantity</th>
            </tr>
          </thead>
          <tbody>
            {tableData.map((dish) => (
              <tr key={dish.dish_id} className="border-b">
                <td className="p-2">{dish.dish_title}</td>
                <td className="p-2">{dish.category_title}</td>
                <td className="p-2">{dish.total_quantity}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default MenuAnalyticsTab;

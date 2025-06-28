import React, { useEffect, useState } from "react";
import axios from "axios";
import { Bar } from "react-chartjs-2";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const TeamTab = () => {
  const [chefs, setChefs] = useState([]);
  const [deliveryStaff, setDeliveryStaff] = useState([]);
  const token = "2|xZk93wzstYeAtrqKAX320tQVKRCTfym09tJdK8FL168f4a12";

  useEffect(() => {
    axios
      .get("http://127.0.0.1:8000/api/admin/analytics/teamkpis", {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((res) => {
        setChefs(res.data.data.chefs || []);
        setDeliveryStaff(res.data.data.delivery_staff || []);
      })
      .catch((err) => {
        console.error("Failed to load team efficiency data:", err);
      });
  }, []);

  const renderTable = (title, data, isChef = true) => (
    <div className="overflow-x-auto bg-white p-4 rounded shadow mb-6">
      <h3 className="text-lg font-semibold mb-4">{title}</h3>
      <table className="min-w-full text-sm text-center">
        <thead className="bg-gray-100">
          <tr>
            <th className="p-2 border">Name</th>
            <th className="p-2 border">Completed Orders</th>
            <th className="p-2 border">
              {isChef ? "Avg Prep Time (min)" : "Avg Delivery Time (min)"}
            </th>
            <th className="p-2 border">% Delayed</th>
            <th className="p-2 border">Efficiency</th>
          </tr>
        </thead>
        <tbody>
          {data.map((person, index) => (
            <tr key={index} className="border-t">
              <td className="p-2 border">{person.name}</td>
              <td className="p-2 border">{person.completed_orders}</td>
              <td className="p-2 border">
                {isChef ? person.avg_prep_time : person.avg_delivery_time}
              </td>
              <td className="p-2 border text-red-600">
                {person.delayed_orders_percent}%
              </td>
              <td className="p-2 border">
                <div className="w-full bg-gray-200 rounded">
                  <div
                    className="bg-green-500 text-white text-xs p-1 rounded"
                    style={{ width: `${person.efficiency}%` }}
                  >
                    {person.efficiency}%
                  </div>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );

  const chartData = {
    labels: chefs.map((c) => c.name),
    datasets: [
      {
        label: "Chef Efficiency %",
        data: chefs.map((c) => c.efficiency),
        backgroundColor: "rgba(34,197,94,0.7)",
      },
    ],
  };

  return (
    <div className="p-6 space-y-8">
      {renderTable("Chefs", chefs, true)}
      {renderTable("Delivery Staff", deliveryStaff, false)}

      <div className="bg-white p-4 rounded shadow">
        <h3 className="text-lg font-semibold mb-4">Chef Efficiency Comparison</h3>
        <Bar
          data={chartData}
          options={{
            responsive: true,
            plugins: {
              legend: { display: false },
              title: { display: false },
            },
            scales: {
              y: {
                beginAtZero: true,
                max: 100,
              },
            },
          }}
        />
      </div>
    </div>
  );
};

export default TeamTab;

import React from "react";
import { Line } from "react-chartjs-2";
import {
  Chart as ChartJS,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Tooltip,
  Legend,
} from "chart.js";

ChartJS.register(LineElement, CategoryScale, LinearScale, PointElement, Tooltip, Legend);

const LineChart = ({ labels, data, label }) => {
  const chartData = {
    labels: labels,
    datasets: [
      {
        label: label,
        data: data,
        borderColor: "#22c55e",
        backgroundColor: "rgba(34, 197, 94, 0.2)",
        tension: 0.3,
        fill: true,
      },
    ],
  };

  return (
    <div className="bg-white p-4 rounded-lg shadow-sm">
      <h2 className="text-lg font-semibold mb-4">{label} (Last 7 Days)</h2>
      <div className="h-[220px]">
      <Line data={chartData} />
      </div>
    </div>
  );
};

export default LineChart;

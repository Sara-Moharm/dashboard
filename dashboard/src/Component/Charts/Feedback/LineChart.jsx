import React, { useEffect, useState } from "react";
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
import axios from "axios";

ChartJS.register(
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Tooltip,
  Legend
);

const LineChart  = ({ filters }) => {
  const [chartData, setChartData] = useState(null);

  
  useEffect(() => {
    const token = "2|xZk93wzstYeAtrqKAX320tQVKRCTfym09tJdK8FL168f4a12"; // حطي التوكن مؤقتًا هنا
      console.log('Filters:', filters);

    axios
      .get("http://127.0.0.1:8000/api/admin/analytics/trend-over-time", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
        params: {
        ...(filters?.period && { period: filters.period }),
        ...(filters?.sentiment && { sentiment: filters.sentiment }),
        },

      })
      .then((response) => {
        const apiData = response.data.data;
        console.log('API response:', response.data); // <== هنا

        if (!Array.isArray(apiData)) {
            console.error('Expected an array but got:', apiData);
            return;
        }
        const labels = apiData.map((item) => item.date);
        const positiveData = apiData.map((item) => item.positive);
        const negativeData = apiData.map((item) => item.negative);
        const neutralData = apiData.map((item) => item.neutral);

        const datasets = [];

if (!filters.sentiment || filters.sentiment === "positive") {
  datasets.push({
    label: "Positive",
    data: positiveData,
    borderColor: "rgba(75, 192, 192, 1)",
    backgroundColor: "rgba(0, 128, 0, 0.1)",
    tension: 0.4,
    fill: false,
  });
}

if (!filters.sentiment || filters.sentiment === "negative") {
  datasets.push({
    label: "Negative",
    data: negativeData,
    borderColor: "rgba(255, 99, 132, 1)",
    backgroundColor: "rgba(255, 0, 0, 0.1)",
    tension: 0.4,
    fill: false,
  });
}

if (!filters.sentiment || filters.sentiment === "neutral") {
  datasets.push({
    label: "Neutral",
    data: neutralData,
    borderColor: "rgba(201, 203, 207, 1)",
    backgroundColor: "rgba(128, 128, 128, 0.1)",
    tension: 0.4,
    fill: false,
  });
}

setChartData({
  labels,
  datasets,
});

      })
      .catch((error) => {
        console.error("Error fetching chart data:", error);
      });
  }, [filters]); // يعيد الطلب لو الفلاتر اتغيرت

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: "top",
      },
    },
  };

  return (
    <div className="h-[350px]" style={{ height: '100%', minHeight: '300px' }}>
      {chartData ? <Line data={chartData} options={options} /> : <p>Loading...</p>}
    </div>
  );
};

export default LineChart;

import React, { useEffect, useState } from "react";
import { Pie } from "react-chartjs-2";
import axios from "axios";
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
} from "chart.js";

ChartJS.register(ArcElement, Tooltip, Legend);

const PieChart = ({ filters }) => {
  const [chartData, setChartData] = useState(null);

  
  useEffect(() => {
    const token = "2|xZk93wzstYeAtrqKAX320tQVKRCTfym09tJdK8FL168f4a12"; // توكن مؤقت

    axios
      .get("http://127.0.0.1:8000/api/admin/analytics/general-sentiment", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
        params: {
          ...(filters?.period && { period: filters.period }),
        },
      })
      .then((response) => {
        const { positive, negative, neutral } = response.data.data;

        setChartData({
          labels: ["Positive", "Negative", "Neutral"],
          datasets: [
            {
              label: "Sentiment Count",
              data: [positive, negative, neutral],
              backgroundColor: [
                "rgba(75, 192, 192, 0.6)", // green
                "rgba(255, 99, 132, 0.6)",  // red
                "rgba(201, 203, 207, 0.6)", // gray
              ],
              borderColor: [
                "rgba(75, 192, 192, 1)",
                "rgba(255, 99, 132, 1)",
                "rgba(201, 203, 207, 1)",
              ],
              borderWidth: 1,
            },
          ],
        });
      })
      .catch((error) => {
        console.error("Error fetching sentiment data:", error);
      });
  }, [filters.period]); // هيتحدث لما يتغير الفلتر

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
      {chartData ? (
        <Pie data={chartData} options={options} />
      ) : (
        <p className="text-center">Loading...</p>
      )}
    </div>
  );
};

export default PieChart;

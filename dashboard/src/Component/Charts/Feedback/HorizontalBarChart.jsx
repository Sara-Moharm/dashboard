import React, { useEffect, useState } from 'react';
import { Bar } from 'react-chartjs-2';
import axios from 'axios';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from 'chart.js';

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

const HorizontalBarChart = ({ filters }) => {
  const [chartData, setChartData] = useState(null);

  useEffect(() => {
    const token = "2|xZk93wzstYeAtrqKAX320tQVKRCTfym09tJdK8FL168f4a12";

    axios
      .get("http://127.0.0.1:8000/api/admin/analytics/top-complained-aspects", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
        params: {
          ...(filters?.period && { period: filters.period }),
        },
      })
      .then((response) => {
        const data = response.data.data;

        const labels = data.map(item => item.aspect);
        const counts = data.map(item => item.count);

        const colors = [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        ];
        setChartData({
          labels,
          datasets: [
            {
              label: 'Count',
              data: counts,
              backgroundColor: colors.slice(0, counts.length),
            },
          ],
        });
      })
      .catch((error) => {
        console.error("Error fetching top complained aspects data:", error);
        setChartData(null);
      });
  }, [filters]);

  const options = {
    indexAxis: 'y', // عشان البار يكون أفقي
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      title: {
        display: true,
        text: 'Top Complained Aspects',
        font: { size: 18 },
      },
    },
    scales: {
      x: { beginAtZero: true },
      y: { ticks: { autoSkip: false } },
    },
  };

  return (
    <div className="w-full h-[350px]" style={{ height: '100%', minHeight: '300px' }}>
      {chartData ? (
        <Bar data={chartData} options={options} />
      ) : (
        <p className="text-center">Loading...</p>
      )}
    </div>
  );
};

export default HorizontalBarChart;

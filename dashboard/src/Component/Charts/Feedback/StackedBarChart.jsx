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

const StackedBarChart = ({ filters }) => {
  const [chartData, setChartData] = useState(null);

  useEffect(() => {
    const token = "2|xZk93wzstYeAtrqKAX320tQVKRCTfym09tJdK8FL168f4a12"; // توكن مؤقت

    axios
      .get("http://127.0.0.1:8000/api/admin/analytics/aspect-sentiment", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
        params: {
          ...(filters?.period && { period: filters.period })
        }
      })
      .then((response) => {
        const data = response.data.data;

        const labels = data.map(item => item.aspect);
        const positive = data.map(item => item.positive);
        const negative = data.map(item => item.negative);
        const neutral = data.map(item => item.neutral);

        setChartData({
          labels,
          datasets: [
            {
              label: 'Positive',
              data: positive,
              backgroundColor: 'rgba(75, 192, 192, 0.6)',
              stack: 'sentiment',
            },
            {
              label: 'Negative',
              data: negative,
              backgroundColor: 'rgba(255, 99, 132, 0.6)',
              stack: 'sentiment',
            },
            {
              label: 'Neutral',
              data: neutral,
              backgroundColor: 'rgba(201, 203, 207, 0.6)',
              stack: 'sentiment',
            },
          ],
        });
      })
      .catch((error) => {
        console.error("Error fetching stacked bar chart data:", error);
      });
  }, [filters]);

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
      },
      title: {
        display: true,
        text: 'Sentiment Distribution by Aspect',
      },
    },
    scales: {
      x: {
        stacked: true,
      },
      y: {
        stacked: true,
        beginAtZero: true,
        max: 100,
        ticks: {
          stepSize: 20
        }
      },
    },
  };

  
  return (
    <div className="w-full h-[350px]"  style={{ height: '100%', minHeight: '300px' }}>
      {chartData ? (
        <Bar data={chartData} options={options} />
      ) : (
        <p className="text-center">Loading...</p>
      )}
    </div>
  );
};

export default StackedBarChart;

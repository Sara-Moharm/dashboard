import React, { useEffect, useState } from 'react';
import axios from 'axios';
import {
  FaClipboardList,
  FaCheckCircle,
  FaTruck,
  FaClock,
  FaHourglassHalf,
} from 'react-icons/fa';

const CoreOperationalKPIs = () => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  const token = '2|xZk93wzstYeAtrqKAX320tQVKRCTfym09tJdK8FL168f4a12';

  useEffect(() => {
    axios
      .get('http://127.0.0.1:8000/api/admin/analytics/operkpis', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((res) => {
        setData(res.data.data);
      })
      .catch((err) => {
        console.error('Failed to fetch KPIs:', err);
      })
      .finally(() => setLoading(false));
  }, []);

  const kpis = [
    {
      label: 'Total Orders Today',
      value: data?.orders_today ?? 0,
      icon: <FaClipboardList />,
      color: 'bg-blue-100 text-blue-800',
    },
    {
      label: 'Completed Orders',
      value: data?.completed_orders ?? 0,
      icon: <FaCheckCircle />,
      color: 'bg-green-100 text-green-800',
    },
    {
      label: 'Ongoing Orders',
      value: data?.active_orders ?? 0,
      icon: <FaTruck />,
      color: 'bg-yellow-100 text-yellow-800',
    },
    {
      label: 'Delayed Orders',
      value: data?.late_orders ?? 0,
      icon: <FaHourglassHalf />,
      color: 'bg-red-100 text-red-800',
    },
    {
      label: 'Avg. Preparation Time (min)',
      value: data?.avg_preparation_time ?? 0,
      icon: <FaClock />,
      color: 'bg-purple-100 text-purple-800',
    },
    {
      label: 'Avg. Delivery Time (min)',
      value: data?.avg_delivery_time ?? 0,
      icon: <FaClock />,
      color: 'bg-indigo-100 text-indigo-800',
    },
  ];

  if (loading) return <p className="text-center">Loading...</p>;

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
      {kpis.map((kpi, index) => (
        <div
          key={index}
          className={`flex items-center justify-between p-4 rounded-lg shadow-sm ${kpi.color}`}
        >
          <div>
            <div className="text-sm font-medium">{kpi.label}</div>
            <div className="text-xl font-bold">{kpi.value}</div>
          </div>
          <div className="text-2xl">{kpi.icon}</div>
        </div>
      ))}
    </div>
  );
};

export default CoreOperationalKPIs;

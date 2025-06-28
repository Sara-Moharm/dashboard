// components/tabs/SalesTab.jsx
import React, { useState } from 'react';
import LineChart from '../Charts/Feedback/LineChart';
import PieChart from '../Charts/Feedback/PieChart';
import StackedBarChart from '../Charts/Feedback/StackedBarChart';
import HorizontalBarChart from '../Charts/Feedback/HorizontalBarChart';
import PeriodFilter from '../Filters/PeriodFilter';
import SentimentFilter from '../Filters/SentimentFilter';

const FeedbackTab = () => {
  const [filters, setFilters] = useState({
    period: '',
    sentiment: '',
  });

  return (
    <div className="p-6">
      {/* Period Filter */}
      <div className="mb-6">
        <PeriodFilter
          value={filters.period}
          onChange={(val) => setFilters({ ...filters, period: val })}
        />
      </div>

      {/* Chart Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Line Chart */}
        <div className="bg-white p-4 rounded-lg shadow h-[450px] flex flex-col">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-lg font-semibold">Line Chart</h2>
            <SentimentFilter
              value={filters.sentiment}
              onChange={(val) => setFilters({ ...filters, sentiment: val })}
            />
          </div>
          <div className="flex-1">
            <LineChart filters={filters} />
          </div>
        </div>

        {/* Pie Chart */}
        <div className="bg-white p-4 rounded-lg shadow min-h-[300px] flex items-center justify-center">
          <PieChart filters={filters} />
        </div>

        {/* Stacked Bar Chart */}
        <div className="bg-white p-4 rounded-lg shadow min-h-[300px] flex items-center justify-center">
          <StackedBarChart filters={filters} />
        </div>

        {/* Horizontal Bar Chart */}
        <div className="bg-white p-4 rounded-lg shadow min-h-[300px] flex items-center justify-center">
          <HorizontalBarChart filters={filters} />
        </div>
      </div>
    </div>
  );
};

export default FeedbackTab;

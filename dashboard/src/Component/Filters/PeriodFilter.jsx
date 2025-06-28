import React from 'react';

const PeriodFilter = ({ value, onChange }) => {
  return (
    <select
      value={value}
      onChange={(e) => onChange(e.target.value)}
      className="border px-2 py-1 rounded"
    >
      <option value="">All Periods</option>
      <option value="today">Today</option>
      <option value="this_week">This Week</option>
      <option value="this_month">This Month</option>
      <option value="last_3_months">Last 3 Months</option>
      <option value="last_6_months">Last 6 Months</option>
      <option value="this_year">This Year</option>
    </select>
  );
};

export default PeriodFilter;

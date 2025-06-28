// components/tabs/OperationalTab.jsx
import React from 'react';
import CoreOperationalKPIs from '../Cards/CoreOperationalKPIs';

const OperationalKPITab = () => {
  return (
    <div className="p-6">
      {/* KPI Cards */}
      <CoreOperationalKPIs />
    </div>
  );
};

export default OperationalKPITab;

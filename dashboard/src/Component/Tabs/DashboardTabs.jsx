import React, { useState } from 'react';
import OperationalKPITab from './OperationalKPITab';
import SalesTab from './SalesTab';
import TeamTab from './TeamTab';
import MenuTab from './MenuTab';
import FeedbackTab from './FeedbackTab';

const DashboardTabs = () => {
  const [activeTab, setActiveTab] = useState('operational');

  const renderTabContent = () => {
    switch (activeTab) {
      case 'operational':
        return <OperationalKPITab />;
      case 'sales':
        return <SalesTab />;
      case 'team':
        return <TeamTab />;
      case 'menu':
        return <MenuTab />;
      case 'feedback':
        return <FeedbackTab />;
      default:
        return null;
    }
  };

  return (
    <div className="p-4">
      <div className="flex gap-4 mb-6 border-b">
        {['operational', 'sales', 'team', 'menu', 'feedback'].map((tab) => (
          <button
            key={tab}
            onClick={() => setActiveTab(tab)}
            className={`px-4 py-2 font-semibold ${
              activeTab === tab ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-500'
            }`}
          >
            {tab[0].toUpperCase() + tab.slice(1)}
          </button>
        ))}
      </div>
      {renderTabContent()}
    </div>
  );
};

export default DashboardTabs;




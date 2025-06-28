import React from "react";

const KpiCard = ({ label, value }) => {
  return (
    <div className="bg-white p-4 rounded-lg shadow-sm">
      <div className="text-sm text-gray-500">{label}</div>
      <div className="text-2xl font-bold text-green-600">{value}</div>
    </div>
  );
};

export default KpiCard;

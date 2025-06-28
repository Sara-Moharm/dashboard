import React from 'react';

const SentimentFilter = ({ value, onChange }) => {
  return (
    <select
      value={value}
      onChange={(e) => onChange(e.target.value)}
      className="border px-2 py-1 rounded"
    >
      <option value="">All Sentiments</option>
      <option value="positive">Positive</option>
      <option value="negative">Negative</option>
      <option value="neutral">Neutral</option>
    </select>
  );
};

export default SentimentFilter;

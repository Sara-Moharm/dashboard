import React, { useState } from "react";
import SideBar from "../Component/SideBar";
import DashboardTabs from "../Component/Tabs/DashboardTabs";
import MainLayout from "../MainLayout";

const Home = () => {
  const [Filters, setFilters] = useState({
    period: "",
    sentiment: "",
  });

  return (
    <div className="min-h-screen flex bg-[#f5f5f5] overflow-hidden">
      {/* Sidebar */}
      <SideBar />

      {/* Main Content */}
      <div className="flex-1 flex flex-col">
        <MainLayout />
        {/* Navbar */}
        {/* <div className="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm">
          <input
            type="text"
            placeholder="Search here"
            className="w-1/2 border px-4 py-2 rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-green-300"
          />
          <div className="flex items-center gap-4">
            <span className="text-gray-700">
              Hello, <strong>Samantha</strong>
            </span>
            <img
              src="https://i.pravatar.cc/150?img=3"
              className="w-9 h-9 rounded-full border"
              alt="Profile"
            />
          </div>
        </div> */}

        {/* Page Content */}
        {/* Tabs Section */}
        {/* <DashboardTabs /> */}
      </div>
    </div>
  );
};

export default Home;

// MainLayout.jsx
import React from "react";
import AppNavbar from "./Component/Navbar";
import { Outlet } from "react-router-dom";

const MainLayout = () => {
  return (
    <>
      <AppNavbar />
      <div className="p-3">
        <Outlet />
      </div>
    </>
  );
};

export default MainLayout;

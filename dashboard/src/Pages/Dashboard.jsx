import React, { useEffect, useState } from "react";
import { Helmet } from "react-helmet";
import axios from "axios";
import { FaList } from "react-icons/fa";
import {
  BiUserCircle,
  // BiShield,
  // BiPeople,
  // BiListCheck,
} from "react-icons/bi";

const InfoCard = ({ title, icon, count, label }) => (
  <div className="col-xxl-4 col-md-6 mb-4">
    <div className="card-w bg-light p-4 rounded-4 info-card">
      <div className="filter">
        <a className="icon" href="#" data-bs-toggle="dropdown">
          {/* <i className="bi bi-three-dots"></i> */}
        </a>
        <ul className="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
          <li className="dropdown-header text-start">
            <h6>Filter</h6>
          </li>
          <li>
            <a className="dropdown-item" href="#">
              Today
            </a>
          </li>
          <li>
            <a className="dropdown-item" href="#">
              This Month
            </a>
          </li>
          <li>
            <a className="dropdown-item" href="#">
              This Year
            </a>
          </li>
        </ul>
      </div>
      <div className="card-body-w">
        <h5 className="card-title-w">
          {title} <span>| Total</span>
        </h5>
        <div className="d-flex align-items-center">
          <div className="card-icon rounded-circle d-flex align-items-center justify-content-center">
            {icon}
          </div>
          <div className="ps-3">
            <h6>{count}</h6>
            <span className="text-muted small pt-2 ps-1">{label}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
);

const Dashboard = () => {
  const [counts, setCounts] = useState({
    adminCount: 0,
    moderatorCount: 0,
    customerCount: 0,
    categoryCount: 0,
    subCategoryCount: 0,
    productCount: 0,
  });

  useEffect(() => {
    axios
      .get("/api/dashboard-counts") // 🎯 غيري الـ URL حسب API بتاعتك
      .then((response) => {
        setCounts(response.data);
      })
      .catch((error) => {
        console.error("Failed to fetch dashboard data:", error);
      });
  }, []);

  return (
    <>
      <Helmet>
        <title>Home Page</title>
      </Helmet>

      <div className="pagetitle">
        <h1>Dashboard</h1>
        <nav>
          <ol className="breadcrumb">
            <li className="breadcrumb-item">
              <a href="/">Home</a>
            </li>
            <li className="breadcrumb-item active">Dashboard</li>
          </ol>
        </nav>
      </div>

      <section className="section dashboard">
        <div className="row">
          <div className="col-lg-">
            <div className="row">
              <InfoCard
                title="Admins"
                icon={<BiUserCircle size={24} />}
                count={counts.adminCount}
                label="Admins"
              />
              <InfoCard
                title="Moderators"
                // icon={<BiShield size={24} />}
                count={counts.moderatorCount}
                label="Moderators"
              />
              <InfoCard
                title="Customers"
                // icon={<BiPeople size={24} />}
                count={counts.customerCount}
                label="Customers"
              />
            </div>
          </div>

          <div className="col-lg-">
            <div className="row">
              <InfoCard
                title="Categories"
                // icon={<FaList size={20} />}
                count={counts.categoryCount}
                label="Total Categories"
              />
              <InfoCard
                title="SubCategories"
                // icon={<BiListCheck size={20} />}
                count={counts.subCategoryCount}
                label="Total SubCategories"
              />
              <InfoCard
                title="Products"
                // icon={<BiListCheck size={20} />}
                count={counts.productCount}
                label="Total Products"
              />
            </div>
          </div>
        </div>
      </section>
    </>
  );
};

export default Dashboard;

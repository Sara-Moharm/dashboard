// src/routes/ProtectedRoute.js
import React, { useEffect, useState } from "react";
import { Navigate } from "react-router-dom";
import axios from "axios";

const ProtectedRoute = ({ children, allowedRoles = [] }) => {
  const [isLoading, setIsLoading] = useState(true);
  const [isAllowed, setIsAllowed] = useState(false);
  const token = localStorage.getItem("token");

  useEffect(() => {
    if (!token) {
      setIsAllowed(false);
      setIsLoading(false);
      return;
    }

    axios
      .get("http://localhost:8000/api/user", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((res) => {
        console.log("✅ user role from /api/user:", res.data.role);
        const userRole = res.data.role;
        // ✅ لو محددتش أدوار مسموحة → السماح للجميع
        if (allowedRoles.length === 0 || allowedRoles.includes(userRole)) {
          setIsAllowed(true);
        } else {
          setIsAllowed(false);
        }
        setIsLoading(false);
      })
      .catch((err) => {
        console.error("Token check failed", err);
        setIsAllowed(false);
        setIsLoading(false);
      });
  }, [token, allowedRoles]);

  if (isLoading) {
    return <div className="text-center mt-5">🔐 Checking access...</div>;
  }

  return isAllowed ? children : <Navigate to="/" replace />;
};

export default ProtectedRoute;

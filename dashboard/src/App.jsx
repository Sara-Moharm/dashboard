import { useState } from "react";
import React from "react";

import MainLayout from "./MainLayout";
import { BrowserRouter, Route, Routes } from "react-router-dom";
import Home from "./Pages/Home";
// import Dashboard from "./Pages/Dashboard";

import OrderList from "./Component/OrderList";
import Customer from "./Pages/Customer";
import Reviwes from "./Pages/Reviwes";
import PendingOrderItems from "./Pages/PendingOrderItems";
import ReadyOrders from "./Pages/ReadyOrders";
import LoginPage from "./Pages/LoginPage";
import ProtectedRoute from "./routes/ProtectedRoute";
import StaffOwnOrderItems from "./Pages/StaffOwnOrderItems";
import DeliveringOrders from "./Pages/DeliveringOrders";
import StaffForm from "./Component/StaffForm";
import Sidebar from "./Component/SideBar";

function App() {
  return (
    <>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<LoginPage />} />
          <Route
            path="/home"
            element={
              <ProtectedRoute allowedRoles={["admin", "super_admin"]}>
                {/* <MainLayout /> */}
                <Home />
              </ProtectedRoute>
            }
          />

          <Route
            path="/pending-order-items"
            element={
              <ProtectedRoute allowedRoles={["kitchen_staff"]}>
                <MainLayout />
                <PendingOrderItems />
              </ProtectedRoute>
            }
          />

          <Route
            path="/ready-orders"
            element={
              <ProtectedRoute allowedRoles={["delivery"]}>
                <MainLayout />
                <ReadyOrders />
              </ProtectedRoute>
            }
          />
          <Route path="/order" element={<OrderList />} />
          <Route path="/customer" element={<Customer />} />
          <Route path="/Review" element={<Reviwes />} />
          <Route
            path="/my-order-items"
            element={
              <ProtectedRoute allowedRoles={["kitchen_staff"]}>
                <MainLayout />
                <StaffOwnOrderItems />
              </ProtectedRoute>
            }
          />
          <Route
            path="/delivering-orders"
            element={
              <ProtectedRoute allowedRoles={["delivery"]}>
                <MainLayout />
                <DeliveringOrders />
              </ProtectedRoute>
            }
          />

          <Route
            path="/admin/create"
            element={
              <ProtectedRoute allowedRoles={["admin", "super_admin"]}>
                {/* <Sidebar /> */}
                <StaffForm role="admin" />
              </ProtectedRoute>
            }
          />
          <Route
            path="/kitchen_staff/create"
            element={
              <ProtectedRoute allowedRoles={["admin", "super_admin"]}>
                {/* <Sidebar /> */}
                <StaffForm role="kitchen_staff" />
              </ProtectedRoute>
            }
          />
          <Route
            path="/delivery/create"
            element={
              <ProtectedRoute allowedRoles={["admin", "super_admin"]}>
                {/* <Sidebar /> */}
                <StaffForm role="delivery" />
              </ProtectedRoute>
            }
          />

          {/* <Route path="/dashboard" element={<Dashboard />} /> */}

          {/* <Route path='/about' element={<AboutUs />}/>
            <Route path='/contact' element={<ContactUs />}/>
            <Route path='/register' element={<Regestration />}/>
            <Route path='/Login' element={<Login />}/>
            <Route path='/SingIN' element={<SignUp />}/>
            <Route path='/desert' element={<Desert />}/>
            <Route path='/cart' element={<Cart />}/>
            <Route path='/drink' element={<Drink />}/>
            <Route path='/food' element={<Food />}/>
            <Route path='/Profile' element={<Profile />}/>
            <Route path='/checkOut' element={<CheckOut />}/>
            <Route path='/Payment' element={<Payment />}/>
            <Route path='/track' element={<Track />}/>
            <Route path='/follow' element={<Follow />}/> */}
        </Routes>
      </BrowserRouter>
    </>
  );
}

export default App;

import React, { useState } from "react";
import { Form, Button, Row, Col, Container } from "react-bootstrap";
import axios from "axios";
import SideBar from "./SideBar";
import MainLayout from "../MainLayout";

const StaffForm = ({ role }) => {
  const [formData, setFormData] = useState({
    fname: "",
    lname: "",
    email: "",
    phone_number: "",
    password: "",
    password_confirmation: "",
    role: role,
    shift_start: "",
    shift_end: "",
  });

  const handleChange = (e) => {
    setFormData((prev) => ({
      ...prev,
      [e.target.name]: e.target.value,
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const token = localStorage.getItem("token");
    axios
      .post(`http://localhost:8000/api/admin/staff/register`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((res) => {
        console.log("Success:", res.data);
        alert("Staff created successfully!");
      })
      .catch((err) => {
        console.error("Error:", err);
        alert("Failed to create staff");
      });
  };

  return (
    <div className="d-flex">
      {/* Sidebar */}
      <SideBar />
      <div className="flex-1 flex flex-col">
        <MainLayout />
        <Container className="mt-5">
          <h3 className="mb-4 text-capitalize">
            Create New {role.replace("_", " ")}
          </h3>
          <Form onSubmit={handleSubmit}>
            <Row>
              <Col md={6}>
                <Form.Group className="mb-3">
                  <Form.Label>First Name</Form.Label>
                  <Form.Control
                    type="text"
                    name="fname"
                    value={formData.fname}
                    onChange={handleChange}
                    required
                  />
                </Form.Group>
              </Col>

              <Col md={6}>
                <Form.Group className="mb-3">
                  <Form.Label>Last Name</Form.Label>
                  <Form.Control
                    type="text"
                    name="lname"
                    value={formData.lname}
                    onChange={handleChange}
                    required
                  />
                </Form.Group>
              </Col>
            </Row>

            <Row>
              <Col md={6}>
                <Form.Group className="mb-3">
                  <Form.Label>Email</Form.Label>
                  <Form.Control
                    type="email"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                    required
                  />
                </Form.Group>
              </Col>

              <Col md={6}>
                <Form.Group className="mb-3">
                  <Form.Label>Phone Number</Form.Label>
                  <Form.Control
                    type="text"
                    name="phone_number"
                    value={formData.phone_number}
                    onChange={handleChange}
                    required
                  />
                </Form.Group>
              </Col>
            </Row>

            <Form.Group className="mb-3">
              <Form.Label>Password</Form.Label>
              <Form.Control
                type="password"
                name="password"
                value={formData.password}
                onChange={handleChange}
                required
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label>Confirm Password</Form.Label>
              <Form.Control
                type="password"
                name="password_confirmation"
                value={formData.password_confirmation}
                onChange={handleChange}
                required
              />
            </Form.Group>

            {/* Optional shift fields only for kitchen_staff or delivery */}
            {(role === "kitchen_staff" || role === "delivery") && (
              <Row>
                <Col md={6}>
                  <Form.Group className="mb-3">
                    <Form.Label>Shift Start</Form.Label>
                    <Form.Control
                      type="datetime-local"
                      name="shift_start"
                      value={formData.shift_start}
                      onChange={handleChange}
                    />
                  </Form.Group>
                </Col>

                <Col md={6}>
                  <Form.Group className="mb-3">
                    <Form.Label>Shift End</Form.Label>
                    <Form.Control
                      type="datetime-local"
                      name="shift_end"
                      value={formData.shift_end}
                      onChange={handleChange}
                    />
                  </Form.Group>
                </Col>
              </Row>
            )}

            <Button type="submit" variant="primary" className="mt-3">
              ➕ Create {role.replace("_", " ")}
            </Button>
          </Form>
        </Container>
      </div>
    </div>
  );
};

export default StaffForm;

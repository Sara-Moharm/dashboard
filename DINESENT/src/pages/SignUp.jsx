import React, { useState } from "react";
import axios from "axios";
import {
  Container,
  Card,
  Form,
  Button,
  Alert,
  Spinner,
  Row,
  Col,
} from "react-bootstrap";
import { useNavigate } from "react-router-dom";

const SignUp = () => {
  const navigate = useNavigate();
  const [form, setForm] = useState({
    fname: "",
    lname: "",
    email: "",
    phone_number: "",
    second_phone_number: "",
    city: "",
    address: {
      street_address: "",
      district: "",
      city: "",
    },
    district: "",
    password: "",
    password_confirmation: "",
  });

  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    const { name, value } = e.target;

    if (["address", "district", "city"].includes(name)) {
      setForm((prev) => ({
        ...prev,
        address: {
          ...prev.address,
          [name]: value,
        },
      }));
    } else {
      setForm((prev) => ({
        ...prev,
        [name]: value,
      }));
    }
  };

  const handleRegister = (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    setSuccess("");

    axios
      .post("http://localhost:8000/api/register", form)
      .then(() => {
        setSuccess("Registration successful! Redirecting to login...");
        setTimeout(() => navigate("/login"), 1500);
      })
      .catch((err) => {
        const msg = err.response?.data?.message || "Registration failed.";
        setError(msg);
      })
      .finally(() => setLoading(false));
  };

  return (
    <div className="signup-page d-flex align-items-center justify-content-center vh-100 bg-light">
      <Card
        className="shadow rounded-4 p-4"
        style={{ width: "100%", maxWidth: 600 }}
      >
        <Card.Body>
          <h2 className="text-center mb-4 fw-bold text-primary">
            Create Account
          </h2>
          <p className="text-muted text-center mb-4">Customer Registration</p>

          {error && <Alert variant="danger">{error}</Alert>}
          {success && <Alert variant="success">{success}</Alert>}

          <Form onSubmit={handleRegister}>
            <Row>
              <Col md={6} className="mb-3">
                <Form.Label>First Name</Form.Label>
                <Form.Control
                  name="fname"
                  required
                  value={form.fname}
                  onChange={handleChange}
                />
              </Col>
              <Col md={6} className="mb-3">
                <Form.Label>Last Name</Form.Label>
                <Form.Control
                  name="lname"
                  required
                  value={form.lname}
                  onChange={handleChange}
                />
              </Col>
            </Row>

            <Row>
              <Col md={6} className="mb-3">
                <Form.Label>Email</Form.Label>
                <Form.Control
                  type="email"
                  name="email"
                  required
                  value={form.email}
                  onChange={handleChange}
                />
              </Col>
              <Col md={6} className="mb-3">
                <Form.Label>phone_number Number</Form.Label>
                <Form.Control
                  name="phone_number"
                  required
                  value={form.phone_number}
                  onChange={handleChange}
                />
              </Col>
            </Row>

            <Row>
              <Col md={6} className="mb-3">
                <Form.Label>Second phone_number (optional)</Form.Label>
                <Form.Control
                  name="second_phone_number"
                  value={form.second_phone_number}
                  onChange={handleChange}
                />
              </Col>
              <Col md={6} className="mb-3">
                <Form.Label>City</Form.Label>
                <Form.Control
                  name="city"
                  required
                  value={form.city}
                  onChange={handleChange}
                />
              </Col>
            </Row>

            <Row>
              <Col md={6} className="mb-3">
                <Form.Label>Street Address</Form.Label>
                <Form.Control
                  name="address"
                  required
                  value={form.address}
                  onChange={handleChange}
                />
              </Col>
              <Col md={6} className="mb-3">
                <Form.Label>District (optional)</Form.Label>
                <Form.Control
                  name="district"
                  value={form.district}
                  onChange={handleChange}
                />
              </Col>
            </Row>

            <Row>
              <Col md={6} className="mb-3">
                <Form.Label>Password</Form.Label>
                <Form.Control
                  type="password"
                  name="password"
                  required
                  value={form.password}
                  onChange={handleChange}
                />
              </Col>
              <Col md={6} className="mb-3">
                <Form.Label>Confirm Password</Form.Label>
                <Form.Control
                  type="password"
                  name="password_confirmation"
                  required
                  value={form.password_confirmation}
                  onChange={handleChange}
                />
              </Col>
            </Row>

            <Button
              variant="primary"
              type="submit"
              className="w-100 mt-2 rounded-3 fw-semibold"
              disabled={loading}
            >
              {loading ? <Spinner animation="border" size="sm" /> : "Sign Up"}
            </Button>
          </Form>

          <div className="mt-3 text-center text-muted small">
            Already have an account? <a href="/login">Log in</a>
          </div>
        </Card.Body>
      </Card>
    </div>
  );
};

export default SignUp;

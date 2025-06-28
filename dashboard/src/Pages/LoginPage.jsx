import React, { useState } from "react";
import axios from "axios";
import { Container, Card, Form, Button, Alert, Spinner } from "react-bootstrap";
import { useNavigate } from "react-router-dom";
import { FaEye, FaEyeSlash } from "react-icons/fa";

const LoginPage = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const navigate = useNavigate();

  const handleLogin = (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");

    axios
      .post("http://localhost:8000/api/staff/login", { email, password })
      .then((res) => {
        const { token, user_id, first_name, role } = res.data;

        // Save to localStorage
        localStorage.setItem("token", token);
        localStorage.setItem("user_id", user_id);
        localStorage.setItem("first_name", first_name);
        localStorage.setItem("role", role);

        // Redirect based on role
        if (role === "admin" || role === "super_admin") {
          navigate("/home");
        } else if (role === "kitchen_staff") {
          navigate("/pending-order-items");
        } else if (role === "delivery") {
          navigate("/ready-orders");
        } else {
          setError("Unauthorized role");
        }
      })
      .catch(() => {
        setError("Invalid email or password");
        setLoading(false);
      });
  };

  return (
    <div className="login-page d-flex align-items-center justify-content-center vh-100 bg-light">
      <Card
        className="shadow rounded-4 p-4"
        style={{ width: "100%", maxWidth: 400 }}
      >
        <Card.Body>
          <h2 className="text-center mb-4 fw-bold text-primary">
            Welcome Back
          </h2>
          <p className="text-muted text-center mb-4">Sign in to your account</p>

          {error && <Alert variant="danger">{error}</Alert>}

          <Form onSubmit={handleLogin}>
            <Form.Group className="mb-3">
              <Form.Label className="fw-semibold">Email address</Form.Label>
              <Form.Control
                type="email"
                placeholder="you@example.com"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="rounded-3"
              />
            </Form.Group>
            <Form.Group className="mb-3">
              <Form.Label className="fw-semibold">Password</Form.Label>
              <div className="position-relative">
                <Form.Control
                  type={showPassword ? "text" : "password"}
                  placeholder="••••••••"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="rounded-3 pe-5"
                />
                <span
                  onClick={() => setShowPassword(!showPassword)}
                  className="position-absolute top-50 end-0 translate-middle-y me-3"
                  style={{ cursor: "pointer", color: "#6c757d" }}
                >
                  {showPassword ? <FaEyeSlash /> : <FaEye />}
                </span>
              </div>
            </Form.Group>

            <Button
              variant="primary"
              type="submit"
              className="w-100 mt-2 rounded-3 fw-semibold"
              disabled={loading}
            >
              {loading ? <Spinner animation="border" size="sm" /> : "Sign In"}
            </Button>
          </Form>

          <div className="mt-3 text-center text-muted small">
            Don't have an account? <a href="#">Contact admin</a>
          </div>
        </Card.Body>
      </Card>
    </div>
  );
};

export default LoginPage;

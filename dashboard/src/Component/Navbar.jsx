import React, { useEffect, useState } from "react";
import { Navbar, Container, Nav, NavDropdown, Image } from "react-bootstrap";
import axios from "axios";
import { useNavigate } from "react-router-dom";

const AppNavbar = () => {
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  const token = localStorage.getItem("token");

  useEffect(() => {
    if (token) {
      axios
        .get("http://localhost:8000/api/user", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((res) => {
          setUser(res.data);
        })
        .catch(() => {
          // لو التوكن مش صالح
          localStorage.removeItem("token");
          navigate("/");
        });
    }
  }, [token, navigate]);

  const handleLogout = () => {
    localStorage.removeItem("token");
    navigate("/");
  };

  return (
    <Navbar bg="light" expand="lg" className="shadow-sm px-3">
      <Container>
        <Navbar.Brand className="fw-bold fs-4 text-primary">
          DineSent
        </Navbar.Brand>
        <Navbar.Toggle aria-controls="main-navbar" />
        <Navbar.Collapse id="main-navbar" className="justify-content-end">
          <Nav>
            {user && (
              <NavDropdown
                title={
                  <>
                    <Image
                      src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                      roundedCircle
                      width="32"
                      height="32"
                      className="me-2"
                    />
                    {user.first_name}
                  </>
                }
                id="user-dropdown"
              >
                <NavDropdown.Item
                  disabled
                  className="text-wrap"
                  style={{ maxWidth: "300px", whiteSpace: "normal" }}
                >
                  <strong>Shift Start:</strong> {user.shift_start || "N/A"}
                </NavDropdown.Item>
                <NavDropdown.Item
                  disabled
                  className="text-wrap"
                  style={{ maxWidth: "300px", whiteSpace: "normal" }}
                >
                  <strong>Shift End:</strong> {user.shift_end || "N/A"}
                </NavDropdown.Item>
                <NavDropdown.Divider />
                <NavDropdown.Item onClick={handleLogout}>
                  Logout
                </NavDropdown.Item>
              </NavDropdown>
            )}
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
};

export default AppNavbar;

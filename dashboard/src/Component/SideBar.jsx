import React from "react";
import { Accordion, Container, Nav } from "react-bootstrap";
import { Link } from "react-router-dom";

const CloudKitchenLogo = () => (
  //   <svg
  //     xmlns="http://www.w3.org/2000/svg"
  //     width="60"
  //     height="60"
  //     viewBox="0 0 64 64"
  //     fill="none"
  //   >
  //     {/* السحابة */}
  //     <path
  //       d="M20 40H44C48.4183 40 52 36.4183 52 32C52 28.0981 49.1825 24.7895 45.4 24.1111C44.0185 18.6667 39.0667 15 33.3333 15C30.7854 15 28.4357 15.8527 26.5458 17.2786C24.6643 13.5385 20.6358 11 16 11C9.37258 11 4 16.3726 4 23C4 28.0191 7.37031 32.2213 12 33.6667C11.744 34.3839 11.6 35.1704 11.6 36C11.6 38.866 14.134 41.4 17 41.4H20Z"
  //       fill="#86C5FF"
  //     />
  //     {/* القبعة */}
  //     {/* دائرة ديكور
  //     <circle cx="32" cy="56" r="3" fill="#FFA726" /> */}
  //     <path
  //       d="M25 14C25 11 28 8 32 8C36 8 39 11 39 14C39 16 37 18 35 18V20H29V18C27 18 25 16 25 14Z"
  //       fill="#FF7043"
  //     />
  //     <rect x="29" y="20" width="6" height="3" fill="#FF7043" />
  //   </svg>
  //   <img src="/assets/logo.png" alt="Chef Logo" width={100} />
  //   <img src="/assets/logo2.png" alt="Chef Logo" width={100} />
  <img src="/assets/logo3.png" alt="Chef Logo" width={100} />
);

const Sidebar = () => {
  const roles = [
    { name: "👤 Customer", route: "customer" },
    { name: "🛠️ Admin", route: "admin" },
    { name: "👨‍🍳 Kitchen Staff", route: "kitchen_staff" },
    { name: "🚚 Delivery", route: "delivery" },
  ];

  return (
    <Container
      fluid
      className="p-3 bg-light border-end shadow-sm"
      style={{ minHeight: "100vh", width: "250px" }}
    >
      {/* Logo */}
      <div className="mb-4 text-center">
        <Link
          to="/"
          className="text-decoration-none d-flex justify-content-center"
        >
          <CloudKitchenLogo />
        </Link>
      </div>

      {/* Accordion Menu */}
      <Accordion defaultActiveKey="0" alwaysOpen>
        {roles.map((role, index) => (
          <Accordion.Item eventKey={index.toString()} key={role.route}>
            <Accordion.Header className="text-capitalize">
              {role.name}
            </Accordion.Header>
            <Accordion.Body className="p-2">
              <Nav className="flex-column">
                {role.route !== "customer" && (
                  <Nav.Link as={Link} to={`/${role.route}/create`}>
                    ➕ Create
                  </Nav.Link>
                )}
                {role.route !== "admin" && (
                  <Nav.Link as={Link} to={`/${role.route}/view`}>
                    👀 View All
                  </Nav.Link>
                )}
                <Nav.Link as={Link} to={`/${role.route}/update`}>
                  ✏️ Update
                </Nav.Link>
                <Nav.Link as={Link} to={`/${role.route}/delete`}>
                  🗑️ Delete
                </Nav.Link>
              </Nav>
            </Accordion.Body>
          </Accordion.Item>
        ))}
        {/* Categories Section */}
        <Accordion.Item eventKey="100">
          <Accordion.Header>📦 Categories</Accordion.Header>
          <Accordion.Body className="p-2">
            <Nav className="flex-column">
              <Nav.Link as={Link} to="/categories/create">
                ➕ Create
              </Nav.Link>
              <Nav.Link as={Link} to="/categories/view">
                👀 View All
              </Nav.Link>
              <Nav.Link as={Link} to="/categories/update">
                ✏️ Update
              </Nav.Link>
              <Nav.Link as={Link} to="/categories/delete">
                🗑️ Delete
              </Nav.Link>
            </Nav>
          </Accordion.Body>
        </Accordion.Item>

        {/* Category Dishes Section */}
        <Accordion.Item eventKey="101">
          <Accordion.Header>🍽️ Category Dishes</Accordion.Header>
          <Accordion.Body className="p-2">
            <Nav className="flex-column">
              <Nav.Link as={Link} to="/category_dishes/create">
                ➕ Create
              </Nav.Link>
              <Nav.Link as={Link} to="/category_dishes/view">
                👀 View All
              </Nav.Link>
              <Nav.Link as={Link} to="/category_dishes/update">
                ✏️ Update
              </Nav.Link>
              <Nav.Link as={Link} to="/category_dishes/delete">
                🗑️ Delete
              </Nav.Link>
            </Nav>
          </Accordion.Body>
        </Accordion.Item>

        {/* Orders Section */}
        <Accordion.Item eventKey="102">
          <Accordion.Header>🧾 Orders</Accordion.Header>
          <Accordion.Body className="p-2">
            <Nav className="flex-column">
              <Nav.Link as={Link} to="/orders/create">
                ➕ Create
              </Nav.Link>
              <Nav.Link as={Link} to="/orders/view">
                👀 View All
              </Nav.Link>
              <Nav.Link as={Link} to="/orders/update">
                ✏️ Update
              </Nav.Link>
              <Nav.Link as={Link} to="/orders/delete">
                🗑️ Delete
              </Nav.Link>
            </Nav>
          </Accordion.Body>
        </Accordion.Item>
      </Accordion>
    </Container>
  );
};

export default Sidebar;

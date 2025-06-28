import React, { useEffect, useState } from "react";
import axios from "axios";
import { Table, Button } from "react-bootstrap";
import { useNavigate } from "react-router-dom";

const DeliveringOrders = () => {
  const [orders, setOrders] = useState([]);
  const token = localStorage.getItem("token");
  const navigate = useNavigate();

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/delivery/orders/delivering", {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((res) => {
        if (res.data.success) {
          setOrders(res.data.orders);
        }
      })
      .catch((err) => {
        console.error("Error fetching delivering orders:", err);
      });
  }, []);

  const markAsDelivered = (id) => {
    axios
      .patch(
        `http://localhost:8000/api/delivery/orders/${id}/delivered`,
        {},
        { headers: { Authorization: `Bearer ${token}` } }
      )
      .then(() => {
        setOrders((prev) =>
          prev.map((order) =>
            order.id === id ? { ...order, status: "delivered" } : order
          )
        );
      })
      .catch((err) => {
        console.error("Failed to mark as delivered:", err);
      });
  };

  return (
    <div className="container mt-4">
      <h3 className="mb-4">Delivering Orders</h3>
      <Table striped bordered hover responsive>
        <thead>
          <tr>
            <th className="text-center">#</th>
            <th className="text-center">Customer Name</th>
            <th className="text-center">Customer address</th>
            <th className="text-center">Phone number</th>
            <th className="text-center">Second Phone number</th>
            <th className="text-center">Total Price</th>
            <th className="text-center">Status</th>
            <th className="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          {orders.length > 0 ? (
            orders.map((order, index) => (
              <tr
                key={order.id}
                className={
                  order.status === "delivered" ? "table-secondary" : ""
                }
              >
                <td>{index + 1}</td>
                <td>{order.customer?.user?.fname || "N/A"}</td>
                <td>{order.address?.street_address || "N/A"}</td>
                <td>{order.customer?.user?.phone_number || "N/A"}</td>
                <td>{order.customer?.second_phone_number || "N/A"}</td>
                <td>{order.total_price} EGP</td>
                <td>
                  <span className="badge bg-info text-dark">
                    {order.status}
                  </span>
                </td>
                <td>
                  <Button
                    variant="success"
                    size="sm"
                    className="me-2"
                    disabled={order.status === "delivered"}
                    onClick={() => markAsDelivered(order.id)}
                  >
                    Done
                  </Button>
                  <Button
                    variant="secondary"
                    size="sm"
                    onClick={() => navigate("/ready-orders")}
                  >
                    Go to Ready Orders
                  </Button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5" className="text-center">
                No delivering orders.
              </td>
            </tr>
          )}
        </tbody>
      </Table>
    </div>
  );
};

export default DeliveringOrders;

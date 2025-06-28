import React, { useEffect, useState } from "react";
import axios from "axios";
import { Button, Card } from "react-bootstrap";
import { useNavigate } from "react-router-dom";

const OrderCard = ({ order, onAccept }) => (
  <div className="col-md-3 mb-4">
    <Card className="shadow-sm rounded-4">
      <Card.Body>
        <Card.Title className="text-center mb-3 fs-3 text-capitalize">
          Order #{order.id}
        </Card.Title>

        <div className="d-flex justify-content-between align-items-center">
          <Card.Text className="text-start">
            <strong>Customer Address:</strong>{" "}
            {order.address?.street_address || "N/A"}
            <br />
            <strong>Total Price:</strong>{" "}
            <span
              className={`badge ${
                order.status === "ready" ? "bg-warning text-dark" : "bg-success"
              }`}
            >
              {order.total_price} EGP
            </span>
          </Card.Text>

          {order.status === "ready" && (
            <Button variant="success" onClick={() => onAccept(order.id)}>
              Accept
            </Button>
          )}
        </div>
      </Card.Body>
    </Card>
  </div>
);

const ReadyOrders = () => {
  const [orders, setOrders] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const navigate = useNavigate();
  const token = localStorage.getItem("token");
  useEffect(() => {
    axios
      .get("http://localhost:8000/api/delivery/orders/ready", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((res) => {
        if (res.data.success) {
          setOrders(res.data.orders);
        }
      })
      .catch((err) => console.error("Error fetching items:", err))
      .finally(() => setIsLoading(false));
  }, []);

  const handleAccept = (orderId) => {
    const token = localStorage.getItem("token");

    axios
      .patch(
        `http://localhost:8000/api/delivery/orders/${orderId}/delivering`,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      )
      .then((res) => {
        setOrders((prev) => prev.filter((order) => order.id !== orderId));
        navigate("/delivering-orders");
      })
      .catch((err) => {
        console.error("Error updating order:", err);
        alert("Failed to update order status");
      });
  };

  return (
    <div className="container mt-4">
      <h3 className="mb-4">Ready Orders</h3>
      <div className="row">
        {isLoading ? (
          <p>Loading ready orders...</p>
        ) : orders.length > 0 ? (
          orders.map((order) => (
            <OrderCard key={order.id} order={order} onAccept={handleAccept} />
          ))
        ) : (
          <p>No ready orders found.</p>
        )}
      </div>
    </div>
  );
};

export default ReadyOrders;

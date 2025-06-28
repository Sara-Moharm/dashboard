import React, { useEffect, useState } from "react";
import axios from "axios";
import { Button, Card } from "react-bootstrap";
import { useNavigate } from "react-router-dom";

// كارت يعرض بيانات الطلب
const OrderItemCard = ({ item, onAccept }) => (
  <div className="col-md-3 mb-4">
    <Card className="shadow-sm rounded-4">
      <Card.Body>
        <Card.Title className="text-center mb-3 fs-4 text-capitalize">
          {item.category_dish?.title}
        </Card.Title>

        <div className="d-flex justify-content-between align-items-center">
          <Card.Text>
            <strong>Quantity:</strong>{" "}
            <span
              className={`badge ${
                item.status === "pending"
                  ? "bg-warning text-dark"
                  : "bg-success"
              }`}
            >
              {item.quantity}
            </span>
          </Card.Text>

          {item.status === "pending" && (
            <Button variant="success" onClick={() => onAccept(item.id)}>
              Accept
            </Button>
          )}
        </div>
      </Card.Body>
    </Card>
  </div>
);

// الكومبوننت الرئيسية
const PendingOrderItems = () => {
  const [orderItems, setOrderItems] = useState({ order_items: [] });
  const [isLoading, setIsLoading] = useState(true);
  const navigate = useNavigate();
  const token = localStorage.getItem("token");

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/kitchen_staff/order_items/pending", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((res) => {
        if (res.data.success) {
          setOrderItems(res.data);
        }
      })
      .catch((err) => console.error("Error fetching items:", err))
      .finally(() => setIsLoading(false));
  }, []);

  const handleAccept = (id) => {
    axios
      .post(
        `http://localhost:8000/api/kitchen_staff/order_items/${id}/prepare`,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      )
      .then(() => {
        setOrderItems((prev) => ({
          ...prev,
          order_items: prev.order_items.filter((item) => item.id !== id),
        }));
        navigate("/my-order-items");
      })
      .catch((err) => {
        console.error("Error updating order item:", err);
        alert("Failed to update order item status");
      });
  };

  return (
    <div className="container mt-4">
      <h3 className="mb-4">Pending Order Items</h3>
      <div className="row">
        {isLoading ? (
          <p>Loading pending order items...</p>
        ) : orderItems?.order_items?.length > 0 ? (
          orderItems.order_items.map((item) => (
            <OrderItemCard key={item.id} item={item} onAccept={handleAccept} />
          ))
        ) : (
          <p>No pending order items found.</p>
        )}
      </div>
    </div>
  );
};

export default PendingOrderItems;

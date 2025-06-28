import React, { useEffect, useState } from "react";
import axios from "axios";
import { Table, Button } from "react-bootstrap";
import { useNavigate } from "react-router-dom";

const StaffOwnOrderItems = () => {
  const [items, setItems] = useState([]);
  const navigate = useNavigate();
  const token = localStorage.getItem("token");

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/kitchen_staff/order_items/preparing", {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((res) => {
        if (res.data.success) {
          setItems(res.data.order_items);
        }
      })
      .catch((err) => {
        console.error("Failed to load items:", err);
      });
  }, []);

  const markAsReady = (id) => {
    axios
      .patch(
        `http://localhost:8000/api/kitchen_staff/order_items/${id}/ready`,
        {},
        { headers: { Authorization: `Bearer ${token}` } }
      )
      .then(() => {
        // ✅ تعديل العنصر محليًا لعرض النتيجة بدون انتظار إعادة تحميل
        setItems((prevItems) =>
          prevItems.map((item) =>
            item.id === id ? { ...item, status: "ready" } : item
          )
        );
      })
      .catch((err) => {
        console.error("Failed to mark as ready:", err);
      });
  };

  const returnToPending = (id) => {
    axios
      .patch(
        `http://localhost:8000/api/kitchen_staff/order_items/${id}/pending`,
        {},
        { headers: { Authorization: `Bearer ${token}` } }
      )
      .then(() => {
        navigate("/pending-order-items");
      })
      .catch((err) => {
        console.error("Failed to return to pending:", err);
      });
  };

  return (
    <div className="container mt-4">
      <h3 className="mb-4">My Order Items</h3>
      <Table striped bordered hover responsive>
        <thead>
          <tr>
            <th className="text-center">#</th>
            <th className="text-center">Category Dish</th>
            <th className="text-center">Quantity</th>
            <th className="text-center">Price</th>
            <th className="text-center">Status</th>
            <th className="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          {items.length > 0 ? (
            items.map((item, index) => (
              <tr
                key={item.id}
                className={item.status === "ready" ? "table-secondary" : ""}
              >
                <td>{index + 1}</td>
                <td>{item.category_dish?.title || "N/A"}</td>
                <td>{item.quantity}</td>
                <td>{item.price.toFixed(2)} EGP</td>
                <td>
                  <span
                    className={`badge ${
                      item.status === "ready"
                        ? "bg-secondary"
                        : "bg-warning text-dark"
                    }`}
                  >
                    {item.status}
                  </span>
                </td>
                <td>
                  <Button
                    variant="success"
                    size="sm"
                    className="me-2"
                    onClick={() => markAsReady(item.id)}
                    disabled={item.status === "ready"} // ✅ الزر يعطل لو جاهز
                  >
                    Done
                  </Button>
                  <Button
                    variant="outline-danger"
                    size="sm"
                    onClick={() => returnToPending(item.id)}
                  >
                    Return
                  </Button>
                  <Button
                    variant="secondary"
                    size="sm"
                    className="ms-2"
                    onClick={() => navigate("/pending-order-items")}
                  >
                    Go to Pending
                  </Button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5" className="text-center">
                No items assigned.
              </td>
            </tr>
          )}
        </tbody>
      </Table>
    </div>
  );
};

export default StaffOwnOrderItems;

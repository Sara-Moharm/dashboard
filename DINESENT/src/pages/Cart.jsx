import React, { useEffect, useState } from "react";
import { useSelector, useDispatch } from "react-redux";
import NavBar from "../components/NavBar";
import { useNavigate } from "react-router-dom";
import { FaTrashAlt } from "react-icons/fa";

const Cart = () => {
  const currentUser = useSelector((state) => state.user.currentUser);
  const [cartItems, setCartItems] = useState([]);
  const [selectedItems, setSelectedItems] = useState([]);
  const [total, setTotal] = useState(0);
  const navigate = useNavigate();

  const fetchCart = () => {
    fetch("http://localhost:8000/api/cart", {
      headers: {
        Authorization: `Bearer ${currentUser?.token}`,
      },
    })
      .then((res) => res.json())
      .then((data) => {
        setCartItems(data.items);
        setTotal(data.total);
      })
      .catch((err) => console.error("Error fetching cart:", err));
  };

  useEffect(() => {
    fetchCart();
  }, []);

  const handleCheckboxChange = (id) => {
    setSelectedItems((prev) =>
      prev.includes(id) ? prev.filter((itemId) => itemId !== id) : [...prev, id]
    );
  };

  const handleIncrement = (id) => {
    fetch(`http://localhost:8000/api/cart/items/${id}/increment`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${currentUser?.token}`,
      },
    }).then(fetchCart);
  };

  const handleDecrement = (id) => {
    fetch(`http://localhost:8000/api/cart/items/${id}/decrement`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${currentUser?.token}`,
      },
    }).then(fetchCart);
  };

  const handleRemove = (id) => {
    fetch(`http://localhost:8000/api/cart/remove/${id}`, {
      method: "DELETE",
      headers: {
        Authorization: `Bearer ${currentUser?.token}`,
      },
    }).then(fetchCart);
  };

  const handleClear = () => {
    fetch(`http://localhost:8000/api/cart/clear`, {
      method: "DELETE",
      headers: {
        Authorization: `Bearer ${currentUser?.token}`,
      },
    }).then(fetchCart);
  };

  const handleCheckout = () => {
    const selectedProducts = cartItems.filter((item) =>
      selectedItems.includes(item.id)
    );
    if (selectedProducts.length === 0) {
      alert("Please select at least one item to checkout.");
      return;
    }

    fetch(`http://localhost:8000/api/cart/checkout`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${currentUser?.token}`,
      },
      body: JSON.stringify({ selected_items: selectedItems }),
    })
      .then((res) => res.json())
      .then((data) => {
        console.log("Checkout complete:", data);
        navigate("/success");
      })
      .catch((err) => console.error("Checkout error:", err));
  };

  return (
    <>
      <NavBar />
      <h2>YOUR Cart</h2>
      <button
        style={{
          display: "flex",
          justifyContent: "center",
          margin: "auto",
          marginBottom: "10px",
          background: "red",
        }}
        onClick={handleClear}
      >
        Clear Cart
      </button>

      <div
        className="cart-grid"
        style={{
          display: "grid",
          gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))",
          gap: "20px",
          padding: "20px",
        }}
      >
        {cartItems.map((item) => (
          <div className="card" key={item.id}>
            <input
              type="checkbox"
              checked={selectedItems.includes(item.id)}
              onChange={() => handleCheckboxChange(item.id)}
              style={{ marginBottom: "10px" }}
            />

            <img
              src={item.image}
              alt={item.title}
              style={{ width: "100%", height: "200px", objectFit: "contain" }}
            />
            <h3 className="product-title">{item.title}</h3>

            <div className="product-footer">
              <span style={{ color: "black" }} className="product-price">
                price: ${item.price}
              </span>
            </div>

            <div
              style={{
                display: "flex",
                justifyContent: "space-between",
                margin: "15px",
              }}
            >
              <button onClick={() => handleDecrement(item.id)} className="btn">
                -
              </button>
              <p>{item.quantity}</p>
              <button onClick={() => handleIncrement(item.id)} className="btn">
                +
              </button>
            </div>

            <p>Total Price: ${(item.price * item.quantity).toFixed(2)}</p>
            <button
              className="remove-item"
              style={{
                display: "flex",
                justifyContent: "center",
                margin: "auto",
                marginBottom: "10px",
                background: "#2C2C2CC7",
              }}
              onClick={() => handleRemove(item.id)}
            >
              <FaTrashAlt />
            </button>
          </div>
        ))}
      </div>

      <div style={{ display: "flex", justifyContent: "center" }}>
        <button
          style={{
            background: "green",
            color: "white",
            fontSize: "16px",
            margin: "10px",
          }}
        >
          Total Price: ${total}
        </button>

        <button
          onClick={handleCheckout}
          style={{
            background: "green",
            color: "white",
            fontSize: "16px",
            border: "none",
            borderRadius: "8px",
            zIndex: 9999,
            boxShadow: "0 4px 10px rgba(0,0,0,0.2)",
            margin: "10px",
          }}
        >
          Check Out
        </button>
      </div>
    </>
  );
};

export default Cart;

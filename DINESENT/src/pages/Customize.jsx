import React, { useState, useEffect } from "react";
import "../Style/CalorieFilter.css";
import NavBar from "../components/NavBar";
import { useDispatch } from "react-redux";
import { addToCart } from "../store/cartSlice";

const MealSuggestion = () => {
  const [categoriesData, setCategoriesData] = useState({});
  const [selectedCategories, setSelectedCategories] = useState([]);
  const [calories, setCalories] = useState("");
  const [meals, setMeals] = useState([]);
  const [showCategories, setShowCategories] = useState(false);

  const dispatch = useDispatch();

  useEffect(() => {
    fetch("http://127.0.0.1:8000/api/categories")
      .then((res) => res.json())
      .then((data) => {
        if (data.success && Array.isArray(data.categories)) {
          const formatted = {};
          data.categories.forEach((cat) => {
            formatted[cat.title] = cat.category_dish;
          });
          setCategoriesData(formatted);
        }
      })
      .catch((err) => console.error("Error fetching categories:", err));
  }, []);

  const handleCategoryChange = (category) => {
    setSelectedCategories((prev) =>
      prev.includes(category)
        ? prev.filter((c) => c !== category)
        : [...prev, category]
    );
  };

  const generateMeals = () => {
    if (!calories) {
      console.warn("Missing data 🚫", { selectedCategories, calories });
      return;
    }

    fetch("http://127.0.0.1:8000/api/suggest-meals", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        categories: selectedCategories,
        calories: parseInt(calories),
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (
          Array.isArray(data.data) &&
          data.data.length > 0 &&
          data.data[0].dishes
        ) {
          setMeals(data.data);
        } else if (Array.isArray(data.data)) {
          const individualMeals = data.data.map((item) => ({
            dishes: [item],
            total_calories: item.calories,
          }));
          setMeals(individualMeals);
        } else {
          setMeals([]);
        }
      })
      .catch((err) => console.error("Fetch error:", err));
  };

  const handleAddToCart = (meal) => {
    const payload = {
      title: meal.dishes.map((i) => i.title).join(" + "),
      quantity: 1,
      total_calories: meal.total_calories,
      dishes: meal.dishes.map((d) => d.id),
    };

    fetch("http://127.0.0.1:8000/api/customer/cart/add-meal", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    })
      .then((res) => res.json())
      .then((response) => {
        console.log("Cart response:", response);
        dispatch(
          addToCart({
            id: Date.now(),
            title: payload.title,
            quantity: 1,
            price: payload.total_calories / 10,
            image: meal.dishes[0]?.image_url || "default.jpg",
          })
        );
      })
      .catch((err) => console.error("Add to cart error:", err));
  };

  return (
    <>
      <NavBar />
      <div className="meal-container">
        <h1>Generate Meals</h1>

        <div className="filters">
          <div
            className="input-group"
            style={{ display: "flex", alignItems: "center", gap: "2rem" }}
          >
            <input
              type="number"
              placeholder="Enter max calories"
              value={calories}
              onChange={(e) => setCalories(e.target.value)}
            />
            <button className="suggest-button" onClick={generateMeals}>
              Suggest
            </button>
          </div>

          <div
            className="category-toggle"
            onClick={() => setShowCategories((prev) => !prev)}
            style={{
              cursor: "pointer",
              marginTop: "1rem",
              display: "inline-flex",
              alignItems: "center",
              gap: "0.5rem",
            }}
            title="Toggle categories"
          >
            <span style={{ fontWeight: "bold" }}>Select Categories</span>
            <span
              className="toggle-arrow"
              style={{
                fontSize: "1.1rem",
                userSelect: "none",
                color: "#333",
                padding: "0.2rem 0.5rem",
                borderRadius: "4px",
                background: "#eee",
                border: "1px solid #ccc",
                cursor: "pointer",
                transition: "background 0.2s",
              }}
              onMouseOver={(e) => (e.target.style.background = "#ddd")}
              onMouseOut={(e) => (e.target.style.background = "#eee")}
            >
              {showCategories ? "▾" : "▸"}
            </span>
          </div>

          {showCategories && (
            <div
              className="checkboxes-inline"
              style={{
                display: "flex",
                flexWrap: "wrap",
                gap: "1rem",
                marginTop: "1rem",
                padding: "1rem 2rem",
                backgroundColor: "#f0f0f0",
                borderRadius: "6px",
              }}
            >
              {Object.keys(categoriesData).length > 0 ? (
                Object.keys(categoriesData).map((cat) => (
                  <label
                    key={cat}
                    className="category-option-inline"
                    style={{
                      display: "inline-flex",
                      alignItems: "center",
                      gap: "0.5rem",
                    }}
                  >
                    <input
                      type="checkbox"
                      value={cat}
                      checked={selectedCategories.includes(cat)}
                      onChange={() => handleCategoryChange(cat)}
                      style={{ cursor: "pointer" }}
                    />
                    <span style={{ cursor: "pointer" }}>{cat}</span>
                  </label>
                ))
              ) : (
                <p className="loading-msg">loading ...</p>
              )}
            </div>
          )}
        </div>

        <div className="meal-results">
          {meals.map((meal, idx) => (
            <div className="meal-card" key={idx}>
              <h3>Meal #{idx + 1}</h3>
              <ul>
                {/* {meal.dishes.map((item, i) => (
                  <li key={i}>
                    {item.title} - {item.calories} cal
                  </li>
                ))} */}
                {meal.dishes.map((item, i) => (
                  <li
                    key={i}
                    style={{
                      display: "flex",
                      alignItems: "center",
                      gap: "10px",
                      marginBottom: "5px",
                    }}
                  >
                    <img
                      src={item.image_url}
                      alt={item.title}
                      style={{
                        width: "50px",
                        height: "50px",
                        objectFit: "cover",
                        borderRadius: "8px",
                      }}
                    />
                    <span>
                      {item.title} - {item.calories} cal
                    </span>
                  </li>
                ))}
              </ul>
              <p>
                <strong>Total:</strong> {meal.total_calories} cal
              </p>
              <button
                className="add-to-cart"
                onClick={() => handleAddToCart(meal)}
              >
                Add to Cart
              </button>
            </div>
          ))}
        </div>
      </div>
    </>
  );
};

export default MealSuggestion;

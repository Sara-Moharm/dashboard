import React, { useEffect, useRef, useState } from "react";
import "../Style/Special.css";

const TopRatedDishes = () => {
  const [dishes, setDishes] = useState([]);
  const sliderRef = useRef(null);

  useEffect(() => {
    fetch("http://localhost:8000/api/toprated_dishes")
      .then((res) => res.json())
      .then((data) => {
        if (data.success && Array.isArray(data.topRatedDishes)) {
          setDishes(data.topRatedDishes);
        }
      })
      .catch((err) => console.error("Error fetching top-rated dishes:", err));
  }, []);

  // 👉 Drag to Scroll Logic
  useEffect(() => {
    const slider = sliderRef.current;
    let isDown = false;
    let startX;
    let scrollLeft;

    const handleMouseDown = (e) => {
      isDown = true;
      slider.classList.add("active");
      startX = e.pageX - slider.offsetLeft;
      scrollLeft = slider.scrollLeft;
    };

    const handleMouseLeave = () => {
      isDown = false;
      slider.classList.remove("active");
    };

    const handleMouseUp = () => {
      isDown = false;
      slider.classList.remove("active");
    };

    const handleMouseMove = (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - slider.offsetLeft;
      const walk = (x - startX) * 2; // scroll speed
      slider.scrollLeft = scrollLeft - walk;
    };

    slider.addEventListener("mousedown", handleMouseDown);
    slider.addEventListener("mouseleave", handleMouseLeave);
    slider.addEventListener("mouseup", handleMouseUp);
    slider.addEventListener("mousemove", handleMouseMove);

    // 💡 Clean up
    return () => {
      slider.removeEventListener("mousedown", handleMouseDown);
      slider.removeEventListener("mouseleave", handleMouseLeave);
      slider.removeEventListener("mouseup", handleMouseUp);
      slider.removeEventListener("mousemove", handleMouseMove);
    };
  }, []);

  return (
    <div className="top-rated-wrapper">
      <h2>Top Rated Dishes</h2>
      <div className="top-rated-slider" ref={sliderRef}>
        {dishes.map((dish, index) => (
          <div className="dish-card" key={index}>
            <img
              src={dish.image_url}
              alt={dish.name}
              style={{
                width: "100%",
                height: "200px",
                objectFit: "cover",
                borderRadius: "10px",
              }}
            />
            <h3 style={{ textAlign: "center" }}>{dish.title}</h3>
            <p style={{ textAlign: "center" }}>{dish.total_value} EGP</p>
            <p style={{ textAlign: "center" }}>{dish.calories} CAL</p>
          </div>
        ))}
      </div>
    </div>
  );
};

export default TopRatedDishes;

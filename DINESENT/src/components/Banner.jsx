import React from "react";
import "../Style/Banner.css";
import { Link } from "react-router-dom";

const Banner = () => {
  return (
    <>
      <div className="Banner">
        <div className="title">DINESENT</div>
        <div className="parg">
          Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim eveniet
          libero optio? A doloribus incidunt temporibus corporis hic cumque
          nobis odio libero earum iure!
        </div>
        <div>
          <Link to="/Menuu">
            <button className="btns">View Our Menu</button>
          </Link>
          <Link to="/customize">
            <button className="btns">Customize Your Order</button>
          </Link>
        </div>
      </div>
    </>
  );
};

export default Banner;

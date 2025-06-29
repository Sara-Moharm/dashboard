import React from "react";
import NavBar from "../components/NavBar";
import "../Style/Menuu.css";
import Menu from "../pages/Menu";

const Menuu = () => {
  return (
    <>
      <NavBar />

      <div className="menu-Banner">
        <div className="title">View Our Menu</div>
        <div className="parg">Lorem ipsum dolor sit amet, consectetur.</div>
      </div>
      <Menu />
    </>
  );
};

export default Menuu;

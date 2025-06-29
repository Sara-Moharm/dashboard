
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import '../Style/NavBar.css';
import { FaBars } from "react-icons/fa";
import { TbXboxXFilled } from "react-icons/tb";
import { useSelector } from 'react-redux';
import { IoMdCart, IoMdPerson } from 'react-icons/io';

const NavBar = () => {
    const user = useSelector((state) => state.user.currentUser);
    const [menuOpen, setMenuOpen] = useState(false);
    const [isMobile, setIsMobile] = useState(false); 
    const product = useSelector((state) => state.cart.items);

    useEffect(() => {
        const checkMobile = () => {
            setIsMobile(window.innerWidth <= 768); 
        };
        checkMobile();
        window.addEventListener("resize", checkMobile);
        return () => window.removeEventListener("resize", checkMobile);
    }, []);

    return (
        <nav className="NavBar">
            <div className="text-nav">DINESENT</div>

            <div className="menu-toggle" onClick={() => setMenuOpen(!menuOpen)}>
                {menuOpen ? (
                    <TbXboxXFilled className="menu-close" />
                ) : (
                    <FaBars />
                )}
            </div>

            <div className={`nav-items ${menuOpen ? 'show' : ''}`}>
                <div className="Links">
                    <Link to="/">Home</Link>
                    <Link to="/Menuu">Menu</Link>
                    <Link to="/about">About Us</Link>
                    <Link to="/contact">Contact Us</Link>
                    <Link to="/cart" className="cart-link">
                        <IoMdCart className="cart-icon" />
                        <span className="cart-count">
                            ({product.length > 0 ? product.length : 0})
                        </span>
                    </Link>
                </div>

                {user ? (
                    menuOpen && (
                        <div className="mobile-only">
                            <Link to="/Profile">
                                <button className="button">
                                    <IoMdPerson />
                                </button>
                            </Link>
                        </div>
                    )
                ) : (
                    menuOpen && (
                        <Link to="/register" className="btn-link mobile-only">
                            <div className="btn">Registration</div>
                        </Link>
                    )
                )}
            </div>

            {user ? (
                <div className="desktop-only">
                    <Link to="/Profile">
                        <button className="button">
                            <IoMdPerson />
                        </button>
                    </Link>
                </div>
            ) : (
                isMobile ? (
                    null
                ) : (
                    <Link to="/register" className="btn-link desktop-only">
                        <div className="btn">Registration</div>
                    </Link>
                )
            )}
        </nav>
    );
};

export default NavBar;

import React from 'react';
import NavBar from '../components/NavBar';
import "../Style/Profile.css";
import { useSelector } from 'react-redux';
import { VscFeedback } from 'react-icons/vsc';
import { FaShippingFast, FaUserEdit } from 'react-icons/fa';
import { FaCartShopping } from 'react-icons/fa6';
import { Link } from 'react-router-dom';
import { FaAddressCard } from "react-icons/fa6";


const Profile = () => {
    const user = useSelector((state) => state.user.currentUser);

    return (
        <>
            <NavBar />
            <div className='parent'>
                <div className='info'>
                    <div>
                        <img src="WhatsApp Image 2025-05-06 at 18.25.43_5e5d72c7.jpg" alt="profile" width={80} height={80} />
                        <p>Profile Photo</p>
                    </div>
                    <div className="email">
                        <p>{user ? user.email : "Email not available"}</p>
                    </div>
                </div>

                <div className='Profile-icons'>
                    <div>
                        <FaUserEdit />
                        <h6 className='h6' >Edit Profile</h6>
                    </div>
                    <div><FaCartShopping />
                        <Link to="/cart" style={{ textDecoration: "none" }}>
                            <h6 className='h6' >Cart</h6>
                        </Link>

                    </div>

                    <div> <FaShippingFast />
                        <Link to="/track" style={{ textDecoration: "none" }}>
                            <h6 className='h6'>Track Order</h6>

                        </Link>
                    </div>

                    <div> <VscFeedback />
                        <Link to="/contact" style={{ textDecoration: "none" }}>
                            <h6 className='h6'>Give Feedback</h6>

                        </Link>
                    </div>
                    <div> <FaAddressCard />
                        <Link to="/addresses" style={{ textDecoration: "none" }}>
                            <h6 className='h6'>addresses</h6>

                        </Link>
                    </div>


                </div>

            </div>

        </>
    );
}

export default Profile;

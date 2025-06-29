import React from 'react'
import NavBar from '../components/NavBar'
import "../Style/Regestration.css"
import { Link } from 'react-router-dom'
import { FaFacebook } from "react-icons/fa";
import { FaGoogle } from "react-icons/fa";
import { FaXTwitter } from "react-icons/fa6";

const Regestration = () => {
    return (
        <>
            <NavBar />
            <div className='Rges-Banner'>
                <div>
                    <p>Already have an Account ?</p>
                    <Link to="/Login">
                        <button className='btn-log'>LOGIN</button>
                    </Link >
                    <p>create an Account </p>
                    <Link to="/SingIN">
                        <button className='btn-sign'>SIGN UP</button>
                    </Link>
                    <p>Continue With </p>
                    <div className='icons'>
                        <Link to="https://www.facebook.com/?locale=ar_AR">
                            <FaFacebook className='icon' />
                        </Link>
                        <Link to="https://accounts.google.com/v3/signin/identifier?authuser=0&continue=https%3A%2F%2Fwww.google.co.uk%2F%3Fpli%3D1&ec=GAlAmgQ&hl=ar&flowName=GlifWebSignIn&flowEntry=AddSession&dsh=S101992573%3A1746446031576891">
                            <FaGoogle className='icon' />
                        </Link>
                        <Link to="https://x.com/i/flow/login?input_flow_data=%7B%22requested_variant%22%3A%22eyJsYW5nIjoiZW4ifQ%3D%3D%22%7D">
                            <FaXTwitter className='icon' />
                        </Link>


                    </div>

                </div>
            </div>

        </>
    )
}

export default Regestration

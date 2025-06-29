import React from 'react'
import NavBar from '../components/NavBar'
import "../Style/Follow.css"
import { Link } from 'react-router-dom'

const Follow = () => {
    return (
        <div>
            <NavBar />

            <div class="follow-container">
                <h2>Info about your order</h2>
                <p>Order #123RGR231567Y</p>

                <div class="progress-track">
                    <div class="step completed">
                        <div class="circle">✔</div>
                        <p>placed</p>
                    </div>
                    <div class="step completed">
                        <div class="circle">✔</div>
                        <p>preparing</p>
                    </div>
                    <div class="step">
                        <div class="circle"></div>
                        <p>shipped</p>
                    </div>
                    <div class="step">
                        <div class="circle"></div>
                        <p>delivered</p>
                    </div>
                </div>
                <Link to="/" style={{textDecoration:"none"}}>  
                        <button class="back-home">back to home</button>
                </Link>
            </div>



        </div>
    )
}

export default Follow

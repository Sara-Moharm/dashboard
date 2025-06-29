import React from 'react'
import NavBar from '../components/NavBar'
import "../Style/AboutUs.css"

const AboutUs = () => {
    return (
        <>
            <NavBar />
            <div className='About-Banner'>
                <div className='title'>Who We Are</div>
                <div className='parg'>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim eveniet libero optio? A doloribus incidunt temporibus corporis hic cumque nobis odio libero earum iure! Quibusdam odio veniam reiciendis voluptate ea.</div>
                <div >
                </div>
            </div>

            <div class="story-wrapper">
                <div class="story">
                    <div>
                        <h2 className='text'>our story</h2>
                        <p> Lorem ipsum dolor sit, amet consectetur adipisicing elit. Veniam dolor odio labore mollitia explicabo corrupti, eos temporibus harum doloribus ratione commodi soluta fugiat repudiandae omnis hic laboriosam, voluptatibus aliquam blanditiis.</p>
                    </div>
                    <img src="WhatsApp Image 2025-05-04 at 19.16.40_7539ca46.jpg" width={400} height={400} alt="..." />
                </div>
            </div>
        </>
    )
}

export default AboutUs

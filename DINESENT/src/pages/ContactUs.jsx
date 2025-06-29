import React from 'react'
import NavBar from '../components/NavBar'
import "../Style/ContactUs.css"
import { useFormik } from 'formik';
import * as Yup from 'yup';

const ContactUs = () => {

    const formik = useFormik({
        initialValues: {
            name: '',
            email: '',
            message: ''
        },
        validationSchema: Yup.object({
            name: Yup.string().required('Name is required'),
            email: Yup.string().email('Invalid email').required('Email is required'),
            message: Yup.string().required('Message is required')
        }),
        onSubmit: (values, { resetForm }) => {
            console.log(values);
            alert('Form submitted!');
            resetForm();
        }
    });

    return (
        <>
            <NavBar />
            <div className='Contact-Banner'>
                <div className='title'>Get in Touch</div>
                <div className='parg'>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim eveniet libero optio? A doloribus incidunt temporibus corporis hic cumque nobis odio libero earum iure! Quibusdam odio veniam reiciendis voluptate ea.</div>
                <div >
                </div>
            </div>

            <div className='container'>
                <div className='form-container'>

                    <form onSubmit={formik.handleSubmit} >
                        <h2>Contact Us</h2>

                        <label htmlFor="name">Name:</label>
                        <input
                            id="name"
                            type="text"
                            {...formik.getFieldProps('name')}
                        />
                        {formik.touched.name && formik.errors.name && (
                            <div className="error">{formik.errors.name}</div>
                        )}

                        <label htmlFor="email">Email:</label>
                        <input
                            id="email"
                            type="email"
                            {...formik.getFieldProps('email')}
                        />
                        {formik.touched.email && formik.errors.email && (
                            <div className="error">{formik.errors.email}</div>
                        )}

                        <label htmlFor="message">Message:</label>
                        <textarea
                            id="message"
                            rows="5"
                            {...formik.getFieldProps('message')}
                        />
                        {formik.touched.message && formik.errors.message && (
                            <div className="error">{formik.errors.message}</div>
                        )}

                        <div className="btn-wrapper">
                            <button type="submit">Send</button>
                        </div>
                    </form>
                </div>

                <div>
                    <img src="WhatsApp Image 2025-05-04 at 19.16.40_7539ca46.jpg" width={300} height={400} alt="" />
                </div>
            </div>



        </>
    )
}

export default ContactUs

import React, { useEffect } from 'react'
import NavBar from '../components/NavBar'
import { useDispatch, useSelector } from 'react-redux';
import { Link, useNavigate } from 'react-router-dom';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import "../Style/CheackOut.css"
import { login } from '../store/userSlice';
import { useLocation } from 'react-router-dom';
const CheckOut = () => {
    const countries = ["Egypt", "Saudi Arabia", "UAE", "Jordan", "Morocco", "Algeria"];
    const cities = [
        "Cairo",
        "Alexandria",
        "Riyadh",
        "Jeddah",
        "Dubai",
        "Abu Dhabi",
        "Amman",
        "Beirut",
        "Baghdad",
        "Kuwait City",
        "Doha",
        "Manama",
        "Tunis",
        "Casablanca",
        "Algiers",
        "Khartoum",
        "Muscat",
        "Istanbul",
        "Tehran",
        "Damascus"
    ];

    const location = useLocation();
    const navigate = useNavigate();
    const selectedProducts = location.state?.selectedProducts || [];
    const dispatch = useDispatch();
  const currentUser = useSelector((state) => state.user.currentUser);


    useEffect(() => {
        if (!currentUser) {
            navigate("/login", { replace: true, state: { from: location.pathname } });
        }
    }, [currentUser, navigate, location]);

    const formik = useFormik({
        initialValues: {
            name: '',
            email: '',
            address: '',
            phone: '',
            country: '',
            city: ''

        },
        validationSchema: Yup.object({
            name: Yup.string().required('Name is required'),
            email: Yup.string().email('Invalid email').required('Email is required'),
            address: Yup.string().required('address is required'),
            phone: Yup.string().min(11, "phone number not complete").required("phone number is required"),
            country: Yup.string().required('Country is required'),
            city: Yup.string().required('City is required')
        }),

        onSubmit: (values, { resetForm }) => {
            console.log(values);
            dispatch(login({ name: values.name, email: values.email }));
            alert('Form submitted!');
            resetForm();
            navigate('/Payment', { state: { selectedProducts, userData: values } });
        }
    });
    return (
        <>
            <NavBar />
            <h2>Complete your shipping info</h2>
            <div>
                <form onSubmit={formik.handleSubmit} className='Check-form' >

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
                    <label htmlFor="address">address:</label>
                    <input
                        id="address"
                        type="text"
                        {...formik.getFieldProps('address')}
                    />
                    {formik.touched.address && formik.errors.address && (
                        <div className="error">{formik.errors.address}</div>
                    )}

                    <label htmlFor="phone">phone:</label>
                    <input
                        id="phone"
                        type="tel"
                        {...formik.getFieldProps('phone')}
                    />
                    {formik.touched.phone && formik.errors.phone && (
                        <div className="error">{formik.errors.phone}</div>
                    )}
                    <div style={{ display: "flex", justifyContent: "space-around", marginBottom: "30px" }}>
                        <select
                            className='btn'
                            id="country"
                            name="country"
                            {...formik.getFieldProps('country')}
                        >
                            <option value="">-- Select --</option>
                            {countries.map((country, index) => (
                                <option key={index} value={country}>{country}</option>
                            ))}
                        </select>

                        <select className='btn' id="city" name="city" {...formik.getFieldProps('city')}>
                            <option value="">-- Select City --</option>
                            {cities.map((city, index) => (
                                <option key={index} value={city}>{city}</option>
                            ))}
                        </select>
                    </div>

                    <div className="btn-wrapper">
                        <button type="submit">SignUp</button>

                    </div>
                </form>
            </div>
        </>
    )
}

export default CheckOut

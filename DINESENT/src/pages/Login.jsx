import React from 'react';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import "../Style/Login.css";
import { Link, useNavigate } from 'react-router-dom';
import { useDispatch } from 'react-redux';
import { login } from '../store/userSlice';

const Login = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();

    const formik = useFormik({
        initialValues: {
            email: '',
            password: '',
        },
        validationSchema: Yup.object({
            email: Yup.string().email('Invalid email').required('Email is required'),
            password: Yup.string().required('Password is required'),
        }),
        onSubmit: (values, { resetForm }) => {
            console.log(values);
            dispatch(login({ email: values.email }));
            alert('Form submitted!');
            resetForm();
            navigate('/');
        }
    });

    return (
        <div>
            <form onSubmit={formik.handleSubmit} className='login-form'>
                <h2>Log In</h2>

                <label htmlFor="email">Email:</label>
                <input
                    id="email"
                    type="email"
                    {...formik.getFieldProps('email')}
                />
                {formik.touched.email && formik.errors.email && (
                    <div className="error">{formik.errors.email}</div>
                )}

                <label htmlFor="password">Password:</label>
                <input
                    id="password"
                    type="password"
                    {...formik.getFieldProps('password')}
                />
                {formik.touched.password && formik.errors.password && (
                    <div className="error">{formik.errors.password}</div>
                )}

                <div style={{ marginBottom: 15 }}>
                    <Link to="/forgot-password" style={{ color: "black" }}>
                        Forgot Password?
                    </Link>
                </div>

                <div className="btn-wrapper">
                    <button type="submit">LogIn</button>
                </div>
            </form>
        </div>
    );
};

export default Login;


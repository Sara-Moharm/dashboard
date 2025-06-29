import React, { useState } from 'react';
import '../Style/payment.css'; 
import NavBar from '../components/NavBar';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import { useLocation, useNavigate } from 'react-router-dom';
import { setOrderProducts } from '../store/orderSlice';  // استيراد الـ action من الـ Redux
import { useDispatch } from 'react-redux';



const PaymentPage = () => {
  const [paymentMethod, setPaymentMethod] = useState('cod');
  const navigate = useNavigate();
  const location = useLocation();
  const selectedProducts = location.state?.selectedProducts || [];
  

  const dispatch = useDispatch();


  const formik = useFormik({
    initialValues: {
      cardNumber: '',
      month: '',
      year: '',
      ccv: '',
      agree: false,
    },
    validationSchema: Yup.object({
      ...(paymentMethod === 'credit' && {
        cardNumber: Yup.string()
          .required('Card number is required')
          .matches(/^[0-9]{16}$/, 'Must be 16 digits'),
        month: Yup.string()
          .required('Month is required')
          .matches(/^(0[1-9]|1[0-2])$/, 'Invalid month'),
        year: Yup.string()
          .required('Year is required')
          .matches(/^[0-9]{4}$/, 'Enter a valid year'),
        ccv: Yup.string()
          .required('CCV is required')
          .matches(/^[0-9]{3}$/, 'Must be 3 digits'),
      }),
      agree: Yup.boolean().oneOf([true], 'You must agree to continue'),
    }),
    onSubmit: (values) => {
      dispatch(setOrderProducts(selectedProducts));  // تحديث الـ Redux state
      alert('Order placed!');
      navigate('/track');  // الانتقال إلى صفحة التتبع بعد إرسال الطلب
  },
  });

  return (
    <>
    <NavBar />
    <div className="payment-container">
      <h2>Choose your payment method</h2>

      <form onSubmit={formik.handleSubmit}>
        <div className="payment-options">
          <label>
            <input
              type="radio"
              name="payment"
              value="credit"
              checked={paymentMethod === 'credit'}
              onChange={() => setPaymentMethod('credit')}
            />
            Credit Card
          </label>
          <label>
            <input
              type="radio"
              name="payment"
              value="cod"
              checked={paymentMethod === 'cod'}
              onChange={() => setPaymentMethod('cod')}
            />
            Cash on Delivery
          </label>
        </div>

        {paymentMethod === 'credit' && (
          <div className="credit-form">
            <input
              type="text"
              name="cardNumber"
              placeholder="Card Number"
              onChange={formik.handleChange}
              onBlur={formik.handleBlur}
              value={formik.values.cardNumber}
            />
            {formik.touched.cardNumber && formik.errors.cardNumber && (
              <div className="error">{formik.errors.cardNumber}</div>
            )}

            <div className="expiry-ccv">
              <input
                type="text"
                name="month"
                placeholder="Month"
                onChange={formik.handleChange}
                onBlur={formik.handleBlur}
                value={formik.values.month}
              />
              <input
                type="text"
                name="year"
                placeholder="Year"
                onChange={formik.handleChange}
                onBlur={formik.handleBlur}
                value={formik.values.year}
              />
              <input
                type="text"
                name="ccv"
                placeholder="CCV"
                onChange={formik.handleChange}
                onBlur={formik.handleBlur}
                value={formik.values.ccv}
              />
            </div>

            <div className="error-container">
              {formik.touched.month && formik.errors.month && (
                <div className="error">{formik.errors.month}</div>
              )}
              {formik.touched.year && formik.errors.year && (
                <div className="error">{formik.errors.year}</div>
              )}
              {formik.touched.ccv && formik.errors.ccv && (
                <div className="error">{formik.errors.ccv}</div>
              )}
            </div>
          </div>
        )}

        <div className="terms">
          <input
            type="checkbox"
            name="agree"
            onChange={formik.handleChange}
            checked={formik.values.agree}
          />
          <label htmlFor="agree">
            By clicking the button you agree with the terms & conditions and privacy policy
          </label>
          {formik.touched.agree && formik.errors.agree && (
            <div className="error">{formik.errors.agree}</div>
          )}
        </div>

        <button type="submit" className="place-order">Place Order</button>
      </form>
    </div>
    </>
  );
  
};



export default PaymentPage;

import { configureStore } from '@reduxjs/toolkit';
import cartReducer from "../store/cartSlice"
import userReducer from '../store/userSlice';
import orderReducer from './orderSlice'; // أضف هذا

export const store = configureStore({
    reducer: {
        cart: cartReducer,
        user: userReducer,
        order: orderReducer
    },
});
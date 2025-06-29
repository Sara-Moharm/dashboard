import { createSlice } from '@reduxjs/toolkit';
const initialState = {
    items: [],
    value: 0
};
const cartSlice = createSlice({
    name: 'cart',
    initialState,
    reducers: {
        addToCart: (state, action) => {
            const item = state.items.find((item) => item.id === action.payload.id);
            if (item) {
                item.quantity += 1;
            } else {
                state.items.push({ ...action.payload, quantity: 1 });
            }
        },
        removeFromCart: (state, action) => {
            state.items = state.items.filter(item => item.id !== action.payload);
        },
        increment: (state, action) => {
            const item = state.items.find(item => item.id === action.payload);
            if (item) {
                item.quantity += 1;
            }
        },
        decrement: (state, action) => {
            const item = state.items.find(item => item.id === action.payload);
            if (item && item.quantity > 1) {
                item.quantity -= 1;
            }
        },
        Clear: (state) => {
            state.items = []
        },
    },
});
export const { addToCart, removeFromCart, increment, decrement, Clear } = cartSlice.actions;
export default cartSlice.reducer;

export const selectTotalPrice = (state) => {
    return state.cart.items.reduce((total, item) => total + item.price * item.quantity, 0);
}

import { createSlice } from "@reduxjs/toolkit";

const initialState = {
    selectedProducts: [],
};

const orderSlice = createSlice({
    name: "order",
    initialState,
    reducers: {
        setOrderProducts: (state, action) => {
            state.selectedProducts = action.payload;
        },
        clearOrder: (state) => {
            state.selectedProducts = [];
        }
    }
});

export const { setOrderProducts, clearOrder } = orderSlice.actions;
export default orderSlice.reducer;

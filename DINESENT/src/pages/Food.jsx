import React, { useEffect, useState } from 'react'
import "../Style/Desert.css"
import NavBar from '../components/NavBar';
import { useDispatch } from 'react-redux';
import { addToCart } from "../store/cartSlice";
const Food = () => {
    const [foodItems, setFoodItems] = useState([{}]);
    const dispatch = useDispatch();
    useEffect(() => {

        fetch('http://localhost:8000/api/categories/2')
            .then(res => res.json())
            .then(data => {
                setFoodItems(data.category_dishes)
                console.log(data);
            })
    }, [])
    return (
        <>
            <NavBar />
            <div className="cards-container">
                {foodItems.map((item) => (
                    <div className="card" key={item.id}>
                        <img src={item.image_url
                        } alt={item.title} className="product-imagee" />
                        <h3 className="product-title">{item.title}</h3>
                        <p className="product-description">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        </p>
                        <div className="product-footer">
                            <span className="product-price">${item.price}</span>
                            <button
                                className="add-to-cart"
                                onClick={() => dispatch(addToCart(item))}
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>
                ))}
            </div>

        </>

    )
}

export default Food

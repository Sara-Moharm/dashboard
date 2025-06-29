import React, { useEffect, useState } from 'react'
import "../Style/Desert.css"
import NavBar from '../components/NavBar';
import { useDispatch } from 'react-redux';
import { addToCart } from "../store/cartSlice";
const Drink = () => {


    const [dessertItems, setDessertItems] = useState([{}]);
    const dispatch = useDispatch();



    useEffect(() => {
        fetch('http://localhost:8000/api/category_dishes')
            .then(res => res.json())
            .then(data => {
                setDessertItems(data.category_dishes); // حسب شكل الريسبونس
                console.log(data);
            })
            .catch(error => console.error("Fetch error:", error));
    }, []);

    return (
        <div>
            <NavBar />

            <div className="cards-container">
                {dessertItems.map((item) => (
                    <div className="card" key={item.id}>
                        <img src={item.image_url
                        } alt={item.title} className="product-imagee" />
                        <h3 className="product-title">{item.title}</h3>
                        <p className="product-description">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        </p>
                        {/* <div className="product-rating">Rating: {item.rating.rate}</div> */}
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

        </div>
    )
}

export default Drink

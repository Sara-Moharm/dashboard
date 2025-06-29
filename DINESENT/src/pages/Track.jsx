import { useSelector } from 'react-redux';
import "../Style/Truck.css";
import NavBar from '../components/NavBar';
import { Link } from 'react-router-dom';

const Track = () => {
    const selectedProducts = useSelector((state) => state.order.selectedProducts);

    return (
        <>
            <NavBar />
            <div className="track-container">
                <div className="track-banner">
                    <h1>🎉 شكراً لاختيارك لنا!</h1>
                    <p>تم تقديم طلبك بنجاح وسنقوم بمعالجته في أسرع وقت.</p>
                </div>

                <div className="product-list">
                    <h2>🛒 المنتجات التي تم طلبها</h2>
                    {selectedProducts.length === 0 ? (
                        <p className="empty-msg">لم يتم اختيار أي منتجات</p>
                    ) : (
                        selectedProducts.map(product => (
                            <div className="product-card" key={product.id}>
                                <img src={product.image} alt={product.title} />
                                <div className="product-info">
                                    <p className="product-title">{product.title}</p>
                                    <p>العدد: {product.quantity}</p>
                                    <p>المجموع: ${(product.quantity * product.price).toFixed(2)}</p>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
            <Link to="/follow" style={{textDecoration:"none"}}>
                <button style={{
                    display: "flex",
                    justifyContent: "center",
                    alignItems: "center",
                    color: "black",
                    background: "gray",
                    borderRadius: "50px",
                    margin: "20px auto", 
                    padding: "10px 20px",
                    border: "none",
                    cursor: "pointer"
                }}>
                    track your order
                </button>
            </Link>

        </>
    );
};

export default Track;

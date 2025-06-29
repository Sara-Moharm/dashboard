import React, { useEffect, useState } from 'react';
import '../Style/Addresses.css';
import AddressForm from './AddressForm'; // 👈 هننشئ الملف ده

const Addresses = () => {
    const [addresses, setAddresses] = useState([]);
    const [showForm, setShowForm] = useState(false); // 👈 تحكم في المودال
    const [editIndex, setEditIndex] = useState(null);
    const [editInitialValues, setEditInitialValues] = useState(null);


useEffect(() => {
    const stored = localStorage.getItem('userAddress');
    if (stored) {
        try {
            const parsed = JSON.parse(stored);
            setAddresses(Array.isArray(parsed) ? parsed : [parsed]);
        } catch (e) {
            console.error("Failed to parse address:", e);
            setAddresses([]);
        }
    }
}, []);


    const handleDelete = (index) => {
        const newList = addresses.filter((_, i) => i !== index);
        setAddresses(newList);
        localStorage.setItem('userAddress', JSON.stringify(newList));
    };

    const handleEdit = (address, index) => {
        setEditIndex(index);
        setEditInitialValues(address);
        setShowForm(true);
    };

    const handleAddAddress = (newAddress) => {
            console.log("New Address to add:", newAddress); // ✅ هل بتوصل؟

        const updated = [...addresses, newAddress];
        setAddresses(updated);
        localStorage.setItem('userAddress', JSON.stringify(updated)); // ✅ حفظ كمصفوفة دايمًا
        setShowForm(false);
    };


    const handleSave = (data) => {
        if (editIndex !== null) {
            const updated = [...addresses];
            updated[editIndex] = data;
            setAddresses(updated);
            localStorage.setItem('userAddress', JSON.stringify(updated));
        } else {
            const updated = [...addresses, data];
            setAddresses(updated);
            localStorage.setItem('userAddress', JSON.stringify(updated));
        }

        // reset الوضع
        setShowForm(false);
        setEditIndex(null);
        setEditInitialValues(null);
    };


    return (
        <div className="account-container">
            <div className="sidebar">
                <ul>
                    <li className="active">Saved Addresses</li>
                    <li>Account Info</li>
                    <li>My Orders</li>
                    <li>Saved Cards</li>
                    <li>talabat Pay</li>
                </ul>
            </div>

            <div className="content">
                <div className="header">
                    <h2>My Account</h2>
                    <button style={{ color: "black" }} className="add-btn" onClick={() => setShowForm(true)}>+ ADD ADDRESS</button>
                </div>

                {addresses.map((item, i) => (
                    <div className="address-box" key={i}>
                        <p><strong>Address:</strong> {item.address.city}</p>
                        <p><strong>District:</strong> {item.address.district}</p>
                        <p><strong>Street Address:</strong> {item.address.street_address|| 'None'}</p>

                        <div className="action-btns">
                            <button style={{ color: "black" }} onClick={() => handleDelete(i)}>Delete</button>
                            <button style={{ color: "black" }} onClick={() => handleEdit(item, i)}>Edit</button>
                        </div>
                    </div>
                ))}

                {/* مودال الفورم */}
                {showForm && (
                    <AddressForm
                        onClose={() => {
                            setShowForm(false);
                            setEditIndex(null);
                            setEditInitialValues(null);
                        }}
                        onSave={handleSave}
                        initialValues={editInitialValues}
                    />
                )}

            </div>
        </div>
    );
};

export default Addresses;


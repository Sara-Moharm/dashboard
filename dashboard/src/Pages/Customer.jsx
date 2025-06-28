import React, { useEffect, useState } from 'react'
import SideBar from '../Component/SideBar';

const Customer = () => {

    const [data, setData] = useState([]);
    const [searchId, setSearchId] = useState('');
    const [searchName, setSearchName] = useState('');

    useEffect(() => {
        fetch('https://jsonplaceholder.typicode.com/posts')
            .then(response => response.json())
            .then(data => {
                setData(data);
            });
    }, []);

    const filteredData = data.filter(item =>
        (searchId === '' || item.id.toString().includes(searchId)) &&
        (searchName === '' || item.title.toLowerCase().includes(searchName.toLowerCase()))
    );

    return (
        <>
            <div className="min-h-screen flex bg-[#f4f6fc] text-sm font-sans overflow-hidden">
                {/* Sidebar */}
 
                <SideBar />

                {/* Main Content */}
                <main className="flex-1 flex flex-col p-6 space-y-6 overflow-x-auto">
                    {/* Topbar */}
                    <div className="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm">
                        <input
                            type="text"
                            placeholder="Search here"
                            className="w-1/2 border px-4 py-2 rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-green-300"
                        />
                        <div className="flex items-center gap-4">
                            <button className="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-sm">
                                Filter Period
                            </button>
                            <span className="text-gray-700">Hello, <strong>Samantha</strong></span>
                            <img src="https://i.pravatar.cc/150?img=3" className="w-9 h-9 rounded-full border" alt="Profile" />
                        </div>
                    </div>

                    {/* Heading */}
                    <div>
                        <h1 className="text-2xl font-semibold text-gray-800">Your Orders</h1>
                        <p className="text-gray-500 mt-1">This is your order list data</p>
                    </div>

                    {/* Search Inputs */}
                    <div className="flex gap-4 mb-4">
                        <input
                            type="text"
                            value={searchId}
                            onChange={(e) => setSearchId(e.target.value)}
                            placeholder="Search by ID"
                            className="px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm text-gray-800"
                        />
                        <input
                            type="text"
                            value={searchName}
                            onChange={(e) => setSearchName(e.target.value)}
                            placeholder="Search by Title"
                            className="px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 text-sm text-gray-800"
                        />
                    </div>

                    {/* Table */}
                    <div className="bg-white rounded-lg shadow overflow-x-auto">
                        <table className="min-w-full text-left border-separate border-spacing-y-3">
                            <thead className="bg-green-600 text-white text-sm">
                                <tr>
                                    <th className="p-3 rounded-l-lg">Order ID</th>
                                    <th className="p-3">Date</th>
                                    <th className="p-3">Customer Name</th>
                                    <th className="p-3">Location</th>
                                    <th className="p-3">Amount</th>
                                    <th className="p-3">Status Order</th>
                                    <th className="p-3 rounded-r-lg">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredData.map((item, index) => (
                                    <tr key={index} className="bg-white text-gray-700 shadow-sm rounded-lg">
                                        <td className="p-3">{item.id}</td>
                                        <td className="p-3">26 March 2020, 12:4 AM</td>
                                        <td className="p-3">{item.title}</td>
                                        <td className="p-3">London Street</td>
                                        <td className="p-3">$164.5</td>
                                        <td className="p-3">
                                            <span className="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs">On Delivery</span>
                                        </td>
                                        <td className="p-3">
                                            <button className="text-gray-400 hover:text-gray-600 text-xl">⋮</button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </main>
            </div>

        </>
    )
}

export default Customer

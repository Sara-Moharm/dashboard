import React from 'react'
import SideBar from '../Component/SideBar'

const Reviwes = () => {
    return (
        <>
            <div className="min-h-screen flex bg-[#f4f6fc] text-sm font-sans overflow-hidden">

                <SideBar />
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


                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {/* Card 1 */}
                        <div className="bg-white p-6 rounded-2xl shadow-md space-y-6">
                            <div className="flex items-center gap-4">
                                <img
                                    src="WhatsApp Image 2025-05-03 at 20.25.19_1cd594fc.jpg"
                                    alt="avatar"
                                    className="w-50 h-50 rounded-full object-cover border-2 border-green-500"
                                />
                                <span className="text-lg font-semibold text-gray-800">Grilled chicken on charcoal</span>
                            </div>
                            <div>
                                <p className="text-gray-600 leading-relaxed">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia natus, sequi asperiores...
                                </p>
                            </div>
                            <div className="flex items-center gap-4 bg-[#5E6C93] p-4 rounded-lg">
                                <img
                                    src="https://i.pravatar.cc/150?img=3"
                                    alt="avatar"
                                    className="w-10 h-10 rounded-full object-cover"
                                />
                                <div className="flex flex-col">
                                    <p className="">hsanlcakls</p>
                                    <span className="">
                                        Rating: <strong className="">5</strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white p-6 rounded-2xl shadow-md space-y-6">
                            <div className="flex items-center gap-4">
                                <img
                                    src="WhatsApp Image 2025-05-03 at 20.25.19_1cd594fc.jpg"
                                    alt="avatar"
                                    className="w-50 h-50 rounded-full object-cover border-2 border-green-500"
                                />
                                <span className="text-lg font-semibold text-gray-800">Grilled chicken on charcoal</span>
                            </div>
                            <div>
                                <p className="text-gray-600 leading-relaxed">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia natus, sequi asperiores...
                                </p>
                            </div>
                            <div className="flex items-center gap-4 bg-[#5E6C93] p-4 rounded-lg">
                                <img
                                    src="https://i.pravatar.cc/150?img=3"
                                    alt="avatar"
                                    className="w-10 h-10 rounded-full object-cover"
                                />
                                <div className="flex flex-col">
                                    <p className="">hsanlcakls</p>
                                    <span className="">
                                        Rating: <strong className="">5</strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white p-6 rounded-2xl shadow-md space-y-6">
                            <div className="flex items-center gap-4">
                                <img
                                    src="WhatsApp Image 2025-05-03 at 20.25.19_1cd594fc.jpg"
                                    alt="avatar"
                                    className="w-50 h-50 rounded-full object-cover border-2 border-green-500"
                                />
                                <span className="text-lg font-semibold text-gray-800">Grilled chicken on charcoal</span>
                            </div>
                            <div>
                                <p className="text-gray-600 leading-relaxed">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia natus, sequi asperiores...
                                </p>
                            </div>
                            <div className="flex items-center gap-4 bg-[#5E6C93] p-4 rounded-lg">
                                <img
                                    src="https://i.pravatar.cc/150?img=3"
                                    alt="avatar"
                                    className="w-10 h-10 rounded-full object-cover"
                                />
                                <div className="flex flex-col">
                                    <p className="">hsanlcakls</p>
                                    <span className="">
                                        Rating: <strong className="">5</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white p-6 rounded-2xl shadow-md space-y-6">
                            <div className="flex items-center gap-4">
                                <img
                                    src="WhatsApp Image 2025-05-03 at 20.25.19_1cd594fc.jpg"
                                    alt="avatar"
                                    className="w-50 h-50 rounded-full object-cover border-2 border-green-500"
                                />
                                <span className="text-lg font-semibold text-gray-800">Grilled chicken on charcoal</span>
                            </div>
                            <div>
                                <p className="text-gray-600 leading-relaxed">
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia natus, sequi asperiores...
                                </p>
                            </div>
                            <div className="flex items-center gap-4 bg-[#5E6C93] p-4 rounded-lg">
                                <img
                                    src="https://i.pravatar.cc/150?img=3"
                                    alt="avatar"
                                    className="w-10 h-10 rounded-full object-cover"
                                />
                                <div className="flex flex-col">
                                    <p className="">hsanlcakls</p>
                                    <span className="">
                                        Rating: <strong className="">5</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>


                </main>
            </div>
        </>
    )
}

export default Reviwes

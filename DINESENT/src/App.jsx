import react from 'react'
import './App.css'
import Home from './pages/Home'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import Menuu from './pages/Menuu'
import AboutUs from './pages/AboutUs'
import ContactUs from './pages/ContactUs'
import Regestration from './pages/Regestration'
import Login from './pages/Login'
import SignUp from './pages/SignUp'
import Desert from './pages/Desert'
import Drink from './pages/Drink'
import Cart from './pages/Cart'
import Food from './pages/Food'
import Profile from './pages/Profile'
import CheckOut from './pages/CheckOut'
import Payment from './pages/Payment'
import Track from './pages/Track'
import Follow from './pages/Follow'
import ForgetPassword from './pages/forgot-password'
import Addresses from './pages/Addresses'
import Customize from './pages/Customize'

function App() {
  return (
    <>
        <BrowserRouter>
          <Routes>
            <Route path='/' element={<Home />}/>
            <Route path='/Menuu' element={<Menuu />}/>
            <Route path='/about' element={<AboutUs />}/>
            <Route path='/contact' element={<ContactUs />}/>
            <Route path='/register' element={<Regestration />}/>
            <Route path='/Login' element={<Login />}/>
            <Route path='/SingIN' element={<SignUp />}/>
            <Route path='/desert' element={<Desert />}/>
            <Route path='/cart' element={<Cart />}/>
            <Route path='/drink' element={<Drink />}/>
            <Route path='/food' element={<Food />}/>
            <Route path='/Profile' element={<Profile />}/>
            <Route path='/checkOut' element={<CheckOut />}/>
            <Route path='/Payment' element={<Payment />}/>
            <Route path='/track' element={<Track />}/>
            <Route path='/follow' element={<Follow />}/>
            <Route path='/forgot-password' element={<ForgetPassword />}/>
            <Route path='/addresses' element={<Addresses />}/>
            <Route path='/customize' element={<Customize />}/>
          </Routes>
        </BrowserRouter>
    </>
  )
}

export default App

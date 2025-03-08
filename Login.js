import { useState } from "react";

import axios from "axios";

import { useNavigate } from "react-router-dom";
 
const Login = ({ setUser }) => {

    const [email, setEmail] = useState("");

    const [password, setPassword] = useState("");

    const navigate = useNavigate();
 
    const handleLogin = async (e) => {

        e.preventDefault();

        try {

            const res = await axios.post("http://localhost:5000/login", { email, password });

            setUser(res.data.user);

            alert("Login Successful");

            navigate("/dashboard");

        } catch (err) {

            alert(err.response?.data?.error || "Login failed");

        }

    };
 
    return (
<div>
<h2>Login</h2>
<form onSubmit={handleLogin}>
<input type="email" placeholder="Email" value={email} onChange={(e) => setEmail(e.target.value)} required />
<input type="password" placeholder="Password" value={password} onChange={(e) => setPassword(e.target.value)} required />
<button type="submit">Login</button>
</form>
</div>

    );

};
 
export default Login;

 
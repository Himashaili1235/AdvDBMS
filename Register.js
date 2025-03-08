import { useState } from "react";
import axios from "axios";
 
const Register = () => {
    const [fullName, setFullName] = useState("");
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [roleId, setRoleId] = useState(1);
 
    const handleRegister = async (e) => {
        e.preventDefault();
        try {
            await axios.post("http://localhost:5000/register", { full_name: fullName, email, password, role_id: roleId });
            alert("Registration Successful. Please login.");
        } catch (err) {
            alert(err.response?.data?.error || "Registration failed");
        }
    };
 
    return (
<div>
<h2>Register</h2>
<form onSubmit={handleRegister}>
<input type="text" placeholder="Full Name" value={fullName} onChange={(e) => setFullName(e.target.value)} required />
<input type="email" placeholder="Email" value={email} onChange={(e) => setEmail(e.target.value)} required />
<input type="password" placeholder="Password" value={password} onChange={(e) => setPassword(e.target.value)} required />
<select value={roleId} onChange={(e) => setRoleId(e.target.value)}>
<option value={1}>Student</option>
<option value={2}>Professor</option>
<option value={3}>Admin</option>
</select>
<button type="submit">Register</button>
</form>
</div>
    );
};
 
export default Register;
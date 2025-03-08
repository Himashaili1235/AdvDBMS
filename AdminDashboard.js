import { useEffect, useState } from "react";
import axios from "axios";
 
const AdminDashboard = () => {
    const [users, setUsers] = useState([]);
    const [newRole, setNewRole] = useState({});
    // Fetch all users
    useEffect(() => {
        axios.get("http://localhost:5000/admin/users")
            .then(response => setUsers(response.data))
            .catch(error => console.error("Error fetching users:", error));
    }, []);
 
    // Handle role change selection
    const handleRoleChange = (userId, roleId) => {
        setNewRole({ ...newRole, [userId]: roleId });
    };
 
    // Update user role
    const updateUserRole = (userId) => {
        axios.post("http://localhost:5000/admin/update-role", {
            user_id: userId,
            role_id: newRole[userId]
        })
        .then(() => {
            alert("User role updated successfully!");
            window.location.reload(); // Refresh users
        })
        .catch(error => alert("Error updating role: " + error));
    };
 
    // Delete user
    const deleteUser = (userId) => {
        axios.post("http://localhost:5000/admin/delete-user", { user_id: userId })
        .then(() => {
            alert("User deleted successfully!");
            setUsers(users.filter(user => user.user_id !== userId)); // Remove from UI
        })
        .catch(error => alert("Error deleting user: " + error));
    };
 
    return (
<div>
<h2>Admin Dashboard - Manage Users</h2>
<table border="1">
<thead>
<tr>
<th>User ID</th>
<th>Full Name</th>
<th>Email</th>
<th>Role</th>
<th>Change Role</th>
<th>Action</th>
</tr>
</thead>
<tbody>
                    {users.map(user => (
<tr key={user.user_id}>
<td>{user.user_id}</td>
<td>{user.full_name}</td>
<td>{user.email}</td>
<td>{user.role_name}</td>
<td>
<select onChange={(e) => handleRoleChange(user.user_id, e.target.value)}>
<option value="1">Student</option>
<option value="2">Professor</option>
<option value="3">Admin</option>
</select>
<button onClick={() => updateUserRole(user.user_id)}>Update</button>
</td>
<td>
<button onClick={() => deleteUser(user.user_id)}>Delete</button>
</td>
</tr>
                    ))}
</tbody>
</table>
</div>
    );
};
 
export default AdminDashboard;
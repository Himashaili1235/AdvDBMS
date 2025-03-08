import { useEffect, useState } from "react";
import axios from "axios";
 
const StudentDashboard = ({ user }) => {
    const [grades, setGrades] = useState([]);
 
    useEffect(() => {
        axios.get(`http://localhost:5000/student/${user.id}/grades`)
            .then(response => setGrades(response.data))
            .catch(error => console.error("Error fetching grades:", error));
    }, [user.id]);
 
    return (
<div>
<h2>Student Dashboard</h2>
<ul>
                {grades.map(grade => (
<li key={grade.course_id}>{grade.course_name}: {grade.grade}</li>
                ))}
</ul>
</div>
    );
};
 
export default StudentDashboard;
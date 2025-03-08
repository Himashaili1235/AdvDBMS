import { useEffect, useState } from "react";
import axios from "axios";
 
const ProfessorDashboard = ({ user }) => {
    const [courses, setCourses] = useState([]);
    const [selectedCourse, setSelectedCourse] = useState(null);
    const [students, setStudents] = useState([]);
    const [updatedGrades, setUpdatedGrades] = useState({});
 
    // Fetch professor's courses
    useEffect(() => {
        axios.get(`http://localhost:5000/professor/${user.id}/courses`)
            .then(response => setCourses(response.data))
            .catch(error => console.error("Error fetching courses:", error));
    }, [user.id]);
 
    // Fetch students enrolled in the selected course
    const handleCourseSelect = (courseId) => {
        setSelectedCourse(courseId);
        axios.get(`http://localhost:5000/professor/${user.id}/students/${courseId}`)
            .then(response => setStudents(response.data))
            .catch(error => console.error("Error fetching students:", error));
    };
 
    // Handle grade update input
    const handleGradeChange = (studentId, newGrade) => {
        setUpdatedGrades({ ...updatedGrades, [studentId]: newGrade });
    };
 
    // Submit grade updates
    const handleUpdateGrades = () => {
        axios.post("http://localhost:5000/professor/update-grades", {
            course_id: selectedCourse,
            grades: updatedGrades
        })
        .then(() => {
            alert("Grades updated successfully!");
            setUpdatedGrades({});
            handleCourseSelect(selectedCourse); // Refresh student list
        })
        .catch(error => alert("Error updating grades: " + error));
    };
 
    return (
<div>
<h2>Professor Dashboard</h2>
<h3>Select a Course:</h3>
<select onChange={(e) => handleCourseSelect(e.target.value)}>
<option value="">-- Select Course --</option>
                {courses.map(course => (
<option key={course.course_id} value={course.course_id}>
                        {course.course_name}
</option>
                ))}
</select>
 
            {selectedCourse && (
<div>
<h3>Students Enrolled</h3>
<table border="1">
<thead>
<tr>
<th>Student Name</th>
<th>Current Grade</th>
<th>Update Grade</th>
</tr>
</thead>
<tbody>
                            {students.map(student => (
<tr key={student.student_id}>
<td>{student.student_name}</td>
<td>{student.current_grade}</td>
<td>
<input
                                            type="text"
                                            placeholder="Enter new grade"
                                            value={updatedGrades[student.student_id] || ""}
                                            onChange={(e) => handleGradeChange(student.student_id, e.target.value)}
                                        />
</td>
</tr>
                            ))}
</tbody>
</table>
<button onClick={handleUpdateGrades}>Update Grades</button>
</div>
            )}
</div>
    );
};
 
export default ProfessorDashboard;
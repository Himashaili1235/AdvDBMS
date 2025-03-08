const express = require("express");

const cors = require("cors");

const bodyParser = require("body-parser");

const crypto = require("crypto");

const db = require("./db"); // ✅ Import database connection from db.js
 
const app = express();

app.use(cors());

app.use(bodyParser.json());
 
// 📌 Login API Endpoint
app.post("/login", (req, res) => {
    const { email, password } = req.body;
    const query = "SELECT * FROM Users WHERE email = ?";
    db.query(query, [email], (err, result) => {
        if (err) {
            return res.status(500).json({ error: "Database error" });
        }
        if (result.length === 0) {
            return res.status(401).json({ error: "Invalid email or password" });
        }
 
        const user = result[0];
        const inputHashedPassword = crypto.createHash("sha256").update(password + user.salt).digest("hex");
 
        console.log("🔹 Input Password Hash:", inputHashedPassword);
        console.log("🔹 Stored Hash in DB:", user.password_hash);
 
        if (inputHashedPassword !== user.password_hash) {
            return res.status(401).json({ error: "Invalid email or password" });
        }
 
        res.json({ message: "Login successful", user: { id: user.user_id, role: user.role_id } });
    });
});
 
// Start the server

const PORT = 5000;

app.listen(PORT, () => {

    console.log(`🚀 Backend running on http://localhost:${PORT}`);

});

 
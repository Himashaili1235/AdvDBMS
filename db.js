const mysql = require("mysql2");
 
const db = mysql.createConnection({
    host: "127.0.0.1",
    user: "root",    // Change this if necessary
    password: "Hima@1235",    // Your MySQL password
    database: "sgs_db"
});
 
db.connect((err) => {
    if (err) {
        console.error("Database connection failed: " + err.message);
    } else {
        console.log("Connected to MySQL Database");
    }
});
 
module.exports = db;
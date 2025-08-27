# Student-Accommodation-System
System for helping students to find accommodations.
# Student Accommodation System

## Description
A web-based Student Accommodation Management System built with PHP, MySQL, HTML, CSS, and JavaScript.  
This system allows administrators to manage students, accommodations, rooms, and payments efficiently.  
Students can view and download their records. Both students and admins can create accounts and log in.  
Each page supports full CRUD operations (Create, Read, Update, Delete).

---

## Running the System
1. **Install XAMPP**  
   - Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org) and install it on your computer.  
   - Start the **Apache** and **MySQL** services using the XAMPP control panel.  

2. **Set Up the Database**  
   - Open **phpMyAdmin** at `http://localhost/phpmyadmin/`.  
   - Create a new database (e.g., `student_accommodation`).  
   - Import the SQL file provided in the project (usually `database.sql`) to create tables and sample data.  

3. **Configure Database Connection**  
   - Open the PHP configuration file (`db_connect.php`).  
   - Update the database credentials if necessary:  
     ```php
     $servername = "localhost";
     $username = "root";   // default for XAMPP
     $password = "";       // default for XAMPP
     $dbname = "student_accommodation";
     ```
     -Make sure the database connection details correspond with your setup.
     

4. **Run the System in a Web Browser**  
   - Copy the project folder into the `htdocs` directory of XAMPP (e.g., `C:\xampp\htdocs\StudentAccommodationSystem`).  
   - Open your web browser and navigate to:  
     ```
     http://localhost/StudentAccommodationSystem/
     ```  
   - You should now see the login or registration page for the system.  




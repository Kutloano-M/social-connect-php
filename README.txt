Connect Sphere — README text file

Welcome to the  "Connect Sphere" Social Platform — a lightweight, beginner-friendly social networking web app built with PHP, MySQL, and a bit of JavaScript.
It allows users to register, log in, create posts, upload images, search for other users, edit their profiles, and chat through private messages.

------------------------------------------------------------
 Requirements
------------------------------------------------------------
Before getting started, make sure your environment includes:

- PHP 7.4+ (with PDO and file info extensions enabled)
- MySQL or MariaDB database
- A local web server such as XAMPP, WAMP, or LAMP

------------------------------------------------------------
Setup Instructions
------------------------------------------------------------

Step 1 — Place the Project
Copy or move the folder named 'Connect Sphere' into your web server’s document root.
For example:
- XAMPP → C:\xampp\htdocs\Connect Sphere
- WAMP → C:\wamp\www\Connect Sphere

------------------------------------------------------------
Step 2 — Create the Database
You can create the database in two ways:

Option A: Using phpMyAdmin
1. Open phpMyAdmin.
2. Click on “Import”.
3. Select the file 'information.sql'.
4. Click Go to execute it.

Option B: Using Command Line
mysql -u root -p < informtion.sql

This will create the database and all required tables automatically.

------------------------------------------------------------
Step 3 — Configure the Database Connection
Open the file:
includes/configuration.php

Update your database username and password to match your local setup (usually 'root' with no password for XAMPP).

------------------------------------------------------------
Step 4 — Set Upload Permissions
Make sure the folder 'uploads/' is writable by the server.
You can run this command (on Linux/Mac):
chmod 755 uploads
or
chmod 775 uploads

------------------------------------------------------------
Step 5 — Launch the Site
Open your browser and go to:
http://localhost/Connect Sphere/register.php

------------------------------------------------------------
Step 6 — Create a Test Account
Sign up by entering:
- Your name
- Email address
- Password

Once registered, log in at:
http://localhost/Connect Sphere/login.php

You’ll be redirected to your Dashboard (dashboard.php) where you can:
- Create posts and upload images
- Search for users
- View and edit profiles
- Change your profile picture or display name
- Send and receive messages

------------------------------------------------------------
 Security Notes
------------------------------------------------------------
This project includes several key security practices:

- SQL Injection Prevention — All queries use prepared statements (PDO).
- XSS Protection — Output is escaped using htmlspecialchars() via helper function e().
- Secure Passwords — Stored using password_hash() and verified with password_verify().
- Safe File Uploads — Checked for valid MIME types and limited to 5MB.

Additional Recommendations for Production:
- Add CSRF tokens to all forms.
- Store uploaded files outside the web root.
- Implement an SMTP-based password reset system.

------------------------------------------------------------
Sample Test Account
------------------------------------------------------------
Full Name: Kutloano Mbele
Email: KutloanoM@gmail.com
Password: choose one at registration

------------------------------------------------------------
Project Structure
------------------------------------------------------------
includes/    → Configuration, helper functions, and layout files
css/         → Stylesheets for the site
js/          → Client-side JavaScript
uploads/     → User-uploaded images
init.sql     → Database schema

------------------------------------------------------------
 Optional Add-ons
------------------------------------------------------------
- Password Reset Flow — Token-based reset (email or printed token for demo)
- Friend Requests — Connect and follow users
- Likes & Notifications — Add engagement features
- Project Packaging — Easily zip and deploy the project

------------------------------------------------------------
 Summary
------------------------------------------------------------
This "Connect Sphere" social platform is an excellent starting point for learning:
- PHP (sessions, forms, file handling)
- MySQL (database design and CRUD operations)
- Front-end basics (HTML, CSS, JavaScript)

It’s small, fast, and perfect for both learning and demonstration.

------------------------------------------------------------
End of File
------------------------------------------------------------

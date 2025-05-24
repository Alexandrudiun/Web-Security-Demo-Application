
🛡️ Web Security Demo Application
By:
-Alexandrudiun
-Eng-Bkm(Biruk Meles)
-Geroge

This is an intentionally vulnerable PHP application created for **educational purposes only**. 
It demonstrates common web vulnerabilities such as:

- SQL Injection
- Cross-Site Scripting (XSS)
- Cross-Site Request Forgery (CSRF)
- Insecure Direct Object Reference (IDOR)
- Path Traversal

⚠️ Do NOT deploy this on a public server. This application is for local testing only.

------------------------------
🚀 Quick Start Guide
------------------------------

✅ Requirements:
- XAMPP for Windows: https://www.apachefriends.org/index.html
- Web browser (e.g. Chrome, Firefox)

📦 Setup Instructions:

1. Download & Install XAMPP
   - Install XAMPP on your Windows machine.
   - Launch the XAMPP Control Panel and start Apache and MySQL.

2. Place Files in htdocs
   - Copy all PHP files (index.php, dashboard.php, conn.php, reset.php, etc.) into:
     C:\xampp\htdocs\login_app\

3. Create the Database
   - In the XAMPP Control Panel, click Admin under MySQL to open phpMyAdmin.
   - Click on the Databases tab and create a new database called:
     users
   - Select the new 'users' database, go to the Import tab, and upload:
     users.sql

4. Access the App
   - In your browser, open:
     http://localhost/login_app/

5. Explore the Vulnerabilities
   - Test login, message posting, and other features.
   - Use the included 'exploits.html' and 'csrf-demo.html' to simulate attacks.
   - Refer to 'attack-vectors.txt' for detailed payloads and explanations.

------------------------------
📁 File Overview
------------------------------

| File               | Purpose                                               |
|--------------------|-------------------------------------------------------|
| index.php          | Login & registration form (vulnerable to SQLi)       |
| dashboard.php      | User dashboard (XSS, CSRF, IDOR)                      |
| conn.php           | Database connection configuration                    |
| reset.php          | Database reset/backup with path traversal flaw       |
| users.sql          | SQL dump to set up the users table                   |
| csrf-demo.html     | Example CSRF attack page                             |
| exploits.html      | Demos for multiple vulnerability types               |
| attack-vectors.txt | Cheat sheet with payloads for various attacks        |

------------------------------
👨‍🏫 Educational Note
------------------------------

This lab demonstrates how real-world attacks work so developers and students 
can learn how to defend against them. Use only in local or controlled environments.


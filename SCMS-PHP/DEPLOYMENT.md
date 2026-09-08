# Deployment Guide - SCMS

Guide for deploying the Smart College Complaint Management System to a production server.

## 🚀 Pre-Deployment Checklist

Before deploying to production:

- [ ] All features tested in local environment
- [ ] Database backed up
- [ ] Production server meets requirements (PHP 7.4+, MySQL 5.7+)
- [ ] SSL certificate ready (HTTPS)
- [ ] Domain name configured
- [ ] Server access credentials available

---

## 📋 Server Requirements

### Minimum Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB 10.2+)
- **Apache**: 2.4+ with mod_rewrite enabled
- **Disk Space**: 500MB minimum
- **RAM**: 512MB minimum
- **SSL Certificate**: Required for production

### Recommended PHP Extensions
- mysqli
- session
- json
- gd (for image processing)
- fileinfo (for file uploads)

---

## 🔧 Deployment Steps

### Step 1: Prepare Files

1. **Clean up development files**:
   ```
   Remove:
   - TESTING_CHECKLIST.md
   - DEPLOYMENT.md
   - Any test.php files
   - .git folder (if exists)
   ```

2. **Update configuration files**:
   - Review `includes/database.php`
   - Update `.htaccess` if needed
   - Check file permissions

3. **Create archive**:
   ```
   Zip the SCMS-PHP folder
   Name: scms-production-v1.0.zip
   ```

### Step 2: Upload to Server

**Via FTP/SFTP:**
1. Connect to server using FTP client (FileZilla, WinSCP)
2. Upload files to: `/public_html/` or `/var/www/html/`
3. Extract if uploaded as zip

**Via SSH:**
```bash
# Upload via SCP
scp -r SCMS-PHP user@your-server.com:/var/www/html/

# Or extract zip on server
unzip scms-production-v1.0.zip -d /var/www/html/
```

### Step 3: Set File Permissions

**Linux/Unix servers:**
```bash
# Navigate to project directory
cd /var/www/html/SCMS-PHP

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Set uploads directory writable
chmod 775 uploads/

# Set owner (replace www-data with your web server user)
chown -R www-data:www-data .
```

**Important directories:**
- `uploads/` - Must be writable (755 or 775)
- All other directories - 755
- All files - 644

### Step 4: Create Production Database

1. **Access MySQL**:
   ```bash
   mysql -u root -p
   ```

2. **Create database**:
   ```sql
   CREATE DATABASE scms_production CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

3. **Create database user** (recommended for security):
   ```sql
   CREATE USER 'scms_user'@'localhost' IDENTIFIED BY 'strong_password_here';
   GRANT ALL PRIVILEGES ON scms_production.* TO 'scms_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

4. **Import schema**:
   ```bash
   mysql -u scms_user -p scms_production < database/scms.sql
   ```

### Step 5: Update Configuration

Edit `includes/database.php`:

```php
<?php
// Production Database Configuration
$host = 'localhost';
$username = 'scms_user';
$password = 'your_strong_password';
$database = 'scms_production';

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    // In production, log error instead of displaying
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Service temporarily unavailable. Please try again later.");
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');
?>
```

### Step 6: Security Configuration

1. **Disable error display** - Edit `includes/database.php`:
   ```php
   // At the top of file
   error_reporting(E_ALL);
   ini_set('display_errors', 0);
   ini_set('log_errors', 1);
   ini_set('error_log', '/var/log/php-errors.log');
   ```

2. **Update .htaccess** for production:
   ```apache
   # Add to .htaccess
   
   # Force HTTPS
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   
   # Prevent access to sensitive files
   <FilesMatch "(^\.ht|\.sql|\.md|composer\..*)">
       Order deny,allow
       Deny from all
   </FilesMatch>
   
   # Security headers
   <IfModule mod_headers.c>
       Header always set X-Content-Type-Options "nosniff"
       Header always set X-Frame-Options "SAMEORIGIN"
       Header always set X-XSS-Protection "1; mode=block"
       Header always set Referrer-Policy "strict-origin-when-cross-origin"
       Header always set Content-Security-Policy "default-src 'self' https:; script-src 'self' 'unsafe-inline' https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;"
   </IfModule>
   ```

3. **Change default admin password**:
   ```sql
   UPDATE users 
   SET password = '$2y$10$NEW_HASHED_PASSWORD_HERE' 
   WHERE email = 'admin@college.edu';
   ```
   
   Generate new hash using PHP:
   ```php
   echo password_hash('your_new_password', PASSWORD_DEFAULT);
   ```

4. **Remove demo accounts** (optional):
   ```sql
   DELETE FROM users WHERE email = 'john.doe@college.edu';
   ```

### Step 7: Configure Email (Optional)

If adding email notifications in future, configure SMTP:

Edit `includes/functions.php`, add:
```php
function sendEmail($to, $subject, $message) {
    $headers = "From: noreply@yourcollege.edu\r\n";
    $headers .= "Reply-To: support@yourcollege.edu\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}
```

### Step 8: Test Production Environment

1. **Access via domain**: `https://yourdomain.com/SCMS-PHP`

2. **Verify checklist**:
   - [ ] Homepage loads correctly
   - [ ] HTTPS is working (SSL certificate valid)
   - [ ] Login system works
   - [ ] Database connection successful
   - [ ] File uploads work
   - [ ] No PHP errors displayed
   - [ ] All pages accessible
   - [ ] Mobile responsive

3. **Test critical workflows**:
   - [ ] Student registration
   - [ ] Complaint submission
   - [ ] Admin login and actions
   - [ ] File upload and download

### Step 9: Setup Backups

**Database Backup Script** (`backup-db.sh`):
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/scms"
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u scms_user -p'password' scms_production > $BACKUP_DIR/scms_$DATE.sql

# Compress
gzip $BACKUP_DIR/scms_$DATE.sql

# Delete backups older than 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Backup completed: scms_$DATE.sql.gz"
```

**Setup Cron Job** (daily at 2 AM):
```bash
crontab -e

# Add:
0 2 * * * /path/to/backup-db.sh
```

### Step 10: Monitor and Maintain

1. **Setup error monitoring**:
   - Check `/var/log/apache2/error.log`
   - Check PHP error log
   - Monitor database performance

2. **Regular tasks**:
   - Review security logs weekly
   - Update PHP/MySQL as needed
   - Monitor disk space (uploads folder)
   - Check database size
   - Review and archive old complaints

---

## 🔒 Security Best Practices

### Must Do
- ✅ Use HTTPS (SSL certificate)
- ✅ Change default admin password
- ✅ Use strong database password
- ✅ Disable PHP error display
- ✅ Set proper file permissions
- ✅ Keep PHP/MySQL updated
- ✅ Regular backups
- ✅ Validate all user inputs
- ✅ Use prepared statements (already implemented)

### Recommended
- 🔐 Implement rate limiting for login attempts
- 🔐 Add CAPTCHA to signup/login forms
- 🔐 Setup fail2ban for brute force protection
- 🔐 Enable ModSecurity (Web Application Firewall)
- 🔐 Regular security audits
- 🔐 Monitor suspicious activities

### File Upload Security
```php
// Already implemented in functions.php:
- File type validation
- File size limits
- Safe file naming
- Separate storage directory
```

---

## 🌐 Domain Configuration

### If using subdomain (recommended)
```
https://complaints.yourcollege.edu
```

**Apache VirtualHost configuration**:
```apache
<VirtualHost *:443>
    ServerName complaints.yourcollege.edu
    DocumentRoot /var/www/html/SCMS-PHP
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.crt
    SSLCertificateKeyFile /path/to/private.key
    SSLCertificateChainFile /path/to/chain.crt
    
    <Directory /var/www/html/SCMS-PHP>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/scms-error.log
    CustomLog ${APACHE_LOG_DIR}/scms-access.log combined
</VirtualHost>
```

### Update URLs in code
If using subdomain, update `index.php`:
```php
header('Location: https://complaints.yourcollege.edu/pages/home.php');
```

---

## 📊 Performance Optimization

### Enable Caching
Add to `.htaccess`:
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### Enable Compression
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

### Database Optimization
```sql
-- Add indexes for better performance
ALTER TABLE complaints ADD INDEX idx_status (status);
ALTER TABLE complaints ADD INDEX idx_user_id (user_id);
ALTER TABLE complaints ADD INDEX idx_created_at (created_at);
```

---

## 🐛 Troubleshooting Production Issues

### Issue: 500 Internal Server Error
**Check**:
```bash
tail -f /var/log/apache2/error.log
```
**Common causes**:
- Incorrect file permissions
- PHP syntax errors
- .htaccess misconfiguration
- Missing PHP extensions

### Issue: Database connection failed
**Check**:
- MySQL is running: `systemctl status mysql`
- Database credentials correct
- User has proper permissions
- Firewall not blocking port 3306

### Issue: File uploads not working
**Check**:
- `uploads/` directory exists and is writable
- PHP `upload_max_filesize` is adequate
- PHP `post_max_size` is adequate
- Disk space available

### Issue: Session issues
**Check**:
- PHP session directory writable: `/var/lib/php/sessions`
- Session cookies allowed
- HTTPS properly configured

---

## 📱 Post-Deployment Tasks

### Week 1
- [ ] Monitor error logs daily
- [ ] Check user feedback
- [ ] Verify email delivery (if configured)
- [ ] Test from different networks
- [ ] Mobile app testing

### Week 2-4
- [ ] Review performance metrics
- [ ] Check database size and growth
- [ ] Verify backup system working
- [ ] Update documentation based on user feedback
- [ ] Plan feature enhancements

### Monthly
- [ ] Security audit
- [ ] Update PHP/MySQL if needed
- [ ] Review and archive old data
- [ ] Check disk space
- [ ] Performance optimization review

---

## 📝 Rollback Plan

If critical issues found in production:

1. **Disable site temporarily**:
   Create `maintenance.html` in root:
   ```html
   <!DOCTYPE html>
   <html>
   <head><title>Maintenance</title></head>
   <body>
       <h1>System Maintenance</h1>
       <p>We're performing maintenance. Please check back in 30 minutes.</p>
   </body>
   </html>
   ```

2. **Restore database backup**:
   ```bash
   mysql -u scms_user -p scms_production < /backups/scms/scms_YYYYMMDD.sql
   ```

3. **Restore previous code version**:
   ```bash
   cp -r /backups/code/SCMS-PHP-v1.0/* /var/www/html/SCMS-PHP/
   ```

4. **Test and re-enable**

---

## 📞 Support Contacts

**Technical Support**:
- Server Admin: [admin@yourcollege.edu]
- Database Admin: [dba@yourcollege.edu]
- Developer: [developer@yourcollege.edu]

**Emergency Contacts**:
- On-call number: [+1-XXX-XXX-XXXX]
- Hosting provider: [provider support link]

---

## ✅ Deployment Complete Checklist

- [ ] Files uploaded to production server
- [ ] File permissions set correctly
- [ ] Database created and schema imported
- [ ] Configuration updated for production
- [ ] Security measures implemented
- [ ] HTTPS/SSL working
- [ ] Default passwords changed
- [ ] Backup system configured
- [ ] Error logging enabled
- [ ] Production testing completed
- [ ] Monitoring setup
- [ ] Documentation updated
- [ ] Team trained on system

---

**🎉 Deployment Successful!**

Your Smart College Complaint Management System is now live and ready to serve students and administrators.

**Production URL**: https://yourdomain.com/SCMS-PHP  
**Admin Access**: https://yourdomain.com/SCMS-PHP/pages/login.php  
**Deployment Date**: _______________  
**Deployed By**: _______________

For ongoing maintenance and updates, refer to this guide and the main README.md documentation.

# Smart College Complaint Management System (SCMS)

A professional, full-featured PHP-based web application for managing student complaints in educational institutions. Built with vanilla PHP, MySQL, and modern CSS/JavaScript - no frameworks required.

## 🌟 Features

### For Students
- **User Registration & Authentication** - Secure signup with password hashing
- **Submit Complaints** - Report issues with file attachments, category selection, and priority levels
- **Track Complaints** - Real-time status tracking with detailed timeline view
- **Dashboard** - Personal statistics and recent complaints overview
- **Filter & Search** - Advanced filtering by status, category, and priority

### For Administrators
- **Admin Dashboard** - Comprehensive overview with statistics and analytics
- **Complaint Management** - View, filter, sort, and manage all complaints
- **Status Updates** - Update complaint status with remarks and timeline tracking
- **Department Assignment** - Assign complaints to appropriate departments
- **Priority Management** - Update complaint priority levels
- **User Management** - View and manage student accounts, suspend/activate users
- **Category Analytics** - Visual breakdown of complaints by category

### Technical Features
- **Fully Responsive Design** - Mobile-first approach, works on all devices
- **Modern UI/UX** - Professional design with gradient cards, smooth animations
- **Session-based Authentication** - Secure role-based access control
- **File Upload System** - Support for images and documents with validation
- **Real-time Validation** - Client-side and server-side form validation
- **Timeline View** - Visual representation of complaint progress
- **Toast Notifications** - User-friendly feedback messages
- **Mobile Menu** - Responsive navigation with hamburger menu

## 📋 Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB 10.2+)
- **Web Server**: Apache with mod_rewrite enabled (XAMPP/WAMP/LAMP)
- **Browser**: Modern browser with JavaScript enabled

## 🚀 Installation & Setup

### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP to your preferred location (e.g., `C:\xampp`)
3. Start Apache and MySQL from the XAMPP Control Panel

### Step 2: Setup Project Files

1. Copy the `SCMS-PHP` folder to your XAMPP htdocs directory:
   ```
   C:\xampp\htdocs\SCMS-PHP
   ```

2. Ensure the `uploads` folder has write permissions:
   - Right-click on `uploads` folder
   - Properties → Security → Edit
   - Grant "Full Control" to "Users" group

### Step 3: Create Database

1. Open phpMyAdmin: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

2. Create a new database:
   - Click "New" in the left sidebar
   - Database name: `scms`
   - Collation: `utf8mb4_general_ci`
   - Click "Create"

3. Import the database schema:
   - Select the `scms` database
   - Click "Import" tab
   - Choose file: `database/scms.sql`
   - Click "Go"

### Step 4: Configure Database Connection

The database configuration is already set in `includes/database.php`:

```php
$host = 'localhost';
$username = 'root';
$password = '';  // Default XAMPP has no password
$database = 'scms';
```

**Note**: If you changed your MySQL root password, update the `$password` variable.

### Step 5: Access the Application

Open your browser and navigate to:
```
http://localhost/SCMS-PHP
```

You should see the homepage. From there, you can:
- Browse public pages
- Sign up as a new student
- Login with demo credentials (see below)

## 👤 Demo Credentials

The SQL file includes two demo accounts:

### Admin Account
- **Email**: `admin@college.edu`
- **Password**: `admin123`
- **Access**: Full administrative controls

### Student Account
- **Email**: `john.doe@college.edu`
- **Password**: `student123`
- **Student ID**: `STU2024001`
- **Access**: Student portal features

## 📁 Project Structure

```
SCMS-PHP/
├── admin/                      # Admin module
│   ├── dashboard.php          # Admin dashboard with statistics
│   ├── complaints.php         # Manage all complaints
│   ├── complaint-details.php  # Detailed complaint view
│   └── users.php              # User management
├── assets/                     # Static assets
│   ├── css/
│   │   ├── style.css          # Main styles with design system
│   │   ├── dashboard.css      # Dashboard-specific styles
│   │   └── responsive.css     # Mobile responsive styles
│   └── js/
│       └── main.js            # JavaScript functionality
├── database/
│   └── scms.sql               # Database schema and demo data
├── includes/                   # PHP components
│   ├── auth.php               # Authentication & authorization
│   ├── database.php           # Database connection
│   ├── functions.php          # Utility functions
│   ├── header.php             # HTML head section
│   ├── navbar.php             # Navigation bar
│   └── footer.php             # Footer section
├── pages/                      # Public pages
│   ├── home.php               # Landing page
│   ├── about.php              # About page
│   ├── contact.php            # Contact page
│   ├── complaints.php         # Public complaints listing
│   ├── login.php              # Login form
│   ├── signup.php             # Registration form
│   └── logout.php             # Logout handler
├── student/                    # Student module
│   ├── dashboard.php          # Student dashboard
│   ├── my-complaints.php      # View student's complaints
│   ├── submit-complaint.php   # Submit new complaint
│   └── track-complaint.php    # Track complaint status
├── uploads/                    # File uploads directory
├── .htaccess                   # Apache configuration
├── index.php                   # Entry point
└── README.md                   # This file
```

## 🎨 Design System

### Colors
- **Primary**: Deep Navy (#1e293b)
- **Secondary**: Professional Blue (#3b82f6)
- **Success**: Green (#10b981)
- **Warning**: Orange (#f59e0b)
- **Error**: Red (#ef4444)

### Typography
- **Font Family**: Inter (Google Fonts)
- **Base Size**: 16px
- **Line Height**: 1.6

### Spacing
- Base unit: 8px
- Consistent 8px spacing system throughout

## 🔐 Security Features

- Password hashing using PHP `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- CSRF protection via session validation
- Session-based authentication
- Role-based access control
- File upload validation (type, size, extension)
- Secure file storage outside public directory access

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 📱 Responsive Breakpoints

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

## 🛠️ Customization

### Changing Colors

Edit `assets/css/style.css`:
```css
:root {
    --primary: #1e293b;
    --secondary: #3b82f6;
    /* Modify other color variables */
}
```

### Modifying Database

1. Update `database/scms.sql` with your schema changes
2. Re-import the database
3. Update relevant PHP files to match new structure

### Adding New Complaint Categories

Edit `student/submit-complaint.php` and `admin/complaints.php`:
```php
<option value="YourCategory">Your Category</option>
```

### Changing File Upload Settings

Edit `includes/functions.php` in the `handleFileUpload()` function:
```php
$maxSize = 5 * 1024 * 1024; // 5MB (change as needed)
$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']; // Add/remove types
```

## 🧪 Testing the System

### Test Workflow

1. **Sign Up**
   - Navigate to Signup page
   - Create a new student account
   - Verify email format and password requirements

2. **Login**
   - Login with student credentials
   - Verify redirect to student dashboard

3. **Submit Complaint**
   - Navigate to "Submit Complaint"
   - Fill in all required fields
   - Upload a test file (< 5MB)
   - Submit and note the Complaint ID

4. **Track Complaint**
   - Go to "Track Complaint"
   - Enter your Complaint ID
   - Verify timeline display

5. **Admin Login**
   - Logout from student account
   - Login with admin credentials
   - Verify redirect to admin dashboard

6. **Admin Actions**
   - View complaint in admin panel
   - Assign department
   - Update priority
   - Change status to "In Progress"
   - Add remarks
   - Verify timeline updates

7. **Student Verification**
   - Logout and login as student
   - Check "Track Complaint"
   - Verify admin updates appear in timeline

## 🐛 Troubleshooting

### Issue: "Connection failed" error

**Solution**: 
- Ensure MySQL is running in XAMPP
- Verify database credentials in `includes/database.php`
- Check if `scms` database exists

### Issue: File upload fails

**Solution**:
- Check `uploads/` folder exists and has write permissions
- Verify `upload_max_filesize` in php.ini (should be at least 5M)
- Check file type is allowed (JPG, PNG, PDF, DOC)

### Issue: Styles not loading

**Solution**:
- Clear browser cache
- Verify file paths in `includes/header.php`
- Check Apache is serving CSS files correctly

### Issue: Session errors

**Solution**:
- Ensure `session_start()` is at the top of PHP files
- Check PHP session directory has write permissions
- Clear browser cookies

### Issue: Redirects not working

**Solution**:
- Enable `mod_rewrite` in Apache
- Verify `.htaccess` file exists
- Check Apache configuration allows `.htaccess` override

## 📊 Database Tables

### users
- User accounts (students and admins)
- Fields: id, full_name, email, password, student_id, phone, role, status, created_at

### complaints
- All complaint records
- Fields: id, complaint_id, user_id, title, category, priority, description, location, attachment, department, status, admin_remarks, created_at, updated_at, resolved_at

### complaint_updates
- Complaint status history
- Fields: id, complaint_id, status, remarks, created_at

## 🤝 Contributing

This is a college project. If you'd like to extend it:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is created for educational purposes. Feel free to use and modify for your college projects.

## 👨‍💻 Developer Notes

### Key Design Decisions

1. **No Frameworks**: Built with vanilla PHP/CSS/JS for learning purposes and maximum control
2. **Session-based Auth**: Simpler than JWT for college project scope
3. **File Structure**: Separated by module (admin/student/pages) for clarity
4. **Component Reuse**: Header/navbar/footer as includes for DRY principle
5. **Mobile-first**: Responsive design prioritizes mobile experience
6. **Security**: Uses PHP best practices (prepared statements, password hashing)

### Future Enhancements

- Email notifications for complaint updates
- Export complaints to PDF/Excel
- Advanced analytics dashboard
- Multi-department assignment
- Complaint escalation system
- Student complaint rating/feedback
- API for mobile app integration
- Dark mode toggle
- Multi-language support

## 📞 Support

For issues or questions:
1. Check the Troubleshooting section
2. Review PHP error logs: `C:\xampp\apache\logs\error.log`
3. Enable PHP error display in development:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

## 🎓 Learning Resources

- [PHP Manual](https://www.php.net/manual/en/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MDN Web Docs](https://developer.mozilla.org/)
- [XAMPP Documentation](https://www.apachefriends.org/docs/)

---

**Built with ❤️ for Smart College Management**

Version: 1.0.0  
Last Updated: August 2026

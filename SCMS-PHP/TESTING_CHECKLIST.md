# Testing Checklist - SCMS

Complete testing guide to verify all functionality works correctly.

## ✅ Pre-Testing Setup

- [ ] XAMPP installed and running (Apache + MySQL)
- [ ] Project copied to `C:\xampp\htdocs\SCMS-PHP`
- [ ] Database `scms` created and imported from `database/scms.sql`
- [ ] Browser opened at `http://localhost/SCMS-PHP`

---

## 🔍 Phase 1: Public Pages Testing

### Home Page
- [ ] Navigate to: `http://localhost/SCMS-PHP` or `http://localhost/SCMS-PHP/pages/home.php`
- [ ] Verify hero section displays
- [ ] Check statistics cards show numbers (total, pending, resolved)
- [ ] Verify "How It Works" section displays
- [ ] Check complaint categories grid displays
- [ ] Verify footer displays
- [ ] Test mobile menu (resize browser to mobile width)
- [ ] Click all navigation links work

### About Page
- [ ] Navigate to: `http://localhost/SCMS-PHP/pages/about.php`
- [ ] Verify page content displays
- [ ] Check navigation works
- [ ] Test responsive design (resize browser)

### Contact Page
- [ ] Navigate to: `http://localhost/SCMS-PHP/pages/contact.php`
- [ ] Verify contact information displays
- [ ] Check responsive layout

### Public Complaints Page
- [ ] Navigate to: `http://localhost/SCMS-PHP/pages/complaints.php`
- [ ] Verify complaint cards display
- [ ] Test category filter (select different categories)
- [ ] Test search functionality (search by keyword)
- [ ] Verify filtering works correctly

---

## 🎓 Phase 2: Student Registration & Authentication

### Sign Up
- [ ] Navigate to: `http://localhost/SCMS-PHP/pages/signup.php`
- [ ] Test empty form submission (should show errors)
- [ ] Test invalid email format (should show error)
- [ ] Test password mismatch (should show error)
- [ ] Test short password < 6 chars (should show error)
- [ ] Fill valid data:
  ```
  Full Name: Test Student
  Email: test.student@college.edu
  Student ID: STU2024999
  Phone: 1234567890
  Password: test123
  Confirm Password: test123
  ```
- [ ] Submit and verify success message
- [ ] Verify redirect to login page

### Login (Student)
- [ ] Navigate to: `http://localhost/SCMS-PHP/pages/login.php`
- [ ] Test empty form (should show errors)
- [ ] Test invalid credentials (should show error)
- [ ] Login with demo student account:
  ```
  Email: john.doe@college.edu
  Password: student123
  ```
- [ ] Verify redirect to student dashboard
- [ ] Check session is active (name displays in navbar)

---

## 📝 Phase 3: Student Module Testing

### Student Dashboard
- [ ] URL: `http://localhost/SCMS-PHP/student/dashboard.php`
- [ ] Verify welcome message with student name
- [ ] Check statistics cards display correctly:
  - Total Complaints
  - Pending
  - In Progress
  - Resolved
- [ ] Verify quick action cards display
- [ ] Check recent complaints section
- [ ] Test "Submit New Complaint" button navigates correctly
- [ ] Test "View All" link works

### Submit Complaint
- [ ] Navigate to: `http://localhost/SCMS-PHP/student/submit-complaint.php`
- [ ] Test empty form submission (should show errors)
- [ ] Fill complaint form:
  ```
  Title: Broken Projector in Room 305
  Category: Infrastructure
  Priority: High
  Location: Main Building, Room 305
  Description: The projector in room 305 has been malfunctioning for 3 days. It keeps turning off during lectures, disrupting classes.
  ```
- [ ] Test file upload:
  - Try uploading file > 5MB (should show error)
  - Try uploading .exe file (should show error)
  - Upload valid image (JPG/PNG) or PDF
  - Verify file preview appears
- [ ] Submit complaint
- [ ] Verify success message with Complaint ID displayed
- [ ] Note the Complaint ID for next tests

### My Complaints
- [ ] Navigate to: `http://localhost/SCMS-PHP/student/my-complaints.php`
- [ ] Verify submitted complaint appears in list
- [ ] Test filters:
  - Filter by Status (Pending, In Progress, Resolved)
  - Filter by Category
  - Filter by Priority
  - Test search (search by title or ID)
- [ ] Click "Apply Filters" and verify results update
- [ ] Click "Clear" and verify filters reset
- [ ] Click "View Details" on a complaint

### Track Complaint
- [ ] Navigate to: `http://localhost/SCMS-PHP/student/track-complaint.php`
- [ ] Enter your Complaint ID from earlier
- [ ] Click "Search"
- [ ] Verify complaint details display:
  - Title, Description, Status
  - Category, Priority, Department (if assigned)
  - Location, Attachment link
  - Submitted date
- [ ] Check timeline section displays correctly
- [ ] Verify "Initial Submission" entry appears
- [ ] Try accessing without Complaint ID (should show search form)

### Student Logout
- [ ] Click "Logout" in navigation
- [ ] Verify redirect to home page
- [ ] Try accessing student dashboard URL directly
- [ ] Should redirect to login page

---

## 👨‍💼 Phase 4: Admin Module Testing

### Admin Login
- [ ] Navigate to: `http://localhost/SCMS-PHP/pages/login.php`
- [ ] Login with admin credentials:
  ```
  Email: admin@college.edu
  Password: admin123
  ```
- [ ] Verify redirect to admin dashboard
- [ ] Check "Admin" badge or indicator displays

### Admin Dashboard
- [ ] URL: `http://localhost/SCMS-PHP/admin/dashboard.php`
- [ ] Verify statistics cards display:
  - Total Complaints
  - Pending
  - In Progress
  - Resolved
  - Today's Complaints
  - Average Resolution Time
- [ ] Check pending alert displays if pending complaints exist
- [ ] Verify "Complaints by Category" chart displays
- [ ] Check recent complaints section shows entries
- [ ] Test "View All" and "Review Now" links

### Manage Complaints
- [ ] Navigate to: `http://localhost/SCMS-PHP/admin/complaints.php`
- [ ] Verify complaints table displays all complaints
- [ ] Check student information displays correctly
- [ ] Test filters:
  - Status filter (all, pending, in_progress, resolved, rejected)
  - Category filter
  - Priority filter
  - Department filter
  - Search (by complaint ID, title, student name)
  - Sort by (newest, oldest, priority)
- [ ] Click "Apply Filters" and verify results
- [ ] Test mobile view (resize browser)
- [ ] Verify mobile cards display on small screens
- [ ] Click "View" button on any complaint

### Complaint Details & Actions
- [ ] URL: `http://localhost/SCMS-PHP/admin/complaint-details.php?id=YOUR_COMPLAINT_ID`
- [ ] Verify all complaint details display correctly
- [ ] Check student information section
- [ ] View attachment (if exists)
- [ ] Check timeline displays

**Test Admin Actions:**

1. **Assign Department**
   - [ ] Select department from dropdown (e.g., "Maintenance")
   - [ ] Click "Assign"
   - [ ] Verify success message
   - [ ] Check department badge appears

2. **Update Priority**
   - [ ] Change priority level (e.g., High to Medium)
   - [ ] Click "Update Priority"
   - [ ] Verify success message
   - [ ] Check priority badge updates

3. **Update Status to In Progress**
   - [ ] Select "In Progress" from status dropdown
   - [ ] Add remarks: "Maintenance team has been notified. Work will begin tomorrow."
   - [ ] Click "Update Status"
   - [ ] Verify success message
   - [ ] Check status badge updates
   - [ ] Verify timeline entry added

4. **Update Status to Resolved**
   - [ ] Select "Resolved" from status dropdown
   - [ ] Add remarks: "Projector has been repaired and tested. Issue resolved."
   - [ ] Click "Update Status"
   - [ ] Verify resolved date appears
   - [ ] Check timeline shows resolution

### User Management
- [ ] Navigate to: `http://localhost/SCMS-PHP/admin/users.php`
- [ ] Verify user statistics cards display
- [ ] Check students list/table displays
- [ ] Verify student information shows:
  - Student ID, Name, Email, Phone
  - Complaint count
  - Status (Active/Suspended)
  - Join date
- [ ] Test filters:
  - Search by name, email, or student ID
  - Filter by status (All, Active, Suspended)
  - Sort by (Newest, Oldest, Name, Most Complaints)
- [ ] Test user actions:
  - Click "Suspend" on an active user
  - Verify status changes to "Suspended"
  - Click "Activate" to reactivate
- [ ] Test mobile view

### Admin Logout
- [ ] Click "Logout"
- [ ] Verify redirect to home page
- [ ] Try accessing admin URLs directly (should redirect to login)

---

## 🔄 Phase 5: End-to-End Workflow Test

This tests the complete complaint lifecycle:

### 1. Student Submits Complaint
- [ ] Login as student (john.doe@college.edu / student123)
- [ ] Submit new complaint with all details + attachment
- [ ] Note Complaint ID
- [ ] Logout

### 2. Admin Reviews and Assigns
- [ ] Login as admin (admin@college.edu / admin123)
- [ ] Find the new complaint in admin complaints page
- [ ] Open complaint details
- [ ] Assign to department: "IT Department"
- [ ] Update priority to: "High"
- [ ] Change status to: "In Progress"
- [ ] Add remarks: "IT team is investigating the issue"
- [ ] Logout

### 3. Student Tracks Progress
- [ ] Login as student
- [ ] Go to "Track Complaint"
- [ ] Enter Complaint ID
- [ ] Verify timeline shows:
  - Initial submission
  - Status updated to "In Progress"
  - Admin remarks visible
- [ ] Check department assignment shows
- [ ] Logout

### 4. Admin Resolves Complaint
- [ ] Login as admin
- [ ] Open same complaint in admin panel
- [ ] Change status to: "Resolved"
- [ ] Add final remarks: "Issue fixed and verified"
- [ ] Check resolved_at timestamp appears
- [ ] Logout

### 5. Student Verifies Resolution
- [ ] Login as student
- [ ] View complaint in "My Complaints"
- [ ] Verify status shows "Resolved"
- [ ] Track complaint
- [ ] Check timeline shows complete journey
- [ ] Verify final remarks appear

---

## 📱 Phase 6: Responsive Design Testing

### Mobile Testing (Resize browser to ~375px width)

**Navigation:**
- [ ] Hamburger menu icon appears
- [ ] Click hamburger to open menu
- [ ] Menu slides in from right/overlay
- [ ] Navigation links work
- [ ] Click outside menu to close
- [ ] Menu closes when clicking a link

**Public Pages:**
- [ ] Home page responsive (cards stack vertically)
- [ ] Statistics display properly
- [ ] Category grid responsive
- [ ] Forms are usable on mobile

**Student Portal:**
- [ ] Dashboard cards stack properly
- [ ] Statistics readable
- [ ] Forms are mobile-friendly
- [ ] File upload works
- [ ] Complaints list shows card view (not table)
- [ ] Filter controls stack vertically

**Admin Portal:**
- [ ] Dashboard responsive
- [ ] Complaints show as cards on mobile (not table)
- [ ] Filters stack properly
- [ ] Complaint details readable
- [ ] Action forms usable
- [ ] User list shows cards on mobile

### Tablet Testing (Resize to ~768px)
- [ ] Layout adapts properly
- [ ] Tables remain or switch to cards appropriately
- [ ] Touch targets are adequate size

---

## 🔒 Phase 7: Security Testing

### Authentication
- [ ] Try accessing student pages without login (should redirect)
- [ ] Try accessing admin pages without login (should redirect)
- [ ] Try accessing admin pages as student (should show access denied)
- [ ] Verify logout destroys session properly

### Input Validation
- [ ] Test SQL injection attempts in forms (should be prevented)
- [ ] Test XSS attempts in text fields (should be escaped)
- [ ] Verify file upload restrictions work
- [ ] Test max file size enforcement

### File Upload Security
- [ ] Try uploading PHP file (should be rejected)
- [ ] Try uploading executable (should be rejected)
- [ ] Verify uploaded files are stored securely
- [ ] Check direct access to uploads (should be controlled)

---

## 🎨 Phase 8: UI/UX Testing

### Visual Elements
- [ ] All colors match design system
- [ ] Badges display correctly (status, priority, category)
- [ ] Cards have proper shadows and hover effects
- [ ] Buttons have hover states
- [ ] Forms are visually consistent
- [ ] Spacing is consistent (8px system)

### Interactions
- [ ] Toast notifications appear for actions
- [ ] Alerts can be closed manually
- [ ] Alerts auto-hide after 5 seconds
- [ ] Password visibility toggle works
- [ ] File preview shows for images
- [ ] Form validation shows errors inline
- [ ] Error messages clear when typing

### Typography
- [ ] Inter font loads correctly
- [ ] Text is readable at all sizes
- [ ] Headings have proper hierarchy
- [ ] Line heights are comfortable

---

## 🐛 Phase 9: Error Handling

### Test Error Scenarios
- [ ] Submit form with missing required fields
- [ ] Try invalid email formats
- [ ] Test password mismatch
- [ ] Upload oversized file
- [ ] Upload invalid file type
- [ ] Search for non-existent complaint ID
- [ ] Try invalid login credentials
- [ ] Access invalid URLs (e.g., complaint-details.php?id=INVALID)

### Verify Error Messages
- [ ] Errors display clearly
- [ ] Error messages are helpful
- [ ] No raw PHP errors visible
- [ ] Database errors handled gracefully

---

## ✨ Phase 10: Performance & Polish

### Performance
- [ ] Pages load quickly (< 2 seconds)
- [ ] Images optimized
- [ ] No console errors (press F12 → Console)
- [ ] CSS and JS files load properly

### Browser Compatibility
- [ ] Test in Chrome
- [ ] Test in Firefox
- [ ] Test in Edge
- [ ] Test in Safari (if available)

### Final Polish
- [ ] All links work correctly
- [ ] No broken images
- [ ] No Lorem Ipsum placeholder text
- [ ] Consistent styling across all pages
- [ ] Footer displays on all pages
- [ ] Navigation consistent across modules

---

## 📊 Test Results Summary

### Overall Results
- Total Tests: ~150+
- Passed: ___
- Failed: ___
- Skipped: ___

### Critical Issues Found
1. 
2. 
3. 

### Minor Issues Found
1. 
2. 
3. 

### Recommendations
1. 
2. 
3. 

---

## 🎯 Final Verification Checklist

Before considering testing complete:

- [ ] All user workflows tested end-to-end
- [ ] Both student and admin roles thoroughly tested
- [ ] Responsive design verified on multiple screen sizes
- [ ] Security measures validated
- [ ] Error handling tested
- [ ] Database operations work correctly
- [ ] File uploads work properly
- [ ] All navigation links functional
- [ ] No PHP errors or warnings
- [ ] JavaScript console clean (no errors)
- [ ] System ready for deployment

---

## 📝 Testing Notes

**Date**: _______________
**Tester**: _______________
**Environment**: 
- PHP Version: _______________
- MySQL Version: _______________
- Browser: _______________
- Screen Resolution: _______________

**Additional Notes**:
```
(Add any observations, bugs found, or improvement suggestions)




```

---

**Testing completed successfully! ✅**

For any issues found, refer to `README.md` Troubleshooting section or check Apache/PHP error logs.

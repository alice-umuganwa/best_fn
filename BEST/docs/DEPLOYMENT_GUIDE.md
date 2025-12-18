# Deployment Guide - Disaster Relief Management System

This guide provides step-by-step instructions for deploying the Disaster Relief Management System on XAMPP with phpMyAdmin.

---

## Prerequisites

Before you begin, ensure you have the following installed:

- **XAMPP** (Apache + MySQL + PHP)
  - Download from: https://www.apachefriends.org/
  - Minimum PHP version: 7.4 or higher
  - MySQL version: 5.7 or higher

---

## Installation Steps

### Step 1: Install XAMPP

1. Download XAMPP from the official website
2. Run the installer
3. Select components:
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin
4. Choose installation directory (default: `C:\xampp`)
5. Complete the installation

### Step 2: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Start **Apache** server (click "Start" button)
3. Start **MySQL** server (click "Start" button)
4. Verify both services show "Running" status

> **Note**: If Apache fails to start, check if port 80 is already in use by another application (like Skype or IIS). You can change Apache port in `httpd.conf` if needed.

### Step 3: Copy Project Files

1. Navigate to your XAMPP installation directory
2. Open the `htdocs` folder (default: `C:\xampp\htdocs`)
3. Copy the entire `BEST` project folder into `htdocs`
4. Final path should be: `C:\xampp\htdocs\BEST`

### Step 4: Create Database

#### Option A: Using phpMyAdmin (Recommended)

1. Open your web browser
2. Navigate to: `http://localhost/phpmyadmin`
3. Click on "New" in the left sidebar
4. Enter database name: `disaster_relief`
5. Select collation: `utf8mb4_general_ci`
6. Click "Create"

#### Option B: Using MySQL Command Line

```bash
# Open XAMPP Shell or Command Prompt
cd C:\xampp\mysql\bin
mysql -u root -p

# In MySQL prompt:
CREATE DATABASE disaster_relief CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
EXIT;
```

### Step 5: Import Database Schema

#### Using phpMyAdmin:

1. In phpMyAdmin, select the `disaster_relief` database
2. Click on the "Import" tab
3. Click "Choose File"
4. Navigate to: `C:\xampp\htdocs\BEST\database\database_schema.sql`
5. Click "Go" to import
6. Wait for success message

#### Using MySQL Command Line:

```bash
cd C:\xampp\mysql\bin
mysql -u root -p disaster_relief < C:\xampp\htdocs\BEST\database\database_schema.sql
```

### Step 6: Verify Database Import

1. In phpMyAdmin, select `disaster_relief` database
2. You should see the following tables:
   - users
   - disasters
   - relief_camps
   - resources
   - donations
   - volunteers
   - volunteer_assignments
   - resource_requests
   - reports
   - notifications

3. Click on `users` table and browse data
4. You should see 4 sample users including admin

### Step 7: Configure Application

The default configuration should work with XAMPP. If needed, edit:

**File**: `C:\xampp\htdocs\BEST\config\config.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP has no password
define('DB_NAME', 'disaster_relief');
```

### Step 8: Test the Application

1. Open your web browser
2. Navigate to: `http://localhost/BEST`
3. You should see the landing page

---

## Default Login Credentials

After importing the database, you can log in with these default accounts:

### Admin Account
- **Username**: `admin`
- **Password**: `admin123`
- **Role**: Administrator (full access)

### Staff Account
- **Username**: `staff1`
- **Password**: `admin123`
- **Role**: Staff

### Volunteer Account
- **Username**: `volunteer1`
- **Password**: `admin123`
- **Role**: Volunteer

### Donor Account
- **Username**: `donor1`
- **Password**: `admin123`
- **Role**: Donor

> **⚠️ IMPORTANT**: Change these passwords immediately after first login for security!

---

## Troubleshooting

### Issue: "Database connection failed"

**Solutions**:
1. Verify MySQL service is running in XAMPP Control Panel
2. Check database credentials in `config/config.php`
3. Ensure database `disaster_relief` exists
4. Test connection in phpMyAdmin

### Issue: "Page not found" or 404 errors

**Solutions**:
1. Verify Apache is running
2. Check project is in correct path: `C:\xampp\htdocs\BEST`
3. Access via: `http://localhost/BEST` (not `http://localhost`)
4. Clear browser cache

### Issue: PHP errors displayed

**Solutions**:
1. Check PHP version (minimum 7.4)
2. Enable required PHP extensions in `php.ini`:
   - `extension=pdo_mysql`
   - `extension=mysqli`
   - `extension=mbstring`
3. Restart Apache after changing `php.ini`

### Issue: Blank white page

**Solutions**:
1. Enable error reporting temporarily
2. Check Apache error logs: `C:\xampp\apache\logs\error.log`
3. Verify file permissions
4. Check for PHP syntax errors

### Issue: CSS/JavaScript not loading

**Solutions**:
1. Check file paths in HTML
2. Verify `assets` folder exists
3. Clear browser cache (Ctrl + F5)
4. Check browser console for errors

---

## File Permissions

On Windows with XAMPP, file permissions are usually not an issue. However, ensure:

1. The `uploads` folder (if created) has write permissions
2. Log files can be written (if logging is enabled)

---

## Security Recommendations

### For Production Deployment:

1. **Change Default Passwords**
   - Change all default user passwords
   - Set strong MySQL root password

2. **Update Configuration**
   ```php
   // In config/config.php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

3. **Enable HTTPS**
   - Configure SSL certificate in Apache
   - Force HTTPS redirects

4. **Database Security**
   - Create dedicated MySQL user (not root)
   - Grant only necessary privileges
   ```sql
   CREATE USER 'drms_user'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON disaster_relief.* TO 'drms_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

5. **File Security**
   - Move sensitive files outside web root
   - Add `.htaccess` protection
   - Disable directory listing

6. **Regular Backups**
   - Backup database regularly
   - Backup uploaded files
   - Store backups securely

---

## Testing the System

### 1. Test User Registration
1. Go to: `http://localhost/BEST/views/auth/register.php`
2. Create a new account
3. Verify account appears in database

### 2. Test Login
1. Go to: `http://localhost/BEST/views/auth/login.php`
2. Login with admin credentials
3. Verify redirect to dashboard

### 3. Test Database Operations
1. Login as admin
2. Try creating a new disaster
3. Verify data appears in database
4. Test update and delete operations

### 4. Test Donations
1. Go to donation page
2. Submit a test donation
3. Verify in database

---

## Performance Optimization

### For Better Performance:

1. **Enable PHP OPcache**
   - Edit `php.ini`
   - Enable `opcache.enable=1`
   - Restart Apache

2. **MySQL Optimization**
   - Increase `innodb_buffer_pool_size` in `my.ini`
   - Enable query cache if needed

3. **Apache Optimization**
   - Enable gzip compression
   - Enable browser caching
   - Configure `KeepAlive` settings

---

## Updating the Application

To update the application:

1. Backup current database
   ```bash
   mysqldump -u root -p disaster_relief > backup.sql
   ```

2. Backup current files
   - Copy `BEST` folder to safe location

3. Apply updates
   - Replace files
   - Run any new SQL migrations

4. Test thoroughly before going live

---

## Uninstallation

To remove the application:

1. Stop Apache and MySQL in XAMPP
2. Delete project folder: `C:\xampp\htdocs\BEST`
3. Drop database in phpMyAdmin:
   ```sql
   DROP DATABASE disaster_relief;
   ```

---

## Additional Resources

- **XAMPP Documentation**: https://www.apachefriends.org/docs/
- **PHP Manual**: https://www.php.net/manual/
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **phpMyAdmin Documentation**: https://docs.phpmyadmin.net/

---

## Support

For issues or questions:
1. Check error logs in `C:\xampp\apache\logs\`
2. Review this deployment guide
3. Check database connection settings
4. Verify all services are running

---

## Quick Reference Commands

### Start/Stop Services (XAMPP Control Panel)
- Start Apache: Click "Start" next to Apache
- Stop Apache: Click "Stop" next to Apache
- Start MySQL: Click "Start" next to MySQL
- Stop MySQL: Click "Stop" next to MySQL

### Access Points
- **Application**: http://localhost/BEST
- **phpMyAdmin**: http://localhost/phpmyadmin
- **XAMPP Dashboard**: http://localhost

### Important Paths
- **Project Root**: `C:\xampp\htdocs\BEST`
- **Apache Config**: `C:\xampp\apache\conf\httpd.conf`
- **PHP Config**: `C:\xampp\php\php.ini`
- **MySQL Config**: `C:\xampp\mysql\bin\my.ini`
- **Error Logs**: `C:\xampp\apache\logs\error.log`

---

**Deployment Complete!** 🎉

Your Disaster Relief Management System should now be running successfully on XAMPP.

# Disaster Relief Management System

A comprehensive web-based platform for managing disaster relief operations, coordinating volunteers, tracking resources, and facilitating donations to help communities in crisis.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

---

## 📋 Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [UML Diagrams](#uml-diagrams)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

---

## ✨ Features

### Core Functionality

- **🚨 Disaster Management**: Track and manage disaster events with detailed information
- **🏕️ Relief Camp Coordination**: Manage relief camp locations, capacity, and operations
- **📦 Resource Inventory**: Track food, medicine, shelter materials, and supplies
- **💰 Donation Portal**: Secure platform for monetary and material donations
- **🤝 Volunteer Management**: Register, verify, and assign volunteers to camps
- **📊 Analytics Dashboard**: Comprehensive reports and real-time statistics
- **👥 Multi-Role Access**: Admin, Staff, Volunteer, and Donor roles
- **🔔 Notifications**: Real-time alerts for critical events and updates

### Technical Features

- **MVC Architecture**: Clean separation of concerns
- **Responsive Design**: Mobile-first, works on all devices
- **Secure Authentication**: Password hashing, session management
- **SQL Injection Protection**: Prepared statements with PDO
- **Modern UI/UX**: Beautiful, intuitive interface with smooth animations
- **RESTful API Ready**: JSON responses for AJAX operations

---

## 🛠️ Technology Stack

### Backend
- **PHP 7.4+**: Server-side programming
- **MySQL 5.7+**: Relational database
- **PDO**: Database abstraction layer

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with CSS Grid and Flexbox
- **JavaScript (ES6+)**: Interactive functionality
- **AJAX**: Asynchronous data operations

### Development Tools
- **XAMPP**: Local development environment
- **phpMyAdmin**: Database management
- **Git**: Version control

---

## 💻 System Requirements

- **Web Server**: Apache 2.4+
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Browser**: Modern browser (Chrome, Firefox, Edge, Safari)
- **RAM**: Minimum 2GB
- **Disk Space**: 500MB free space

---

## 🚀 Installation

### Quick Start with XAMPP

1. **Install XAMPP**
   ```
   Download from: https://www.apachefriends.org/
   Install with Apache, MySQL, PHP, and phpMyAdmin
   ```

2. **Clone/Copy Project**
   ```bash
   # Copy project to XAMPP htdocs
   C:\xampp\htdocs\BEST
   ```

3. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create database: `disaster_relief`
   - Import: `database/database_schema.sql`

4. **Configure Application**
   - Edit `config/config.php` if needed
   - Default settings work with XAMPP

5. **Access Application**
   ```
   http://localhost/BEST
   ```

### Default Login Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| Staff | staff1 | admin123 |
| Volunteer | volunteer1 | admin123 |
| Donor | donor1 | admin123 |

> ⚠️ **Change passwords immediately after first login!**

For detailed installation instructions, see [DEPLOYMENT_GUIDE.md](docs/DEPLOYMENT_GUIDE.md)

---

## 📖 Usage

### For Administrators

1. **Login** with admin credentials
2. **Manage Disasters**: Create and track disaster events
3. **Setup Relief Camps**: Establish camps with capacity and location
4. **Allocate Resources**: Track and distribute supplies
5. **Assign Volunteers**: Match volunteers to camps
6. **Generate Reports**: View analytics and statistics

### For Donors

1. **Register/Login** as a donor
2. **View Active Disasters**: See current relief efforts
3. **Make Donations**: Contribute money or materials
4. **Track Contributions**: View donation history

### For Volunteers

1. **Register** as a volunteer
2. **Complete Profile**: Add skills and availability
3. **Wait for Verification**: Admin approves volunteers
4. **View Assignments**: Check camp assignments
5. **Log Hours**: Track volunteer time

---

## 📁 Project Structure

```
BEST/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   ├── main.js            # Core JavaScript
│   │   └── dashboard.js       # Dashboard scripts
│   └── images/                # Image assets
├── config/
│   ├── config.php             # Application configuration
│   └── Database.php           # Database connection class
├── controllers/
│   ├── AuthController.php     # Authentication logic
│   ├── DisasterController.php # Disaster management
│   ├── CampController.php     # Camp operations
│   ├── DonationController.php # Donation processing
│   └── VolunteerController.php# Volunteer management
├── models/
│   ├── User.php               # User model
│   ├── Disaster.php           # Disaster model
│   ├── ReliefCamp.php         # Camp model
│   ├── Donation.php           # Donation model
│   └── Volunteer.php          # Volunteer model
├── views/
│   ├── auth/
│   │   ├── login.php          # Login page
│   │   └── register.php       # Registration page
│   ├── admin/
│   │   ├── dashboard.php      # Admin dashboard
│   │   ├── manage_disasters.php
│   │   ├── manage_camps.php
│   │   └── manage_resources.php
│   └── volunteer/
│       └── dashboard.php      # Volunteer dashboard
├── database/
│   └── database_schema.sql    # Database structure
├── docs/
│   ├── UML_DIAGRAMS.md        # System diagrams
│   ├── DEPLOYMENT_GUIDE.md    # Installation guide
│   └── TESTING_PLAN.md        # Test cases
├── index.php                  # Landing page
└── README.md                  # This file
```

---

## 🗄️ Database Schema

The system uses 11 main tables:

- **users**: User accounts and authentication
- **disasters**: Disaster event records
- **relief_camps**: Relief camp information
- **resources**: Inventory tracking
- **donations**: Donation records
- **volunteers**: Volunteer profiles
- **volunteer_assignments**: Camp assignments
- **resource_requests**: Supply requests
- **reports**: Generated reports
- **notifications**: System notifications

See [database_schema.sql](database/database_schema.sql) for complete structure.

---

## 📊 UML Diagrams

Comprehensive UML diagrams are available in [docs/UML_DIAGRAMS.md](docs/UML_DIAGRAMS.md):

- **Use Case Diagram**: System interactions
- **Class Diagram**: Object-oriented structure
- **Activity Diagram**: Disaster response workflow
- **Data Flow Diagram**: Information flow
- **Sequence Diagrams**: Process interactions
- **Entity-Relationship Diagram**: Database relationships

---

## 🧪 Testing

### Manual Testing

1. **User Registration**: Create new accounts
2. **Login/Logout**: Test authentication
3. **CRUD Operations**: Create, read, update, delete records
4. **Form Validation**: Test input validation
5. **Role-Based Access**: Verify permissions
6. **Responsive Design**: Test on different devices

### Test Accounts

Use the default credentials to test different user roles and permissions.

For detailed test cases, see [docs/TESTING_PLAN.md](docs/TESTING_PLAN.md)

---

## 🔒 Security

### Implemented Security Measures

- ✅ **Password Hashing**: bcrypt with cost factor 10
- ✅ **SQL Injection Protection**: PDO prepared statements
- ✅ **XSS Prevention**: Input sanitization and output escaping
- ✅ **Session Security**: Secure session management
- ✅ **CSRF Protection**: Token-based validation (recommended)
- ✅ **Role-Based Access Control**: Permission checking

### Security Best Practices

1. Change default passwords immediately
2. Use HTTPS in production
3. Keep PHP and MySQL updated
4. Regular database backups
5. Implement rate limiting for login attempts
6. Enable error logging (disable display in production)

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 for PHP code
- Use meaningful variable and function names
- Comment complex logic
- Write clean, readable code

---

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## 👥 Authors

- **Development Team**: Disaster Relief Management System
- **Course**: Final Exam Project
- **Year**: 2024

---

## 📞 Support

For support and questions:

- 📧 Email: info@drms.org
- 📖 Documentation: See `docs/` folder
- 🐛 Bug Reports: Create an issue

---

## 🙏 Acknowledgments

- XAMPP for the development environment
- PHP and MySQL communities
- All contributors and testers

---

## 🗺️ Roadmap

### Future Enhancements

- [ ] Real-time notifications with WebSockets
- [ ] Mobile app (iOS/Android)
- [ ] SMS alerts for critical events
- [ ] Payment gateway integration
- [ ] Multi-language support
- [ ] Advanced analytics with charts
- [ ] API documentation
- [ ] Docker containerization
- [ ] Automated testing suite

---

## 📸 Screenshots

### Landing Page
Modern, responsive landing page with active disasters and statistics.

### Admin Dashboard
Comprehensive dashboard with analytics, charts, and quick actions.

### Donation Portal
Secure donation form with disaster selection and payment options.

---

**Made with ❤️ for disaster relief efforts**

---

## Quick Links

- [Installation Guide](docs/DEPLOYMENT_GUIDE.md)
- [UML Diagrams](docs/UML_DIAGRAMS.md)
- [Database Schema](database/database_schema.sql)
- [Testing Plan](docs/TESTING_PLAN.md)

---

**Version**: 1.0.0  
**Last Updated**: December 2024  
**Status**: Production Ready ✅

# JeevanDaan - Mohammed Aadil's Contributions

## Assigned Tasks

| Task ID | Task Name | Status |
|---------|-----------|--------|
| LBDM-180 | Verification System | ✅ Complete |
| LBDM-179 | Red Cross Management | ✅ Complete |
| LBDM-176 | Logout (Red Cross) | ✅ Complete |
| LBDM-159 | Location Validation | ✅ Complete |
| LBDM-156 | Email Validation | ✅ Complete |
| LBDM-158 | Date Validation | ✅ Complete |

## Features Implemented

### LBDM-180: Verification System
- Admin can verify users by reviewing citizenship documents
- Admin can verify organizations
- Approve/Reject with reason notification to user
- Verified badge shown on profile

### LBDM-179: Red Cross Management
- Organization personnel profile management
- View all verified donors with filtering by blood group and district
- View blood requests with contact details
- Receive notifications for new donors

### LBDM-176: Logout (Red Cross)
- Secure logout for organization users
- Session cleanup
- Redirect to home page after logout

### LBDM-159: Location Validation
- Rejects only numeric values in location field
- Real-time client-side validation
- Server-side backup validation

### LBDM-156: Email Validation
- Email format validation
- Duplicate email check on registration
- Applied to user and organization signup

### LBDM-158: Date Validation
- Blood donation: only today and future dates allowed
- Blood requests: past dates blocked
- Client and server-side validation

## Setup Instructions

1. Copy config: `cp app/config/config.example.php app/config/config.php`
2. Update credentials in `config.php`
3. Import database: `mysql -u root -p < database/jeevandaan.sql`
4. Install dependencies: `composer install`
5. Configure Google OAuth and Gmail SMTP

## Tech Stack
- PHP 8+ (MVC Architecture)
- MySQL (MySQLi)
- HTML5, CSS3, JavaScript
- Google OAuth 2.0
- PHPMailer (Gmail SMTP)

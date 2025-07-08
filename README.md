# Auction platform system

## Overview
The Auction System is a web-based platform for managing cricket player auctions. It provides an administrative interface for managing players and teams, conducting live auctions, and tracking budgets. A separate user display module allows real-time viewing of auction progress.

## Directory Structure
```
auction/
├── Admin/
│   ├── assits/           # Player and team images
│   ├── css/              # Stylesheets for admin interface
│   ├── pages/            # Admin HTML pages (except index.html)
│   ├── php/              # Backend PHP scripts
│   └── index.html        # Admin dashboard
├── userdispaly/
│   ├── css/              # Stylesheets for user display
│   ├── php/              # PHP scripts for user display
│   └── display.html      # Main user display page
├── vendor/               # Composer dependencies
├── composer.json         # Composer configuration
└── README.md             # Project documentation
```

## Features
- Player and team management (add, update, delete)
- Real-time auction dashboard
- Budget tracking for teams
- Downloadable auction results (Excel)
- Database reset and clearing tools
- User-facing auction display

## Installation
1. **Requirements:**
   - PHP 7.2 or higher
   - MySQL
   - Composer
   - Web server (Apache, Nginx, XAMPP, WAMP, etc.)

2. **Dependency Installation:**
   - Run `composer install` in the project root to install PHP dependencies.

3. **Database Configuration:**
   - Import the SQL schema to create the required tables (`players`, `teams`, etc.).
   - Update database credentials in `Admin/php/db.php`.

4. **File Placement:**
   - Place all admin HTML files (except `index.html`) in `Admin/pages/`.
   - Keep all PHP scripts in `Admin/php/`.
   - User display files are located in `userdispaly/`.

# Polyprep Educational Website

A mobile-first educational platform for sharing and managing course notes.

## Features

- 🔐 User Authentication
- 👤 User Dashboard for browsing and uploading notes
- 🛠️ Admin Dashboard for content management
- 📁 PDF file handling with approval system
- 📱 Mobile-optimized responsive design

## Technical Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Server**: Raspberry Pi 4 Model B

## Installation

1. Clone this repository to your Raspberry Pi's web server directory
2. Import the `database/polyprep.sql` file into your MySQL database
3. Configure database connection in `config/database.php`
4. Ensure the `uploads` directory has write permissions
5. Access the website through your browser

## Directory Structure

- `assets/` - CSS, JS, and image files
- `config/` - Configuration files
- `database/` - Database schema and setup
- `includes/` - Reusable PHP components
- `uploads/` - Directory for storing uploaded PDF files
- `views/` - PHP view files for different pages

# Database Directory

This directory contains the SQL scripts and migration tools for the Digital School Report Card Management System.

## Files
- `school_report_db.sql`: The main SQL schema and sample data. This file can be imported directly into phpMyAdmin or via the command line.
- `migrate.php`: A PHP script to automatically create the database and tables from `school_report_db.sql`.

## How to Import
### Via PHP Script (Recommended)
Run the following command in your terminal:
```bash
php database/migrate.php
```

### Via phpMyAdmin
1. Open phpMyAdmin.
2. Go to the **Import** tab.
3. Choose the `school_report_db.sql` file.
4. Click **Go**.

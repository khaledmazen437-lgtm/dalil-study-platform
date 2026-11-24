# Dalil — دليل (Study Platform)

> منصة "دليل" الدراسية — كورسات، كويزات، ملاحظات، وملخصات أسبوعية، مع لوحة تحكم أدمن.

An Arabic (RTL) study platform for students: courses, quizzes with a timer, notes, weekly resources, progress tracking, and an admin dashboard.

## Features

- Course pages, timed quizzes, notes and weekly resources
- Student progress tracking
- Admin panel for content management
- PWA support (`sw.js`)

## Tech Stack

- PHP 8+ (no framework), MySQL via PDO, vanilla JS front-end

## Setup

1. Create a MySQL database (default name `dalil_db`) and import `setup.sql`.
2. Configure the app through environment variables (never commit credentials):

   | Variable | Purpose |
   |---|---|
   | `DALIL_DB_HOST` | DB host (default `localhost`) |
   | `DALIL_DB_NAME` | DB name (default `dalil_db`) |
   | `DALIL_DB_USER` | DB user (**required**) |
   | `DALIL_DB_PASS` | DB password (**required**) |
   | `DALIL_ADMIN_USER` | Admin username (default `admin`) |
   | `DALIL_ADMIN_SALT` | Admin password salt (**required**) |
   | `DALIL_ADMIN_PASSWORD` | Admin password (**required**) |
   | `DALIL_BASE_PATH` | URL base path (default `/dalil`) |

3. Deploy on any PHP host (Apache/Nginx + PHP-FPM) with `config.php` reachable by the app.

## Security Notes

- Database and admin credentials were previously committed to this repo. They have been rotated and removed from the code; the app now reads them from environment variables only.
- Database errors no longer leak internal details to the browser.

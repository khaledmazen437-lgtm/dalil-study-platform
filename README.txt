دليل (Dalil) — Educational Study Platform

> منصة تعليمية متكاملة لطالب الحاسبات: كورسات، امتحانات، فلاش كاردز، حساب GPA، مؤقت تركيز، وملاحظات — كلها في مكان واحد.

A full-stack PHP/MySQL study platform for computer-science students, with an admin panel and a mobile-friendly UI.

## Features

- Courses, materials and resources pages
- Exams and quizzes with a JSON-driven question bank
- Flashcards with an admin-editable deck (`admin/flashcards.json`)
- GPA calculator, weekly planner and progress tracking
- Focus timer and notes
- Admin panel (under `/mhd-admin`) for content management
- PWA basics: `manifest.json` and a service worker (`sw.js`)

## Tech Stack

- PHP 8+ (no framework), MySQL/MariaDB
- Vanilla HTML/CSS/JS (`assets/style.css`, `assets/main.js`)
- Apache with `mod_rewrite` (`.htaccess` included)

## Project Structure

```text
├── index.php          # Home
├── pages/             # courses, exams, flashcards, gpa, focus, notes, quiz...
├── admin/             # Admin panel + editable JSON content
├── includes/          # db.php, header, footer, navbar
├── assets/            # CSS, JS, images
├── config.php         # DB + site settings
├── setup.sql          # Database schema and seed data
└── api.php            # JSON API endpoints
```

## Setup (XAMPP)

1. Copy files to `C:\xampp\htdocs\dalil\`
2. Create a MySQL database `dalil_db` and import `setup.sql` via phpMyAdmin
3. Edit `config.php` (DB credentials + `BASE_PATH`)
4. Open `http://localhost/dalil/`

> ⚠️ Security: never commit real database or admin credentials. Keep secrets in environment variables or an untracked config file, and rotate any credential that was ever committed.



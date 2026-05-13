# Oshanda Geethanjana Portfolio (PHP + MySQL + AJAX)

Production-ready personal portfolio system built for XAMPP using pure PHP, MySQL, JavaScript, AJAX, and HTML5 (no framework build tools).

## Overview

This project includes:
- Premium Apple-inspired white glassmorphism UI
- Public pages: Home, About, Education, Works, Blogs, Courses, Contact
- AJAX-powered dynamic sections (Works, Education, Courses)
- Medium blog feed integration via server-side RSS parsing
- Contact form with AJAX, honeypot anti-spam, validation, database storage
- Simple admin panel for managing works, education, courses, and messages
- Reusable includes (config, DB, helpers, SEO, header, footer)
- SEO setup (meta tags, OG/Twitter tags, schema markup, robots, sitemap)

## Stack

- PHP 8+
- MySQL
- JavaScript (Fetch API)
- HTML5 + CSS3
- Three.js + GLTFLoader (CDN)
- Font Awesome (CDN)

## Project Structure

```text
portfolio-php/
  index.php
  about.php
  education.php
  works.php
  blogs.php
  courses.php
  contact.php

  includes/
    config.php
    db.php
    functions.php
    seo.php
    header.php
    footer.php

  api/
    get_works.php
    get_education.php
    get_courses.php
    get_medium_posts.php
    submit_contact.php

  admin/
    index.php
    login.php
    logout.php
    dashboard.php
    works.php
    education.php
    courses.php
    messages.php
    save_work.php
    save_education.php
    save_course.php
    delete_item.php

  assets/
    images/
    icons/
    uploads/

  models/
    character.glb
    education.glb
    works.glb
    courses.glb
    contact.glb

  database/
    portfolio.sql

  partials/
    home-hero.php
    home-featured-works.php
    home-featured-courses.php
    home-education-preview.php
    home-blog-preview.php
    home-contact-cta.php

  css/
  js/
  robots.txt
  sitemap.xml
  manifest.json
  .htaccess
```

## Setup (XAMPP)

1. Copy folder to: `C:\xampp\htdocs\portfolio\portfolio-php`
2. Start **Apache** and **MySQL** from XAMPP control panel.
3. Open phpMyAdmin: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
4. Import SQL file: `database/portfolio.sql`
5. Edit DB credentials in `includes/config.php` if needed:
   - `database.host`
   - `database.port`
   - `database.name`
   - `database.username`
   - `database.password`
6. Open site: [http://localhost/portfolio/portfolio-php/index.php](http://localhost/portfolio/portfolio-php/index.php)

## Default Admin Login

- URL: [http://localhost/portfolio/portfolio-php/admin/login.php](http://localhost/portfolio/portfolio-php/admin/login.php)
- Username: `admin`
- Password: `Admin@123`

> Change this password immediately in production.

## Personal Data Configuration

Edit `includes/config.php`:
- Site identity (`site.name`, `site.brand_title`, `site.hero_tagline`)
- Email and WhatsApp (`contact` section)
- Social links (`social` section)
- Medium feed username/url (`medium` section)
- Availability and location (`site.availability`, `site.location`)

## Medium Fetching Logic

- Endpoint: `api/get_medium_posts.php`
- Uses server-side RSS fetch from: `https://medium.com/feed/@oshandageethanjana`
- Parses XML safely using `simplexml_load_string` with safe flags
- Extracts title, URL, image (if found), excerpt, publish date, reading time
- Frontend loads posts asynchronously via AJAX on Home and Blogs pages

## 3D Model Setup

- Place valid `.glb` files in `/models` with these names:
  - `character.glb`
  - `education.glb`
  - `works.glb`
  - `courses.glb`
  - `contact.glb`
- Current files are placeholders. Replace them with real GLB models.
- If a model fails to load or WebGL is unavailable, pages continue working with fallback behavior.

## Security Notes

- Prepared statements everywhere for DB operations
- Output escaping helper for rendered HTML
- CSRF token validation for admin and contact submission
- Session hardening (cookie flags + regenerate ID)
- Honeypot anti-spam field on contact form
- URL/email/server validation on input handlers

## Troubleshooting

1. **Database connection error**
   - Check credentials in `includes/config.php`
   - Ensure MySQL is running and `portfolio_db` exists

2. **Medium posts not loading**
   - Verify internet access from PHP environment
   - Check `allow_url_fopen` in PHP settings
   - Ensure Medium feed URL is reachable

3. **3D model not visible**
   - Replace placeholder `.glb` files with valid models
   - Open browser console for GLTF errors

4. **Admin login fails**
   - Re-import `database/portfolio.sql`
   - Confirm default credentials above

5. **404 for clean URLs**
   - Ensure Apache `mod_rewrite` is enabled
   - Keep `.htaccess` in project root

## Production Notes

- Update `site.base_url` in `includes/config.php` to your live domain
- Use HTTPS in production
- Rotate admin password
- Replace all placeholder assets and models

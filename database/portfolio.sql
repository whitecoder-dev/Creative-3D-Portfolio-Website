-- Portfolio Website Database
-- Generated for XAMPP / MySQL 8+

CREATE DATABASE IF NOT EXISTS `portfolio_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_db`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `education`;
DROP TABLE IF EXISTS `works`;
DROP TABLE IF EXISTS `admin_users`;

CREATE TABLE `works` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `image_url` VARCHAR(500) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `short_description` TEXT NOT NULL,
  `live_demo_url` VARCHAR(500) NOT NULL,
  `code_url` VARCHAR(500) NULL,
  `is_premium` TINYINT(1) DEFAULT 0,
  `category` VARCHAR(100) DEFAULT 'General',
  `is_featured` TINYINT(1) DEFAULT 0,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `education` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `image_url` VARCHAR(500) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `short_description` TEXT NOT NULL,
  `official_site_url` VARCHAR(500) NOT NULL,
  `provider` VARCHAR(150) NULL,
  `type` VARCHAR(100) NULL,
  `issue_date` DATE NULL,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `image_url` VARCHAR(500) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `short_description` TEXT NOT NULL,
  `provider` VARCHAR(150) NULL,
  `category` VARCHAR(100) NULL,
  `level` VARCHAR(100) NULL,
  `duration` VARCHAR(100) NULL,
  `course_type` ENUM('free','premium') DEFAULT 'free',
  `access_url` VARCHAR(500) NULL,
  `official_site_url` VARCHAR(500) NULL,
  `price_label` VARCHAR(100) NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(200) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `project_type` VARCHAR(150) NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `works` (`image_url`, `name`, `short_description`, `live_demo_url`, `code_url`, `is_premium`, `category`, `is_featured`, `display_order`) VALUES
('https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80', 'WhiteCoder Client Portal', 'A modern client management dashboard with analytics, project tracking, and role-based controls.', 'https://example.com/live/client-portal', 'https://github.com/oshandageethanjana/whitecoder-client-portal', 0, 'Web App', 1, 1),
('https://images.unsplash.com/photo-1518773553398-650c184e0bb3?auto=format&fit=crop&w=1200&q=80', 'HND Study Hub Platform', 'Educational platform for HNDIT students with resource sharing, class modules, and mentorship tools.', 'https://example.com/live/hnd-study-hub', NULL, 1, 'Education', 1, 2),
('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80', 'AI Content Workflow System', 'Automation tool for generating and managing structured educational content using AI assistance.', 'https://example.com/live/ai-content-workflow', 'https://github.com/oshandageethanjana/ai-content-workflow', 0, 'AI', 0, 3),
('https://images.unsplash.com/photo-1558655146-9f40138edfeb?auto=format&fit=crop&w=1200&q=80', 'Portfolio Suite Template', 'Premium Apple-inspired portfolio template with reusable blocks and dynamic CMS-ready architecture.', 'https://example.com/live/portfolio-suite', 'https://github.com/oshandageethanjana/portfolio-suite', 0, 'UI/UX', 0, 4),
('https://images.unsplash.com/photo-1551650975-87deedd944c3?auto=format&fit=crop&w=1200&q=80', 'Freelance Operations Stack', 'Private business workflow system for task, lead, and service pipeline management.', 'https://example.com/live/freelance-stack', NULL, 1, 'Productivity', 0, 5);

INSERT INTO `education` (`image_url`, `name`, `short_description`, `official_site_url`, `provider`, `type`, `issue_date`, `display_order`) VALUES
('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80', 'Higher National Diploma in Information Technology (HNDIT)', 'Formal higher education in software engineering, systems analysis, and IT solution development.', 'https://sliate.ac.lk', 'SLIATE', 'formal', '2022-01-15', 1),
('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80', 'LinkedIn Learning: Full-Stack Foundations', 'Completed structured learning path on full-stack development architecture and best practices.', 'https://www.linkedin.com/learning/', 'LinkedIn Learning', 'certificate', '2024-05-10', 2),
('https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1200&q=80', 'Microsoft Learn: AI Fundamentals', 'Practical pathway for AI concepts, machine learning basics, and cloud-first AI service usage.', 'https://learn.microsoft.com/', 'Microsoft Learn', 'platform', '2024-11-03', 3),
('https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80', 'GitHub Skills: Collaboration and CI', 'Hands-on exercises for repository collaboration, pull requests, and automation workflow setup.', 'https://skills.github.com/', 'GitHub', 'course', '2023-08-25', 4),
('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1200&q=80', 'Applied Web Security Bootcamp', 'Focused training on secure coding, OWASP basics, and defensive web architecture.', 'https://www.coursera.org/', 'Coursera', 'bootcamp', '2025-02-15', 5);

INSERT INTO `courses` (`image_url`, `title`, `short_description`, `provider`, `category`, `level`, `duration`, `course_type`, `access_url`, `official_site_url`, `price_label`, `is_featured`, `display_order`) VALUES
('https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80', 'PHP 8 Production Fundamentals', 'Learn modern PHP architecture, routing, security essentials, and robust CRUD patterns.', 'WhiteCoder', 'Web Development', 'Intermediate', '8 Hours', 'free', 'https://example.com/course/php8-fundamentals', 'https://example.com/courses', 'Free', 1, 1),
('https://images.unsplash.com/photo-1522252234503-e356532cafd5?auto=format&fit=crop&w=1200&q=80', 'AI Integration for Web Products', 'Integrate practical AI workflows into real-world web apps with clean system design.', 'WhiteCoder', 'Artificial Intelligence', 'Intermediate', '6 Weeks', 'premium', 'https://example.com/course/ai-integration', 'https://example.com/courses/ai-integration', 'LKR 12,500', 1, 2),
('https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?auto=format&fit=crop&w=1200&q=80', 'Frontend Precision Design System', 'Craft premium UI systems with clean hierarchy, spacing, and accessible interactions.', 'HND Study Hub', 'UI/UX', 'Beginner', '5 Hours', 'free', 'https://example.com/course/frontend-precision', 'https://example.com/courses/frontend-precision', 'Free', 0, 3),
('https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80', 'Secure MySQL and API Engineering', 'Build secure database-backed APIs with prepared statements, validation, and performance tuning.', 'WhiteCoder', 'Backend', 'Advanced', '4 Weeks', 'premium', 'https://example.com/course/mysql-api-engineering', 'https://example.com/courses/mysql-api-engineering', 'LKR 9,900', 0, 4),
('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=80', 'Freelancer Project Delivery Blueprint', 'End-to-end framework for planning, delivery, and communication in paid digital projects.', 'WhiteCoder', 'Career', 'All Levels', '3 Hours', 'free', 'https://example.com/course/freelancer-blueprint', 'https://example.com/courses/freelancer-blueprint', 'Free', 0, 5);

INSERT INTO `admin_users` (`username`, `password_hash`) VALUES
('admin', '$2y$10$IOiCcDQLjKFFBRUhW/y0bOnIazQH9MAceygAV8aOm3wKXqC0LN/Ie');

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
--  دليل — منصة مازن خالد | كلية الحاسبات والمعلومات
--  ملف إنشاء وتهيئة قاعدة البيانات MySQL
--  قم بتشغيل هذا الملف مرة واحدة فقط عند التثبيت
--  الإصدار 2.0 — بيانات شاملة لقسم الحاسبات
-- =====================================================

CREATE DATABASE IF NOT EXISTS `dalil_db`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `dalil_db`;

-- ══════════════════════════════════════════════════
--  إنشاء الجداول
-- ══════════════════════════════════════════════════

DROP TABLE IF EXISTS `materials`;
DROP TABLE IF EXISTS `weekly_summaries`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `past_exams`;
DROP TABLE IF EXISTS `important_links`;
DROP TABLE IF EXISTS `news`;
DROP TABLE IF EXISTS `ticker_messages`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `admins`;

-- ── المواد الدراسية ──────────────────────────────────
CREATE TABLE `courses` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(255) NOT NULL,
  `code`        VARCHAR(50)  DEFAULT NULL,
  `description` TEXT         DEFAULT NULL,
  `icon`        VARCHAR(10)  DEFAULT NULL,
  `color`       VARCHAR(20)  DEFAULT NULL,
  `semester`    VARCHAR(50)  DEFAULT NULL,
  `year`        VARCHAR(20)  DEFAULT NULL,
  `sort_order`  INT          DEFAULT 0,
  `is_active`   TINYINT(1)   DEFAULT 1,
  `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── المحتوى/الروابط ──────────────────────────────────
CREATE TABLE `materials` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `course_id`   INT          NOT NULL,
  `title`       VARCHAR(255) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `url`         TEXT         NOT NULL,
  `type`        ENUM('summary','slides','video','recording','weekly_summary','question','exam','other') DEFAULT 'other',
  `sort_order`  INT          DEFAULT 0,
  `is_active`   TINYINT(1)   DEFAULT 1,
  `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── ملخصات الأسبوع ───────────────────────────────────
CREATE TABLE `weekly_summaries` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `course_id`    INT          DEFAULT NULL,
  `week_number`  INT          NOT NULL,
  `week_title`   VARCHAR(255) DEFAULT NULL,
  `description`  TEXT         DEFAULT NULL,
  `telegram_url` TEXT         NOT NULL,
  `is_active`    TINYINT(1)   DEFAULT 1,
  `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── بنك الأسئلة ──────────────────────────────────────
CREATE TABLE `questions` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `course_id`    INT          DEFAULT NULL,
  `title`        VARCHAR(255) NOT NULL,
  `description`  TEXT         DEFAULT NULL,
  `type`         ENUM('mcq','essay','mixed','sheet') DEFAULT 'mixed',
  `file_url`     TEXT         DEFAULT NULL,
  `telegram_url` TEXT         DEFAULT NULL,
  `is_active`    TINYINT(1)   DEFAULT 1,
  `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── الامتحانات السابقة ───────────────────────────────
CREATE TABLE `past_exams` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `course_id`    INT          DEFAULT NULL,
  `title`        VARCHAR(255) NOT NULL,
  `description`  TEXT         DEFAULT NULL,
  `exam_type`    ENUM('final','midterm','quiz','assignment') DEFAULT 'final',
  `year`         INT          DEFAULT NULL,
  `semester`     VARCHAR(50)  DEFAULT NULL,
  `file_url`     TEXT         DEFAULT NULL,
  `solution_url` TEXT         DEFAULT NULL,
  `telegram_url` TEXT         DEFAULT NULL,
  `is_active`    TINYINT(1)   DEFAULT 1,
  `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── الأخبار والإعلانات ───────────────────────────────
CREATE TABLE `news` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `title`      VARCHAR(255) NOT NULL,
  `content`    TEXT         DEFAULT NULL,
  `type`       ENUM('news','announcement','important') DEFAULT 'news',
  `is_ticker`  TINYINT(1)   DEFAULT 0,
  `is_active`  TINYINT(1)   DEFAULT 1,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── شريط الأخبار ─────────────────────────────────────
CREATE TABLE `ticker_messages` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `message`    TEXT         NOT NULL,
  `sort_order` INT          DEFAULT 0,
  `is_active`  TINYINT(1)   DEFAULT 1,
  `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── الروابط المهمة ───────────────────────────────────
CREATE TABLE `important_links` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(255) NOT NULL,
  `url`         TEXT         NOT NULL,
  `icon`        VARCHAR(10)  DEFAULT NULL,
  `type`        VARCHAR(50)  DEFAULT 'other',
  `description` TEXT         DEFAULT NULL,
  `sort_order`  INT          DEFAULT 0,
  `is_active`   TINYINT(1)   DEFAULT 1,
  `created_at`  DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── المديرين ─────────────────────────────────────────
CREATE TABLE `admins` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `name`          VARCHAR(255) DEFAULT NULL,
  `created_at`    DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ══════════════════════════════════════════════════
--  البيانات الأولية (Seed Data)
-- ══════════════════════════════════════════════════

-- ─── المدير — admin / admin123 ──────────────────────
-- hash = sha256("admin123mhd_salt_2024")
INSERT INTO `admins` (`username`, `password_hash`, `name`) VALUES
('admin', 'e2703b179222d0086633d6453248ea4571fc0b611ab18e1b302d594f150bb91f', 'مازن خالد');


-- ─── شريط الأخبار ────────────────────────────────────
INSERT INTO `ticker_messages` (`message`, `sort_order`, `is_active`) VALUES
('مرحباً بكم في منصة دليل — مازن خالد 🎓', 1, 1),
('تابع آخر الملخصات والمراجعات أولاً بأول 📚', 2, 1),
('حظاً موفقاً لجميع الطلاب في الامتحانات النهائية 🌟', 3, 1),
('جديد: تم رفع امتحانات دور يناير 2025 🔥', 4, 1),
('استخدم حاسبة GPA لحساب معدلك الترمي والتراكمي 🧮', 5, 1),
('انضم لمجموعة التيليجرام للمستجدات الفورية 📢', 6, 1);


-- ─── الأخبار والإعلانات ─────────────────────────────
INSERT INTO `news` (`title`, `content`, `type`, `is_ticker`, `is_active`, `created_at`) VALUES
('🎉 مرحباً بكم في منصة دليل الجديدة',
 'تم إطلاق النسخة الجديدة من منصة دليل بواجهة محسّنة وأدوات جديدة مثل حاسبة GPA ومؤقت المذاكرة. نتمنى لكم تجربة ممتعة ومثمرة.',
 'announcement', 1, 1, '2025-01-15 09:00:00'),

('📝 رفع امتحانات نهائي الترم الأول 2024/2025',
 'تم رفع امتحانات الترم الأول لجميع المواد — هياكل البيانات، قواعد البيانات، الشبكات، ونظم التشغيل. الامتحانات متاحة مع نماذج الإجابة.',
 'important', 1, 1, '2025-01-20 10:00:00'),

('⚠️ تنبيه: موعد امتحان هياكل البيانات',
 'امتحان هياكل البيانات يوم الأحد الموافق 2025/02/02 الساعة 10 صباحاً في قاعة A101. يُرجى الحضور قبل الموعد بـ 15 دقيقة.',
 'important', 0, 1, '2025-01-28 14:00:00'),

('📚 إضافة ملخصات الأسبوع السادس',
 'تم إضافة ملخصات الأسبوع السادس لمواد: البرمجة الكائنية، قواعد البيانات، والذكاء الاصطناعي. تابعوا القسم المخصص على المنصة.',
 'news', 0, 1, '2025-02-01 16:00:00'),

('🆕 إضافة شيتات الأسايمنت — هندسة البرمجيات',
 'تم رفع ملفات الأسايمنت الأول والثاني لمادة هندسة البرمجيات مع إرشادات الحل. الموعد النهائي للتسليم الأسبوع القادم.',
 'news', 0, 1, '2025-02-05 11:00:00'),

('🌐 ورشة عمل: مقدمة في الـ Cloud Computing',
 'ستقام ورشة عمل مجانية عن الـ Cloud Computing والـ DevOps يوم الأربعاء القادم الساعة 2 ظهراً. التسجيل عبر الرابط في مجموعة التيليجرام.',
 'announcement', 0, 1, '2025-02-08 09:00:00'),

('📊 نتائج منتصف الفصل',
 'تم رصد درجات امتحانات منتصف الفصل. المراجعة والاعتراض خلال أسبوع من تاريخ الإعلان. تواصل مع الدكتور المختص.',
 'news', 0, 1, '2025-02-10 13:00:00'),

('🔒 محاضرة خاصة: أمن التطبيقات',
 'محاضرة إضافية في مادة أمن المعلومات عن SQL Injection و XSS Attacks غداً الساعة 12 ظهراً. الحضور اختياري ومفيد جداً.',
 'announcement', 0, 1, '2025-02-12 08:00:00'),

('💡 نصيحة: طريقة المذاكرة الفعّالة',
 'استخدم تقنية Pomodoro الموجودة في المنصة — 25 دقيقة مذاكرة مركّزة + 5 دقائق راحة. الدراسات أثبتت فاعليتها في تحسين التركيز بنسبة 40%.',
 'news', 0, 1, '2025-02-14 10:00:00'),

('🏆 تهانينا لطلاب الفصل الأول المتفوقين',
 'نبارك للطلاب المتفوقين في نتائج الفصل الأول. استمروا في التميز وتابعوا المنصة للحصول على أفضل المواد والمراجعات.',
 'announcement', 0, 1, '2025-02-16 12:00:00');


-- ─── المواد الدراسية (10 مواد) ──────────────────────
INSERT INTO `courses` (`name`,`code`,`description`,`icon`,`color`,`semester`,`year`,`sort_order`,`is_active`) VALUES
('مقدمة في البرمجة',
 'CS101',
 'أساسيات البرمجة بلغة C/C++ — المتغيرات، الحلقات، الدوال، والمصفوفات. أول خطواتك في عالم الحاسبات.',
 '💻','#1A7FD4','الأول','1', 1, 1),

('الرياضيات المتقطعة',
 'MATH101',
 'المنطق الرياضي، نظرية المجموعات، العلاقات والدوال، والجبر البولياني. أساس رياضيات الحاسبات.',
 '📐','#8B5CF6','الأول','1', 2, 1),

('البرمجة الكائنية',
 'CS201',
 'مبادئ OOP بلغة Java وC++ — الكلاسات، الكائنات، الوراثة، والتعددية الشكلية.',
 '🎯','#0EA5C9','الأول','2', 3, 1),

('هياكل البيانات والخوارزميات',
 'CS202',
 'القوائم، الأكوام، الطوابير، الأشجار، والـ Graphs. تحليل تعقيد الخوارزميات Big-O.',
 '🌳','#10B981','الثاني','2', 4, 1),

('قواعد البيانات',
 'CS203',
 'تصميم قواعد البيانات العلائقية، لغة SQL، نموذج ER، والـ Normalization.',
 '🗄️','#F59E0B','الثاني','2', 5, 1),

('الشبكات والاتصالات',
 'CS301',
 'نموذج OSI، بروتوكولات TCP/IP، الـ Routing، والـ Switching. أساس هندسة الشبكات.',
 '🌐','#EF4444','الأول','3', 6, 1),

('نظم التشغيل',
 'CS302',
 'العمليات والخيوط، إدارة الذاكرة، جدولة المعالج، وأنظمة الملفات.',
 '⚙️','#F97316','الثاني','3', 7, 1),

('هندسة البرمجيات',
 'CS401',
 'دورة حياة البرنامج، نماذج التطوير Agile/Scrum، UML، والـ Design Patterns.',
 '🏗️','#8B5CF6','الأول','4', 8, 1),

('الذكاء الاصطناعي',
 'CS402',
 'خوارزميات البحث، التعلم الآلي Machine Learning، الشبكات العصبية، ومعالجة اللغات الطبيعية.',
 '🤖','#1A7FD4','الأول','4', 9, 1),

('أمن المعلومات',
 'CS403',
 'التشفير، أمن الشبكات، الاختراق الأخلاقي Ethical Hacking، وحماية البيانات.',
 '🔒','#10B981','الثاني','4',10, 1);


-- ─── المحتوى (Materials) ────────────────────────────
-- [ 1 ] مقدمة في البرمجة
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(1,'شرح كامل: C++ من الصفر للاحتراف','سلسلة شاملة تغطي كل أساسيات C++ مع تمارين عملية','https://www.youtube.com/playlist?list=PLDoPjvoNmBAwy-rS6WKudwVeb_x63EzgS','video',1),
(1,'محاضرة 1: المتغيرات وأنواع البيانات','شرح مفصل للـ Data Types في C++ مع أمثلة تطبيقية','https://www.youtube.com/watch?v=ZzaPdXTrSb8','recording',2),
(1,'محاضرة 2: الجمل الشرطية والحلقات','If-Else, Switch, For, While, Do-While بالتفصيل','https://www.youtube.com/watch?v=Rub-JsjMhWY','recording',3),
(1,'شيت أسايمنت 1 — المصفوفات والدوال','أسئلة مطلوب حلها: تمارين على Arrays and Functions','https://t.me/mazen_cs_notes/101','question',4),
(1,'ملخص الوحدة الأولى — Programming Basics','ملخص منظم بالـ highlights لكل نقاط المحاضرات الأولى','https://t.me/mazen_cs_notes/102','summary',5),
(1,'سلايدز المحاضرات 1-5','الشرائح الرسمية المستخدمة في المحاضرات كـ PDF','https://t.me/mazen_cs_notes/103','slides',6);

-- [ 2 ] الرياضيات المتقطعة
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(2,'شرح: المنطق الرياضي Propositional Logic','Truth Tables, Tautology, Contradiction بالعربي','https://www.youtube.com/watch?v=w5QXiDx0hJs','video',1),
(2,'محاضرة: نظرية المجموعات Sets Theory','Union, Intersection, Complement والعمليات المختلفة','https://www.youtube.com/watch?v=tyDKR4FG3Yw','recording',2),
(2,'محاضرة: العلاقات والدوال Relations & Functions','تعريف العلاقات، خصائصها، والدوال المختلفة','https://www.youtube.com/watch?v=LY7YmuDbuW0','recording',3),
(2,'ملخص الجبر البولياني','Boolean Algebra والتبسيط باستخدام قواعد De Morgan','https://t.me/mazen_cs_notes/201','summary',4),
(2,'سلايدز الفصل الدراسي كاملاً','جميع شرائح المادة مرتبة حسب المحاضرات','https://t.me/mazen_cs_notes/202','slides',5),
(2,'أسئلة مراجعة شاملة','50 سؤال MCQ + مقال مع الإجابات النموذجية','https://t.me/mazen_cs_notes/203','question',6);

-- [ 3 ] البرمجة الكائنية
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(3,'OOP in Java — Full Course بالعربي','الكورس الأشمل لتعلم OOP بلغة Java من الصفر','https://www.youtube.com/playlist?list=PLnzqK5HvcpwQfXeFaGHRYVfIZB-OXmhNn','video',1),
(3,'محاضرة: الكلاسات والكائنات Classes & Objects','تعريف الكلاس، الـ Constructor، و Encapsulation','https://www.youtube.com/watch?v=GoXwIVyNvX0','recording',2),
(3,'محاضرة: الوراثة والتعددية Inheritance & Polymorphism','Method Overriding, Overloading, Abstract Classes','https://www.youtube.com/watch?v=HudxkRVfkLc','recording',3),
(3,'ملخص Design Principles — SOLID','شرح مبادئ SOLID بأمثلة عملية بالعربي','https://t.me/mazen_cs_notes/301','summary',4),
(3,'Lab Sheet 1: Banking System','مشروع نظام بنك مصغر باستخدام OOP — متطلبات التسليم','https://t.me/mazen_cs_notes/302','question',5),
(3,'Lab Sheet 2: Library Management System','تصميم نظام مكتبة — الـ UML diagram مطلوب','https://t.me/mazen_cs_notes/303','question',6);

-- [ 4 ] هياكل البيانات والخوارزميات
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(4,'Data Structures — الكورس الكامل','شرح شامل للـ Stack, Queue, LinkedList, Trees, Graphs','https://www.youtube.com/playlist?list=PLCInYL3l2AajqOUW_2SwjWeMwf4vL4RSp','video',1),
(4,'محاضرة: Stack والـ Queue','تطبيقات عملية: Balanced Parentheses, BFS, DFS','https://www.youtube.com/watch?v=wjI1WNcIntg','recording',2),
(4,'محاضرة: Binary Search Tree','Insertion, Deletion, Search + الـ Traversal Methods','https://www.youtube.com/watch?v=pYT9F8_LFTM','recording',3),
(4,'محاضرة: Sorting Algorithms','Bubble, Selection, Insertion, Merge, Quick Sort مع التحليل','https://www.youtube.com/watch?v=g-PGLbMth_g','recording',4),
(4,'ملخص Big-O Notation','تحليل تعقيد الوقت والمكان — جدول مقارنة شامل','https://t.me/mazen_cs_notes/401','summary',5),
(4,'شيت أسايمنت: Linked List Implementation','تطبيق كامل للـ Doubly Linked List مع الدوال المطلوبة','https://t.me/mazen_cs_notes/402','question',6);

-- [ 5 ] قواعد البيانات
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(5,'Database Design — من الصفر للاحتراف','ER Diagram, Normalization, SQL بالتفصيل الممل','https://www.youtube.com/playlist?list=PLDoPjvoNmBAxvshoHoP7RdFdqHWBXvzJl','video',1),
(5,'محاضرة: ER Diagram والـ Mapping','تحويل الـ ER Diagram لجداول علائقية خطوة بخطوة','https://www.youtube.com/watch?v=T7AxM8GBHdk','recording',2),
(5,'محاضرة: SQL Queries المتقدمة','JOINs, Subqueries, Aggregate Functions, Views','https://www.youtube.com/watch?v=HXV3zeQKqGY','recording',3),
(5,'ملخص Normalization 1NF→3NF','شرح مبسط للـ Normal Forms مع أمثلة عملية','https://t.me/mazen_cs_notes/501','summary',4),
(5,'سلايدز: SQL Complete Reference','مرجع SQL الكامل — DDL, DML, DCL, TCL','https://t.me/mazen_cs_notes/502','slides',5),
(5,'Lab: مشروع نظام تسجيل طلاب','تصميم قاعدة بيانات نظام تسجيل جامعي — المطلوب كاملاً','https://t.me/mazen_cs_notes/503','question',6);

-- [ 6 ] الشبكات والاتصالات
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(6,'Computer Networks — الشرح الكامل','نموذج OSI, TCP/IP, Protocols من الصفر','https://www.youtube.com/playlist?list=PLBlnK6fEyqRgMCUAG0XRw78UA8qnv6jEx','video',1),
(6,'محاضرة: OSI Model — الطبقات السبع','وظيفة كل طبقة ومثال عملي على كل منها','https://www.youtube.com/watch?v=vv4y_uOneC0','recording',2),
(6,'محاضرة: IP Addressing والـ Subnetting','CIDR, Subnet Mask, VLSM بالتدريب العملي','https://www.youtube.com/watch?v=s_gy9HMOLnE','recording',3),
(6,'ملخص: TCP vs UDP وبروتوكولات التطبيق','HTTP, HTTPS, FTP, DNS, DHCP, SMTP مقارنة وشرح','https://t.me/mazen_cs_notes/601','summary',4),
(6,'Packet Tracer Labs — الشبكات العملي','ملفات Cisco Packet Tracer للتدريب العملي','https://t.me/mazen_cs_notes/602','slides',5),
(6,'أسئلة مراجعة الشبكات','بنك أسئلة MCQ + مسائل Subnetting','https://t.me/mazen_cs_notes/603','question',6);

-- [ 7 ] نظم التشغيل
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(7,'Operating Systems — الكورس الكامل','Processes, Threads, Memory, File Systems بالتفصيل','https://www.youtube.com/playlist?list=PLbGui_ZYuhigTkSVBhLgjpBHEaRViXLdm','video',1),
(7,'محاضرة: Process Scheduling','FCFS, SJF, Round Robin, Priority Scheduling مع الحلول','https://www.youtube.com/watch?v=Jkmy2YLUbUY','recording',2),
(7,'محاضرة: Memory Management','Paging, Segmentation, Virtual Memory بالتفصيل','https://www.youtube.com/watch?v=LKe7xK0bF7o','recording',3),
(7,'محاضرة: Deadlock والـ Synchronization','Mutex, Semaphore, Deadlock Detection والتعامل معه','https://www.youtube.com/watch?v=onkWXaXAgbY','recording',4),
(7,'ملخص Scheduling Algorithms','مقارنة بين خوارزميات الجدولة مع أمثلة محلولة','https://t.me/mazen_cs_notes/701','summary',5),
(7,'شيت: حل مسائل Scheduling','20 مسألة محلولة في Process Scheduling كل الأنواع','https://t.me/mazen_cs_notes/702','question',6);

-- [ 8 ] هندسة البرمجيات
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(8,'Software Engineering Fundamentals','SDLC, Agile, Scrum, Kanban للمبتدئين','https://www.youtube.com/watch?v=9TycLR0TqFA','video',1),
(8,'محاضرة: UML Diagrams الكاملة','Use Case, Class, Sequence, Activity Diagrams بالأمثلة','https://www.youtube.com/watch?v=WnMQ8HlmeXc','recording',2),
(8,'محاضرة: Design Patterns','Singleton, Factory, Observer, MVC بالكود','https://www.youtube.com/watch?v=BWprw8Nu4dI','recording',3),
(8,'ملخص Agile vs Waterfall','مقارنة شاملة بين منهجيات التطوير مع متى تستخدم كل منها','https://t.me/mazen_cs_notes/801','summary',4),
(8,'مشروع النهائي — متطلبات التسليم','متطلبات المشروع الكامل، التوثيق، وخطة الاختبار','https://t.me/mazen_cs_notes/802','question',5),
(8,'نموذج SRS — متطلبات البرنامج','قالب Software Requirements Specification جاهز','https://t.me/mazen_cs_notes/803','slides',6);

-- [ 9 ] الذكاء الاصطناعي
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(9,'AI للمبتدئين — من الصفر','Search Algorithms, Machine Learning, Neural Networks','https://www.youtube.com/playlist?list=PLeo1K3hjS3uu7CxAacxVndI4bE_o3BDtO','video',1),
(9,'محاضرة: Search Algorithms','BFS, DFS, A*, Hill Climbing, Genetic Algorithms','https://www.youtube.com/watch?v=5eIK3zUdYmE','recording',2),
(9,'محاضرة: Machine Learning Intro','Supervised, Unsupervised, Reinforcement Learning','https://www.youtube.com/watch?v=ukzFI9rgwfU','recording',3),
(9,'محاضرة: Neural Networks Basics','Perceptron, Backpropagation, Activation Functions','https://www.youtube.com/watch?v=aircAruvnKk','recording',4),
(9,'ملخص: ML Algorithms Cheatsheet','مقارنة بين الخوارزميات — متى تستخدم كل منها','https://t.me/mazen_cs_notes/901','summary',5),
(9,'Lab: Python للذكاء الاصطناعي','Jupyter Notebooks جاهزة مع كود NumPy وScikit-learn','https://t.me/mazen_cs_notes/902','question',6);

-- [ 10 ] أمن المعلومات
INSERT INTO `materials` (`course_id`,`title`,`description`,`url`,`type`,`sort_order`) VALUES
(10,'Cybersecurity Essentials بالعربي','أمن الشبكات، التشفير، الاختراق الأخلاقي من الصفر','https://www.youtube.com/playlist?list=PLCIJjtzQPZJ-k4ADq_kyuyWVSRPC5JxeG','video',1),
(10,'محاضرة: Cryptography & Encryption','Symmetric, Asymmetric, Hash Functions, PKI','https://www.youtube.com/watch?v=AQDCe585Lnc','recording',2),
(10,'محاضرة: Network Security','Firewalls, IDS/IPS, VPN, SSL/TLS','https://www.youtube.com/watch?v=E_-gkZFZ7NI','recording',3),
(10,'محاضرة: OWASP Top 10 Vulnerabilities','SQL Injection, XSS, CSRF وطرق الوقاية والاستغلال','https://www.youtube.com/watch?v=rWHvp7rUka8','recording',4),
(10,'ملخص التشفير المتماثل وغير المتماثل','AES, RSA, DES, 3DES مقارنة وأمثلة','https://t.me/mazen_cs_notes/1001','summary',5),
(10,'CTF Challenges للتدريب العملي','Capture The Flag — تحديات أمنية للتدريب','https://t.me/mazen_cs_notes/1002','question',6);


-- ─── ملخصات الأسبوع ─────────────────────────────────
INSERT INTO `weekly_summaries` (`course_id`,`week_number`,`week_title`,`description`,`telegram_url`,`is_active`,`created_at`) VALUES
(4, 1,'الأسبوع الأول: مقدمة في هياكل البيانات','مراجعة المفاهيم الأساسية: Array, Pointer, الذاكرة الديناميكية','https://t.me/mazen_cs_notes/w401','1','2025-01-26 09:00:00'),
(4, 2,'الأسبوع الثاني: Stack والـ Queue','تطبيق Stack بالمصفوفة والـ LinkedList + Queue','https://t.me/mazen_cs_notes/w402','1','2025-02-02 09:00:00'),
(4, 3,'الأسبوع الثالث: Linked Lists','Singly, Doubly, Circular Linked List مع الدوال كاملة','https://t.me/mazen_cs_notes/w403','1','2025-02-09 09:00:00'),
(4, 4,'الأسبوع الرابع: Binary Trees','Binary Tree, BST — Insertion, Deletion, Search','https://t.me/mazen_cs_notes/w404','1','2025-02-16 09:00:00'),

(5, 1,'الأسبوع الأول: مقدمة في قواعد البيانات','مفاهيم DBMS، الفرق بين الملفات وقواعد البيانات','https://t.me/mazen_cs_notes/w501','1','2025-01-26 10:00:00'),
(5, 2,'الأسبوع الثاني: ER Diagram','رسم الـ Entities, Attributes, Relationships بالصح','https://t.me/mazen_cs_notes/w502','1','2025-02-02 10:00:00'),
(5, 3,'الأسبوع الثالث: SQL الأساسي','CREATE, INSERT, SELECT, UPDATE, DELETE بالتطبيق','https://t.me/mazen_cs_notes/w503','1','2025-02-09 10:00:00'),
(5, 4,'الأسبوع الرابع: SQL المتقدم','JOINs كاملة + Subqueries + GROUP BY','https://t.me/mazen_cs_notes/w504','1','2025-02-16 10:00:00'),

(7, 1,'الأسبوع الأول: مقدمة في نظم التشغيل','تعريف الـ OS، وظائفه، الفرق بين الـ Process والـ Program','https://t.me/mazen_cs_notes/w701','1','2025-01-27 09:00:00'),
(7, 2,'الأسبوع الثاني: Process Scheduling','FCFS وSJF مع مسائل محلولة — Gantt Chart','https://t.me/mazen_cs_notes/w702','1','2025-02-03 09:00:00'),
(7, 3,'الأسبوع الثالث: Memory Management','Paging + مسائل على Page Tables','https://t.me/mazen_cs_notes/w703','1','2025-02-10 09:00:00'),
(7, 4,'الأسبوع الرابع: Deadlock','شروط الـ Deadlock والحلول — Prevention, Avoidance','https://t.me/mazen_cs_notes/w704','1','2025-02-17 09:00:00');


-- ─── بنك الأسئلة ────────────────────────────────────
INSERT INTO `questions` (`course_id`,`title`,`description`,`type`,`telegram_url`,`is_active`) VALUES
(4,'50 سؤال MCQ — هياكل البيانات','يغطي: Stack, Queue, Trees, Graphs, Sorting Algorithms','mcq','https://t.me/mazen_cs_notes/q401',1),
(4,'أسئلة مقال — هياكل البيانات','أسئلة الكتابة: تطبيق خوارزميات وتحليل التعقيد','essay','https://t.me/mazen_cs_notes/q402',1),
(4,'ورقة عمل: BST Operations','20 سؤال على Binary Search Tree — Insert, Delete, Search','mixed','https://t.me/mazen_cs_notes/q403',1),

(5,'MCQ قواعد البيانات — 60 سؤال','يشمل: ER, SQL, Normalization, Transactions, Indexing','mcq','https://t.me/mazen_cs_notes/q501',1),
(5,'مسائل SQL عملية','30 استعلام SQL من بسيط لمتقدم مع الإجابات','sheet','https://t.me/mazen_cs_notes/q502',1),
(5,'أسئلة Normalization','حل 15 مثال على 1NF, 2NF, 3NF, BCNF','mixed','https://t.me/mazen_cs_notes/q503',1),

(6,'MCQ الشبكات — 70 سؤال','يغطي كل المنهج: OSI, TCP/IP, Subnetting, Protocols','mcq','https://t.me/mazen_cs_notes/q601',1),
(6,'مسائل Subnetting محلولة','25 مسألة Subnetting بالحل التفصيلي خطوة بخطوة','sheet','https://t.me/mazen_cs_notes/q602',1),

(7,'MCQ نظم التشغيل — 80 سؤال','Scheduling, Memory, Deadlock, File Systems','mcq','https://t.me/mazen_cs_notes/q701',1),
(7,'مسائل Process Scheduling','حل 30 مسألة بجميع الخوارزميات — Preemptive & Non-preemptive','sheet','https://t.me/mazen_cs_notes/q702',1),
(7,'أسئلة Memory Management','مسائل Paging + Virtual Memory + TLB محلولة','mixed','https://t.me/mazen_cs_notes/q703',1),

(9,'MCQ الذكاء الاصطناعي','يغطي: Search, ML, Neural Networks, NLP','mcq','https://t.me/mazen_cs_notes/q901',1),
(10,'MCQ أمن المعلومات','Cryptography, Network Security, OWASP, Ethical Hacking','mcq','https://t.me/mazen_cs_notes/q1001',1),
(3,'أسئلة OOP — مفاهيم وتطبيقات','يشمل: Classes, Inheritance, Polymorphism, Interfaces','mixed','https://t.me/mazen_cs_notes/q301',1),
(1,'بنك MCQ البرمجة الإجرائية','C/C++ — Variables, Arrays, Pointers, Functions','mcq','https://t.me/mazen_cs_notes/q101',1);


-- ─── الامتحانات السابقة ─────────────────────────────
INSERT INTO `past_exams` (`course_id`,`title`,`description`,`exam_type`,`year`,`semester`,`telegram_url`,`is_active`) VALUES
-- هياكل البيانات
(4,'نهائي هياكل البيانات — 2024/2025 ترم1','الامتحان النهائي الأخير — يشمل كل المنهج','final',2024,'الأول','https://t.me/mazen_cs_notes/e401',1),
(4,'منتصف هياكل البيانات — 2024/2025','امتحان منتصف الفصل — Stack, Queue, LinkedList','midterm',2024,'الأول','https://t.me/mazen_cs_notes/e402',1),
(4,'نهائي هياكل البيانات — 2023/2024','الدور الأول 2023 — مع نموذج الإجابة','final',2023,'الأول','https://t.me/mazen_cs_notes/e403',1),
(4,'نهائي هياكل البيانات — 2022/2023','الدور الأول 2022 — Trees and Graphs','final',2022,'الأول','https://t.me/mazen_cs_notes/e404',1),

-- قواعد البيانات
(5,'نهائي قواعد البيانات — 2024/2025','يشمل: ER, SQL, Normalization, Transactions','final',2024,'الثاني','https://t.me/mazen_cs_notes/e501',1),
(5,'منتصف قواعد البيانات — 2024/2025','ER Diagram + SQL Queries + Mapping','midterm',2024,'الثاني','https://t.me/mazen_cs_notes/e502',1),
(5,'نهائي قواعد البيانات — 2023/2024','دور يناير 2024 — مع الإجابة النموذجية','final',2023,'الثاني','https://t.me/mazen_cs_notes/e503',1),
(5,'نهائي قواعد البيانات — 2022/2023','SQL Advanced + Normalization الكامل','final',2022,'الثاني','https://t.me/mazen_cs_notes/e504',1),

-- الشبكات
(6,'نهائي الشبكات — 2024/2025','OSI, TCP/IP, Subnetting, Routing Protocols','final',2024,'الأول','https://t.me/mazen_cs_notes/e601',1),
(6,'منتصف الشبكات — 2024/2025','OSI Model + IP Addressing + Subnetting','midterm',2024,'الأول','https://t.me/mazen_cs_notes/e602',1),
(6,'نهائي الشبكات — 2023/2024','مع الإجابة النموذجية كاملة','final',2023,'الأول','https://t.me/mazen_cs_notes/e603',1),
(6,'نهائي الشبكات — 2022/2023','يشمل مسائل Subnetting صعبة','final',2022,'الأول','https://t.me/mazen_cs_notes/e604',1),

-- نظم التشغيل
(7,'نهائي نظم التشغيل — 2024/2025','Scheduling + Memory + Deadlock + File Systems','final',2024,'الثاني','https://t.me/mazen_cs_notes/e701',1),
(7,'منتصف نظم التشغيل — 2024/2025','Process Scheduling + مسائل Gantt Chart','midterm',2024,'الثاني','https://t.me/mazen_cs_notes/e702',1),
(7,'نهائي نظم التشغيل — 2023/2024','دور مايو 2024 — مع الإجابات','final',2023,'الثاني','https://t.me/mazen_cs_notes/e703',1),
(7,'نهائي نظم التشغيل — 2022/2023','يشمل كل مسائل الـ Paging','final',2022,'الثاني','https://t.me/mazen_cs_notes/e704',1),

-- مقدمة في البرمجة
(1,'نهائي البرمجة — 2024/2025','C++ — Arrays, Pointers, Functions, Files','final',2024,'الأول','https://t.me/mazen_cs_notes/e101',1),
(1,'منتصف البرمجة — 2024/2025','Variables, Conditionals, Loops, Functions','midterm',2024,'الأول','https://t.me/mazen_cs_notes/e102',1),
(1,'نهائي البرمجة — 2023/2024','مع الكود والإجابات النموذجية','final',2023,'الأول','https://t.me/mazen_cs_notes/e103',1),

-- الذكاء الاصطناعي
(9,'نهائي الذكاء الاصطناعي — 2024/2025','Search + ML + Neural Networks + NLP','final',2024,'الأول','https://t.me/mazen_cs_notes/e901',1),
(9,'منتصف الذكاء الاصطناعي — 2024/2025','Search Algorithms + Optimization Problems','midterm',2024,'الأول','https://t.me/mazen_cs_notes/e902',1),

-- أمن المعلومات
(10,'نهائي أمن المعلومات — 2024/2025','Crypto + Network Security + OWASP','final',2024,'الثاني','https://t.me/mazen_cs_notes/e1001',1),
(10,'منتصف أمن المعلومات — 2024/2025','Cryptography — Symmetric & Asymmetric','midterm',2024,'الثاني','https://t.me/mazen_cs_notes/e1002',1);


-- ─── الروابط المهمة ──────────────────────────────────
INSERT INTO `important_links` (`title`,`url`,`icon`,`type`,`sort_order`,`is_active`) VALUES
('قناة تيليجرام دليل',       'https://t.me/mazen_cs_notes',         '📢','telegram',1,1),
('قناة يوتيوب Elzero',       'https://www.youtube.com/@ElzeroWebSchool','▶️','youtube', 2,1),
('قناة يوتيوب Codezilla',    'https://www.youtube.com/@Codezilla_',  '🎬','youtube', 3,1),
('W3Schools المرجع',          'https://www.w3schools.com',            '🌐','website',  4,1),
('GeeksforGeeks',             'https://www.geeksforgeeks.org',        '🧑‍💻','website',5,1),
('LeetCode — تدريب Coding',  'https://leetcode.com',                 '⚡','practice',6,1),
('HackerRank',                'https://www.hackerrank.com',           '🏆','practice',7,1),
('Cisco NetAcad',             'https://www.netacad.com',              '🌐','course',   8,1);

-- ══════════════════════════════════════════════════
-- ✅ تم الإعداد بنجاح!
-- الموقع جاهز للعمل. قم برفع الملفات وزيارة الموقع.
-- بيانات الدخول: admin / admin123
-- ══════════════════════════════════════════════════

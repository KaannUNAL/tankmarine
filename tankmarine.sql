-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 08 Oca 2026, 14:43:40
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `tankmarine`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `about`
--

CREATE TABLE `about` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `mission` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `about`
--

INSERT INTO `about` (`id`, `title`, `content`, `mission`, `vision`, `image`, `video_url`, `created_at`, `updated_at`) VALUES
(1, 'Hakkımızda', 'BİZ KİMİZ;\r\n\r\nTankmarine Gemi İşletmeciliği, 2017 yılında İstanbul\'da kurulmuş olup, üçüncü parti firmalara Gemi İşletmeciliği Teknik hizmetleri sunmaktadır.  Ana hedefimiz, kendi değerlerimiz ve  endüstri gereksinimlerine uygun olarak işletmeciliğimiz altındaki gemileri teknik olarak yönetmektir.\r\n\r\nGemiadamı seçimi ve işe alımı konusunda engin tecrübeye sahip olarak, Müşterimizin gereksinimlerine en hızlı bir şekilde yanıt vermeye odaklanmış, yenilikçi, kararlı bir firmayız. \r\n\r\nTankMarine Gemi İşletmeciliği, global marketde hızla gelişerek,müşterilerine kaliteli ve düşük maliyetli Teknik hizmet sunacağından emindir.\r\n\r\n', 'İş ortaklarımızın bilgi, deneyim ve inovasyon yoluyla temel hedeflerine ulaşmalarını sağlamak.', 'İşinde profesyonel,denizcilik piyasasında önde gelen yaratıcı çözümlerin öncüsü olmak.', 'about/695f54c46c308_1767855300.jpg', '', '2026-01-08 06:31:25', '2026-01-08 06:55:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Kaan ÜNAL', '$2y$10$tY7ahrYTtCpTXTPRh5O6rueJkuyuNrTcVwHAcVfHZ2OgPz2vKN5za', 'admin@tankmarine.com.tr', 'Kaan ÜNAL', 'admin', 1, '2026-01-08 06:46:34', '2026-01-08 06:31:25', '2026-01-08 13:29:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `careers`
--

CREATE TABLE `careers` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `careers`
--

INSERT INTO `careers` (`id`, `title`, `department`, `location`, `type`, `description`, `requirements`, `active`, `deadline`, `created_at`, `updated_at`) VALUES
(1, 'IT DEPARTMANT', 'IT', 'tuzla', 'Tam Zamanlı', 'IT', 'IT', 1, '2026-01-24', '2026-01-08 08:04:43', '2026-01-08 08:04:43');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `career_applications`
--

CREATE TABLE `career_applications` (
  `id` int(11) NOT NULL,
  `career_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `career_applications`
--

INSERT INTO `career_applications` (`id`, `career_id`, `name`, `email`, `phone`, `cv_file`, `cover_letter`, `status`, `created_at`) VALUES
(1, 1, 'Kaan ÜNAL', 'kaanunal1907@icloud.com', '05398203236', 'cv/695f6537c7b33_1767859511.pdf', 'test', 'pending', '2026-01-08 08:05:11');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `contact_form_fields`
--

CREATE TABLE `contact_form_fields` (
  `id` int(11) NOT NULL,
  `field_name` varchar(50) DEFAULT NULL,
  `field_label` varchar(100) DEFAULT NULL,
  `field_type` varchar(50) DEFAULT 'text',
  `is_required` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `placeholder` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `contact_form_fields`
--

INSERT INTO `contact_form_fields` (`id`, `field_name`, `field_label`, `field_type`, `is_required`, `is_active`, `sort_order`, `placeholder`, `created_at`) VALUES
(1, 'name', 'Ad Soyad', 'text', 1, 1, 1, 'Ad Soyadınız', '2026-01-08 07:52:15'),
(2, 'email', 'E-posta', 'email', 1, 1, 2, 'ornek@email.com', '2026-01-08 07:52:15'),
(3, 'phone', 'Telefon', 'tel', 0, 1, 3, '+90 XXX XXX XX XX', '2026-01-08 07:52:15'),
(4, 'subject', 'Konu', 'text', 1, 1, 4, 'Mesaj konusu', '2026-01-08 07:52:15'),
(5, 'message', 'Mesajınız', 'textarea', 1, 1, 5, 'Mesajınızı buraya yazın...', '2026-01-08 07:52:15');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'test', 'test@gmail.com', '05398203236', 'test', 'test', 'read', '2026-01-08 07:43:40');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `country_flags`
--

CREATE TABLE `country_flags` (
  `id` int(11) NOT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `flag_emoji` varchar(10) DEFAULT NULL,
  `flag_image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `country_flags`
--

INSERT INTO `country_flags` (`id`, `country_name`, `flag_emoji`, `flag_image`, `sort_order`, `active`, `created_at`) VALUES
(1, 'TÜRKİYE', '🇹🇷', 'flags/695f5f773a847_1767858039.webp', 1, 1, '2026-01-08 07:30:59'),
(14, 'İTALYA', '', 'flags/695f675f9fd1d_1767860063.png', 2, 1, '2026-01-08 08:14:23'),
(15, 'İNGİLTERE', '', 'flags/695f679e2f1ef_1767860126.png', 3, 1, '2026-01-08 08:15:26'),
(16, 'ALMANYA', '', 'flags/695f67b22cd60_1767860146.png', 4, 1, '2026-01-08 08:15:46'),
(17, 'ABD', '', 'flags/695f67cd265d7_1767860173.png', 5, 1, '2026-01-08 08:16:13'),
(18, 'İSPANYA', '', 'flags/695f67e6de62f_1767860198.png', 6, 1, '2026-01-08 08:16:38');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `fleet`
--

CREATE TABLE `fleet` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `dwt` varchar(50) DEFAULT NULL,
  `built_year` varchar(10) DEFAULT NULL,
  `flag` varchar(100) DEFAULT NULL,
  `length` varchar(50) DEFAULT NULL,
  `beam` varchar(50) DEFAULT NULL,
  `draft` varchar(50) DEFAULT NULL,
  `engine` varchar(255) DEFAULT NULL,
  `speed` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `fleet`
--

INSERT INTO `fleet` (`id`, `name`, `type`, `dwt`, `built_year`, `flag`, `length`, `beam`, `draft`, `engine`, `speed`, `description`, `image`, `active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'M/T LIDYA', 'Oil Chemical Tanker', '2946', '2008', 'TURKIYE', '', '', '', '', '', '', 'fleet/695f563e894da_1767855678.jfif', 1, 0, '2026-01-08 07:01:18', '2026-01-08 07:01:18'),
(2, 'M/T KARİA', 'Oil Chemical Tanker', '2946', '2005', 'TURKIYE', '', '', '', '', '', '', 'fleet/695f56e7103bd_1767855847.jpg', 1, 0, '2026-01-08 07:04:07', '2026-01-08 07:04:07'),
(3, 'M/T İYONYA', 'Oil Chemical Tanker', '2946', '2012', 'TURKIYE', '', '', '', '', '', '', 'fleet/695f57493ca65_1767855945.jpeg', 1, 0, '2026-01-08 07:05:45', '2026-01-08 07:05:45');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `fleet_gallery`
--

CREATE TABLE `fleet_gallery` (
  `id` int(11) NOT NULL,
  `fleet_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `gallery`
--

INSERT INTO `gallery` (`id`, `category_id`, `title`, `image`, `description`, `sort_order`, `active`, `created_at`) VALUES
(1, 1, 'Lidya', 'gallery/695f5fc826533_1767858120.jfif', 'Lidya', 0, 1, '2026-01-08 07:42:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `gallery_categories`
--

CREATE TABLE `gallery_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `gallery_categories`
--

INSERT INTO `gallery_categories` (`id`, `name`, `slug`, `sort_order`, `created_at`) VALUES
(1, 'Gemilerimiz', 'gemilerimiz', 1, '2026-01-08 06:31:25'),
(2, 'Etkinlikler', 'etkinlikler', 2, '2026-01-08 06:31:25'),
(3, 'Liman Operasyonları', 'liman-operasyonlari', 3, '2026-01-08 06:31:25');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `news`
--

INSERT INTO `news` (`id`, `title`, `slug`, `summary`, `content`, `image`, `views`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Dokuz Eylül University 2024 Career Days', 'dokuz-eylul-university-2024-career-days', '.', 'As Tankmarine Ship Management, we are happy to participate in Dokuz Eylül University 2024 Career Days. Thank you to all participants.', 'news/695f5bd43f971_1767857108.jpeg', 4, 1, '2026-01-08 07:25:08', '2026-01-08 12:07:40'),
(2, 'Karadeniz Technical University And Ordu University 2024 Career Days', 'karadeniz-technical-university-and-ordu-university-2024-career-days', '.', 'As Tankmarine Ship Management, we are delighted to have participated in the Career Days events organized by Ordu University DUIM and Karadeniz Technical University DUIM last week.These events provided an excellent platform to engage with bright and ambitious students, exchange ideas with industry professionals, and explore new opportunities for collaboration. We were inspired by the energy, curiosity, and passion displayed by all participants.We are pleased to share that our Operations Manager, Capt. Orhan Kasap, actively participated in these events on behalf of our company.We extend our sincere gratitude to the universities and everyone involved in organizing these outstanding events. We look forward to building stronger connections and contributing to the professional growth of future talent.', 'news/695f5c387352c_1767857208.jpeg', 2, 1, '2026-01-08 07:26:48', '2026-01-08 12:51:33');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT 'Tankmarine Ship Management',
  `site_logo` varchar(255) DEFAULT NULL,
  `site_favicon` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `map_embed` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `google_analytics` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `site_logo`, `site_favicon`, `phone`, `email`, `address`, `facebook`, `twitter`, `linkedin`, `instagram`, `youtube`, `map_embed`, `meta_title`, `meta_description`, `meta_keywords`, `google_analytics`, `created_at`, `updated_at`) VALUES
(1, 'Tankmarine Ship Management', 'logo/695f64915e725_1767859345.png', 'logo/695f64685b362_1767859304.png', '+90 216 510 8104', 'support@tankmarine.com.tr', 'Yayla, Kekik Sok. No:6A D:6B, 34940 Tuzla/İstanbul', '', '', 'https://www.linkedin.com/company/tankmarine-ship-management/?viewAsMember=true', '', '', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3019.0055313511557!2d29.298779012480235!3d40.82784217125702!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14caaf3ec0a1e4ab%3A0x8812a6feb485f0fb!2sTANKMARINE%20MANAGEMENT%20%26%20TRADING%20LTD.!5e0!3m2!1str!2str!4v1767858820785!5m2!1str!2str', 'Tankmarine Ship Management - Profesyonel Tanker İşletmeciliği', 'Tankmarine Ship Management olarak denizcilik sektöründe profesyonel tanker işletmeciliği ve gemi yönetimi hizmetleri sunuyoruz.', 'tanker işletmeciliği, gemi yönetimi, denizcilik, ship management, tankmarine, teknik gemi yönetimi', '', '2026-01-08 06:31:25', '2026-01-08 08:02:25');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `sliders`
--

INSERT INTO `sliders` (`id`, `title`, `description`, `image`, `button_text`, `button_link`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
(4, '1', '1', 'slider/695f53be71f7e_1767855038.jpg', '', '', 1, 1, '2026-01-08 06:50:38', '2026-01-08 06:51:27'),
(5, '2', '2', 'slider/695f53e79701d_1767855079.jpg', '', '', 2, 1, '2026-01-08 06:51:19', '2026-01-08 06:51:19'),
(6, '3', '3', 'slider/695f540de1121_1767855117.jpg', '', '', 3, 1, '2026-01-08 06:51:57', '2026-01-08 06:52:25'),
(7, '4', '4', 'slider/695f54203e232_1767855136.jpg', '', '', 4, 1, '2026-01-08 06:52:16', '2026-01-08 06:52:29');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `statistics`
--

CREATE TABLE `statistics` (
  `id` int(11) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `statistics`
--

INSERT INTO `statistics` (`id`, `label`, `value`, `icon`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Yıllık Deneyim', '9', 'fa-calendar', 1, 1, '2026-01-08 06:31:25', '2026-01-08 07:34:56'),
(2, 'Filo Sayısı', '3+', 'fa-ship', 2, 1, '2026-01-08 06:31:25', '2026-01-08 07:35:16'),
(3, 'Deniz Mili', '500K+', 'fa-anchor', 3, 1, '2026-01-08 06:31:25', '2026-01-08 06:31:25');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `team`
--

CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `team`
--

INSERT INTO `team` (`id`, `name`, `position`, `description`, `image`, `email`, `phone`, `linkedin`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
(1, 'OKTAY BEŞİKÇİ', 'General Manager', '', 'team/695f57fa1dc1e_1767856122.jpeg', '', '', '', 1, 1, '2026-01-08 07:08:42', '2026-01-08 07:15:10'),
(2, 'ORHAN KASAP', 'OPERATIONS MANAGER', '', 'team/695f583392a7b_1767856179.jpeg', '', '', '', 2, 1, '2026-01-08 07:09:39', '2026-01-08 07:15:19'),
(3, 'İLKER DEĞİŞİCİ', 'HSEQ/DPA/VETTING SUPERINTENDENT', '', 'team/695f595eb55c1_1767856478.jpeg', '', '', '', 3, 1, '2026-01-08 07:14:38', '2026-01-08 07:15:24'),
(4, 'ŞİYAR PERÇİN', 'TECHNICAL MANAGER', '', 'team/695f59b3c4825_1767856563.jpeg', '', '', '', 4, 1, '2026-01-08 07:16:03', '2026-01-08 07:16:16'),
(5, 'BAHTİYAR ŞENER', 'TECHNICAL SUPERINTENDENT', '', 'team/695f59f56236c_1767856629.jpeg', '', '', '', 5, 1, '2026-01-08 07:17:09', '2026-01-08 07:17:09'),
(6, 'AYNUR ŞENER', 'TECHNICAL SUPERINTENDENT ASSISTANT', '', 'team/695f5a1795ca5_1767856663.jpeg', '', '', '', 6, 1, '2026-01-08 07:17:43', '2026-01-08 07:17:43'),
(7, 'ÇETİN AKTAŞ', 'ELECTRIC SUPERINTENDENT', '', 'team/695f5a5287e02_1767856722.jpeg', '', '', '', 7, 1, '2026-01-08 07:18:42', '2026-01-08 07:18:42'),
(8, 'CİHAN AKAN', 'CREW MANAGER', '', 'team/695f5a76b2065_1767856758.jpeg', '', '', '', 8, 1, '2026-01-08 07:19:18', '2026-01-08 07:19:23'),
(9, 'DİLA SU AKKUŞ', 'CREW MANAGER ASSISTANT', '', 'team/695f5aa2a0b75_1767856802.jpeg', '', '', '', 9, 1, '2026-01-08 07:20:02', '2026-01-08 07:20:06'),
(10, 'ARZU YILMAZ', 'FINANCE AND ACCOUNT MANAGER', '', 'team/695f5ac49942f_1767856836.jpeg', '', '', '', 10, 1, '2026-01-08 07:20:36', '2026-01-08 07:20:36'),
(11, 'ASLIHAN KARABACAK', 'ACCOUNTER', '', 'team/695f5ae4a613c_1767856868.jpeg', '', '', '', 11, 1, '2026-01-08 07:21:08', '2026-01-08 07:21:08'),
(12, 'KAAN HAKAN ARSLAN', 'PURCHASE MANAGER', '', 'team/695f5b0124a64_1767856897.jpeg', '', '', '', 12, 1, '2026-01-08 07:21:37', '2026-01-08 07:21:37'),
(13, 'HAKAN İNCİ', 'PURCHASE SPECIALIST', '', 'team/695f5b2892d5e_1767856936.jpeg', '', '', '', 13, 1, '2026-01-08 07:22:16', '2026-01-08 07:22:16'),
(14, 'KAAN ÜNAL', 'IT MANAGER', '', 'team/695f5b6a3585c_1767857002.jpeg', '', '', '', 14, 1, '2026-01-08 07:23:22', '2026-01-08 07:23:22');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `about`
--
ALTER TABLE `about`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Tablo için indeksler `careers`
--
ALTER TABLE `careers`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `career_applications`
--
ALTER TABLE `career_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `career_id` (`career_id`);

--
-- Tablo için indeksler `contact_form_fields`
--
ALTER TABLE `contact_form_fields`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `country_flags`
--
ALTER TABLE `country_flags`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `fleet`
--
ALTER TABLE `fleet`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `fleet_gallery`
--
ALTER TABLE `fleet_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fleet_id` (`fleet_id`);

--
-- Tablo için indeksler `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Tablo için indeksler `gallery_categories`
--
ALTER TABLE `gallery_categories`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `statistics`
--
ALTER TABLE `statistics`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `about`
--
ALTER TABLE `about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `careers`
--
ALTER TABLE `careers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `career_applications`
--
ALTER TABLE `career_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `contact_form_fields`
--
ALTER TABLE `contact_form_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `country_flags`
--
ALTER TABLE `country_flags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tablo için AUTO_INCREMENT değeri `fleet`
--
ALTER TABLE `fleet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `fleet_gallery`
--
ALTER TABLE `fleet_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `gallery_categories`
--
ALTER TABLE `gallery_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `statistics`
--
ALTER TABLE `statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `team`
--
ALTER TABLE `team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `career_applications`
--
ALTER TABLE `career_applications`
  ADD CONSTRAINT `career_applications_ibfk_1` FOREIGN KEY (`career_id`) REFERENCES `careers` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `fleet_gallery`
--
ALTER TABLE `fleet_gallery`
  ADD CONSTRAINT `fleet_gallery_ibfk_1` FOREIGN KEY (`fleet_id`) REFERENCES `fleet` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `gallery_categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

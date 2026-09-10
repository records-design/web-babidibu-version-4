-- Babidibu Records CMS — Schema
-- Importar desde phpMyAdmin: pestaña SQL → pegar todo → Ejecutar

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin','editor') DEFAULT 'editor',
  `nombre` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lanzamientos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL,
  `artista` VARCHAR(255) NOT NULL,
  `youtube_id` VARCHAR(50) NOT NULL,
  `orden` INT DEFAULT 0,
  `publicado` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `artistas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `bio` TEXT,
  `num` VARCHAR(10),
  `tags` VARCHAR(255),
  `foto` VARCHAR(255),
  `foto_carousel` VARCHAR(255),
  `logo` VARCHAR(255),
  `spotify_embed` VARCHAR(500),
  `link_spotify` VARCHAR(255),
  `link_youtube` VARCHAR(255),
  `link_instagram` VARCHAR(255),
  `link_tiktok` VARCHAR(255),
  `color_g1` VARCHAR(20) DEFAULT '#8B5CF6',
  `color_g2` VARCHAR(20) DEFAULT '#60A5FA',
  `photo_pos` VARCHAR(50),
  `photo_scale` VARCHAR(10),
  `orden` INT DEFAULT 0,
  `publicado` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hero_slides` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `imagen` VARCHAR(255) NOT NULL,
  `alt` VARCHAR(255),
  `nombre` VARCHAR(255),
  `subtitulo` VARCHAR(255) DEFAULT 'Artistas · Babidibu Records',
  `color` VARCHAR(20) DEFAULT '#F6D51F',
  `orden` INT DEFAULT 0,
  `publicado` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

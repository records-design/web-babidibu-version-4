<?php
// Instalador del CMS Babidibu Records
// Correr UNA sola vez en: tudominio.com/setup/install.php

$config = [
  'host' => 'localhost',
  'db'   => 'babidibu_cms',
  'user' => 'root',       // cambiar por usuario de Hostinger
  'pass' => '',           // cambiar por contraseña de Hostinger
  'admin_email' => 'info@babidiburecords.com',
  'admin_pass'  => 'BabidibuAdmin2024!', // cambiar después del primer login
];

try {
  $pdo = new PDO("mysql:host={$config['host']};charset=utf8mb4", $config['user'], $config['pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
  $pdo->exec("USE `{$config['db']}`");

  $pdo->exec("
    CREATE TABLE IF NOT EXISTS usuarios (
      id INT AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(255) UNIQUE NOT NULL,
      password VARCHAR(255) NOT NULL,
      rol ENUM('admin','editor') DEFAULT 'editor',
      nombre VARCHAR(100),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
  ");

  $pdo->exec("
    CREATE TABLE IF NOT EXISTS lanzamientos (
      id INT AUTO_INCREMENT PRIMARY KEY,
      titulo VARCHAR(255) NOT NULL,
      artista VARCHAR(255) NOT NULL,
      youtube_id VARCHAR(50) NOT NULL,
      orden INT DEFAULT 0,
      publicado TINYINT(1) DEFAULT 1,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
  ");

  $pdo->exec("
    CREATE TABLE IF NOT EXISTS artistas (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nombre VARCHAR(255) NOT NULL,
      slug VARCHAR(255) UNIQUE NOT NULL,
      bio TEXT,
      num VARCHAR(10),
      tags VARCHAR(255),
      foto VARCHAR(255),
      foto_carousel VARCHAR(255),
      logo VARCHAR(255),
      spotify_embed VARCHAR(500),
      link_spotify VARCHAR(255),
      link_youtube VARCHAR(255),
      link_instagram VARCHAR(255),
      link_tiktok VARCHAR(255),
      color_g1 VARCHAR(20) DEFAULT '#8B5CF6',
      color_g2 VARCHAR(20) DEFAULT '#60A5FA',
      photo_pos VARCHAR(50),
      photo_scale VARCHAR(10),
      orden INT DEFAULT 0,
      publicado TINYINT(1) DEFAULT 1,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
  ");

  $pdo->exec("
    CREATE TABLE IF NOT EXISTS babidibu_tv (
      id INT AUTO_INCREMENT PRIMARY KEY,
      titulo VARCHAR(255) NOT NULL,
      artista VARCHAR(255) NOT NULL,
      youtube_id VARCHAR(50) NOT NULL,
      orden INT DEFAULT 0,
      publicado TINYINT(1) DEFAULT 1,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
  ");

  $pdo->exec("
    CREATE TABLE IF NOT EXISTS hero_slides (
      id INT AUTO_INCREMENT PRIMARY KEY,
      imagen VARCHAR(255) NOT NULL,
      alt VARCHAR(255),
      nombre VARCHAR(255),
      subtitulo VARCHAR(255) DEFAULT 'Artistas · Babidibu Records',
      color VARCHAR(20) DEFAULT '#F6D51F',
      orden INT DEFAULT 0,
      publicado TINYINT(1) DEFAULT 1,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
  ");

  // Crear usuario admin
  $hash = password_hash($config['admin_pass'], PASSWORD_BCRYPT);
  $stmt = $pdo->prepare("INSERT IGNORE INTO usuarios (email, password, rol, nombre) VALUES (?, ?, 'admin', 'Admin')");
  $stmt->execute([$config['admin_email'], $hash]);

  // Datos de ejemplo
  $pdo->exec("INSERT IGNORE INTO lanzamientos (titulo, artista, youtube_id, orden) VALUES
    ('La Sirenita - Cuentos infantiles', 'El Mundo de Chani', '3z8yO5MOOAk', 1),
    ('Las Hinchadas del Mundial', 'El Mundo de Chani', 'WV40kNYnLxY', 2),
    ('Manualidades de Argentina', 'El Mundo de Chani', 'XhBg5kpoj8E', 3)
  ");

  echo '<h2 style="color:green;font-family:sans-serif">Instalacion exitosa</h2>
  <p style="font-family:sans-serif">Base de datos creada correctamente.</p>
  <p style="font-family:sans-serif"><strong>Email:</strong> '.$config['admin_email'].'</p>
  <p style="font-family:sans-serif"><strong>Password:</strong> '.$config['admin_pass'].'</p>
  <p style="color:red;font-family:sans-serif"><strong>Elimina este archivo del servidor despues de instalar.</strong></p>
  <p style="font-family:sans-serif"><a href="../php-admin/login.php">Ir al panel</a></p>';

} catch (Exception $e) {
  echo '<h2 style="color:red;font-family:sans-serif">Error</h2><p style="font-family:sans-serif">'.$e->getMessage().'</p>';
}

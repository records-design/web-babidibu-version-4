<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'babidibu_cms');
define('DB_USER', 'root');       // cambiar por usuario de Hostinger
define('DB_PASS', '');           // cambiar por contraseña de Hostinger
define('DB_CHARSET', 'utf8mb4');

function db(): PDO {
  static $pdo = null;
  if ($pdo === null) {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }
  return $pdo;
}

function auth(): void {
  session_start();
  if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
  }
}

function csrf_token(): string {
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf'];
}

function csrf_check(): void {
  if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    http_response_code(403); die('Token inválido');
  }
}

function is_admin(): bool {
  return ($_SESSION['rol'] ?? '') === 'admin';
}

$UPLOAD_DIR = __DIR__ . '/../imagenes-babidibu-records/cms/';
$UPLOAD_URL = '../imagenes-babidibu-records/cms/';
if (!is_dir($UPLOAD_DIR)) mkdir($UPLOAD_DIR, 0755, true);

<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php-admin/config.php';

try {
  $stmt = db()->query("SELECT titulo, artista, youtube_id FROM lanzamientos WHERE publicado=1 ORDER BY orden ASC, id ASC");
  echo json_encode(['ok' => true, 'data' => $stmt->fetchAll()]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['ok' => false]);
}

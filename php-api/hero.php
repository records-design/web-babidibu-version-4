<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php-admin/config.php';

try {
  $stmt = db()->query("SELECT imagen, alt FROM hero_slides WHERE publicado=1 ORDER BY orden ASC, id ASC");
  $rows = $stmt->fetchAll();
  $upload_url = '../imagenes-babidibu-records/cms/';
  foreach ($rows as &$r) {
    $r['imagen'] = $upload_url . $r['imagen'];
  }
  echo json_encode(['ok' => true, 'data' => $rows]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['ok' => false]);
}

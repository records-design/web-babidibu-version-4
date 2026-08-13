<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../php-admin/config.php';

try {
  $stmt = db()->query("SELECT nombre, slug, bio, num, tags, foto, foto_carousel, logo, spotify_embed, link_spotify, link_youtube, link_instagram, link_tiktok, color_g1, color_g2, photo_pos, photo_scale FROM artistas WHERE publicado=1 ORDER BY orden ASC, nombre ASC");
  $rows = $stmt->fetchAll();
  $upload_url = '../imagenes-babidibu-records/cms/';
  foreach ($rows as &$r) {
    if ($r['foto'])          $r['foto']          = $upload_url . $r['foto'];
    if ($r['foto_carousel']) $r['foto_carousel']  = $upload_url . $r['foto_carousel'];
    if ($r['logo'])          $r['logo']           = $upload_url . $r['logo'];
  }
  echo json_encode(['ok' => true, 'data' => $rows]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['ok' => false]);
}

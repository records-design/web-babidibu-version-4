<?php
require_once 'config.php';
auth();
require_once 'layout.php';
global $UPLOAD_DIR, $UPLOAD_URL;

$msg = '';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

function slugify(string $s): string {
  $s = strtolower(trim($s));
  $s = preg_replace('/[áàä]/u','a',$s); $s = preg_replace('/[éèë]/u','e',$s);
  $s = preg_replace('/[íìï]/u','i',$s); $s = preg_replace('/[óòö]/u','o',$s);
  $s = preg_replace('/[úùü]/u','u',$s); $s = str_replace('ñ','n',$s);
  return preg_replace('/[^a-z0-9]+/','-',trim($s,'-'));
}

function upload_img(string $field, string $dir): ?string {
  if (empty($_FILES[$field]['tmp_name'])) return null;
  $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
  if (!in_array($ext,['jpg','jpeg','png','webp'])) return null;
  $name = uniqid().'.'.$ext;
  if (!is_dir($dir)) mkdir($dir, 0755, true);
  move_uploaded_file($_FILES[$field]['tmp_name'], $dir.$name);
  return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $nombre   = trim($_POST['nombre'] ?? '');
  $bio      = trim($_POST['bio'] ?? '');
  $slug     = slugify($nombre);
  $orden    = (int)($_POST['orden'] ?? 0);
  $publicado = isset($_POST['publicado']) ? 1 : 0;
  $link_spotify   = trim($_POST['link_spotify'] ?? '');
  $link_youtube   = trim($_POST['link_youtube'] ?? '');
  $link_instagram = trim($_POST['link_instagram'] ?? '');

  if ($_POST['form_action'] === 'delete' && $id) {
    db()->prepare("DELETE FROM artistas WHERE id=?")->execute([$id]);
    header('Location: artistas.php?ok=borrado'); exit;
  }

  if ($nombre) {
    $foto = upload_img('foto', $UPLOAD_DIR);
    $foto_carousel = upload_img('foto_carousel', $UPLOAD_DIR);

    if ($id) {
      $stmt = db()->prepare("SELECT foto, foto_carousel FROM artistas WHERE id=?");
      $stmt->execute([$id]);
      $old = $stmt->fetch();
      $foto = $foto ?? $old['foto'];
      $foto_carousel = $foto_carousel ?? $old['foto_carousel'];
      db()->prepare("UPDATE artistas SET nombre=?,slug=?,bio=?,foto=?,foto_carousel=?,link_spotify=?,link_youtube=?,link_instagram=?,orden=?,publicado=? WHERE id=?")
         ->execute([$nombre,$slug,$bio,$foto,$foto_carousel,$link_spotify,$link_youtube,$link_instagram,$orden,$publicado,$id]);
    } else {
      db()->prepare("INSERT INTO artistas (nombre,slug,bio,foto,foto_carousel,link_spotify,link_youtube,link_instagram,orden,publicado) VALUES (?,?,?,?,?,?,?,?,?,?)")
         ->execute([$nombre,$slug,$bio,$foto,$foto_carousel,$link_spotify,$link_youtube,$link_instagram,$orden,$publicado]);
    }
    header('Location: artistas.php?ok=1'); exit;
  }
  $msg = 'El nombre es obligatorio.';
}

$ok = $_GET['ok'] ?? '';
$row = null;
if ($action === 'edit' && $id) {
  $stmt = db()->prepare("SELECT * FROM artistas WHERE id=?");
  $stmt->execute([$id]);
  $row = $stmt->fetch();
}

layout_head('Artistas');
layout_sidebar('artistas');
?>
<div class="main">
  <div class="topbar">
    <div class="topbar-title">Artistas</div>
  </div>
  <div class="content">

<?php if ($ok): ?>
  <div class="alert alert-verde"><?= $ok === 'borrado' ? 'Artista eliminado.' : 'Artista guardado correctamente.' ?></div>
<?php endif; ?>
<?php if ($msg): ?>
  <div class="alert alert-coral"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($action === 'new' || $action === 'edit'): ?>
  <div class="card">
    <div class="card-title"><?= $action === 'edit' ? 'Editar artista' : 'Nuevo artista' ?></div>
    <form method="POST" enctype="multipart/form-data" action="artistas.php<?= $id ? "?action=edit&id=$id" : '?action=new' ?>">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="form_action" value="save">
      <div class="form-row">
        <div class="form-group">
          <label>Nombre *</label>
          <input type="text" name="nombre" required value="<?= htmlspecialchars($row['nombre'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Orden</label>
          <input type="number" name="orden" value="<?= $row['orden'] ?? 0 ?>" min="0">
        </div>
      </div>
      <div class="form-group">
        <label>Bio</label>
        <textarea name="bio"><?= htmlspecialchars($row['bio'] ?? '') ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Foto de perfil</label>
          <input type="file" name="foto" accept="image/*">
          <?php if (!empty($row['foto'])): ?>
            <img src="<?= $UPLOAD_URL . htmlspecialchars($row['foto']) ?>" style="height:60px;margin-top:8px;border-radius:8px;">
          <?php endif; ?>
        </div>
        <div class="form-group">
          <label>Foto para carrusel</label>
          <input type="file" name="foto_carousel" accept="image/*">
          <?php if (!empty($row['foto_carousel'])): ?>
            <img src="<?= $UPLOAD_URL . htmlspecialchars($row['foto_carousel']) ?>" style="height:60px;margin-top:8px;border-radius:8px;">
          <?php endif; ?>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Link Spotify</label>
          <input type="text" name="link_spotify" value="<?= htmlspecialchars($row['link_spotify'] ?? '') ?>" placeholder="https://open.spotify.com/artist/...">
        </div>
        <div class="form-group">
          <label>Link YouTube</label>
          <input type="text" name="link_youtube" value="<?= htmlspecialchars($row['link_youtube'] ?? '') ?>" placeholder="https://youtube.com/@...">
        </div>
      </div>
      <div class="form-group">
        <label>Link Instagram</label>
        <input type="text" name="link_instagram" value="<?= htmlspecialchars($row['link_instagram'] ?? '') ?>" placeholder="https://instagram.com/...">
      </div>
      <div class="form-group">
        <label><input type="checkbox" name="publicado" <?= ($row['publicado'] ?? 1) ? 'checked' : '' ?>> &nbsp;Publicado</label>
      </div>
      <div style="display:flex;gap:12px;align-items:center;">
        <button type="submit" class="btn btn-verde">Guardar</button>
        <a href="artistas.php" class="btn btn-outline">Cancelar</a>
        <?php if ($id): ?>
          <form method="POST" action="artistas.php?action=edit&id=<?= $id ?>" style="margin:0" onsubmit="return confirm('Eliminar este artista?')">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="form_action" value="delete">
            <button type="submit" class="btn btn-coral btn-sm">Eliminar</button>
          </form>
        <?php endif; ?>
      </div>
    </form>
  </div>

<?php else: ?>
  <div class="actions">
    <div></div>
    <a href="artistas.php?action=new" class="btn btn-verde">+ Nuevo artista</a>
  </div>
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Foto</th><th>Nombre</th><th>Orden</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
          <?php
          $rows = db()->query("SELECT * FROM artistas ORDER BY orden ASC, nombre ASC")->fetchAll();
          foreach ($rows as $r):
          ?>
          <tr>
            <td>
              <?php if ($r['foto']): ?>
                <img class="thumb" src="<?= $UPLOAD_URL . htmlspecialchars($r['foto']) ?>" alt="">
              <?php else: ?>
                <div class="thumb" style="display:flex;align-items:center;justify-content:center;background:#F3F4F6;">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M6 20v-1a6 6 0 0112 0v1"/></svg>
                </div>
              <?php endif; ?>
            </td>
            <td style="font-weight:700"><?= htmlspecialchars($r['nombre']) ?></td>
            <td><?= $r['orden'] ?></td>
            <td><span class="badge <?= $r['publicado'] ? 'badge-verde' : 'badge-coral' ?>"><?= $r['publicado'] ? 'Publicado' : 'Oculto' ?></span></td>
            <td><a href="artistas.php?action=edit&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">Editar</a></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?>
          <tr><td colspan="5"><div class="empty"><p>No hay artistas todavia.</p></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

  </div>
</div>
<?php layout_foot(); ?>

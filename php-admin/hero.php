<?php
require_once 'config.php';
auth();
require_once 'layout.php';
global $UPLOAD_DIR, $UPLOAD_URL;

$msg = '';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $nombre    = trim($_POST['nombre'] ?? '');
  $subtitulo = trim($_POST['subtitulo'] ?? '');
  $color     = trim($_POST['color'] ?? '#F6D51F');
  $alt       = trim($_POST['alt'] ?? '');
  $orden     = (int)($_POST['orden'] ?? 0);
  $publicado = isset($_POST['publicado']) ? 1 : 0;

  if ($_POST['form_action'] === 'delete' && $id) {
    db()->prepare("DELETE FROM hero_slides WHERE id=?")->execute([$id]);
    header('Location: hero.php?ok=borrado'); exit;
  }

  $imagen = null;
  if (!empty($_FILES['imagen']['tmp_name'])) {
    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    if (in_array($ext,['jpg','jpeg','png','webp'])) {
      $name = 'hero_'.uniqid().'.'.$ext;
      if (!is_dir($UPLOAD_DIR)) mkdir($UPLOAD_DIR, 0755, true);
      move_uploaded_file($_FILES['imagen']['tmp_name'], $UPLOAD_DIR.$name);
      $imagen = $name;
    }
  }

  if ($id) {
    $stmt = db()->prepare("SELECT imagen FROM hero_slides WHERE id=?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();
    $imagen = $imagen ?? $old['imagen'];
    db()->prepare("UPDATE hero_slides SET imagen=?,alt=?,nombre=?,subtitulo=?,color=?,orden=?,publicado=? WHERE id=?")
       ->execute([$imagen,$alt,$nombre,$subtitulo,$color,$orden,$publicado,$id]);
    header('Location: hero.php?ok=1'); exit;
  } elseif ($imagen) {
    db()->prepare("INSERT INTO hero_slides (imagen,alt,nombre,subtitulo,color,orden,publicado) VALUES (?,?,?,?,?,?,?)")
       ->execute([$imagen,$alt,$nombre,$subtitulo,$color,$orden,$publicado]);
    header('Location: hero.php?ok=1'); exit;
  } else {
    $msg = 'Tenes que subir una imagen.';
  }
}

$ok = $_GET['ok'] ?? '';
$row = null;
if ($action === 'edit' && $id) {
  $stmt = db()->prepare("SELECT * FROM hero_slides WHERE id=?");
  $stmt->execute([$id]);
  $row = $stmt->fetch();
}

layout_head('Hero');
layout_sidebar('hero');
?>
<div class="main">
  <div class="topbar">
    <div class="topbar-title">Hero — Carrusel de imagenes</div>
  </div>
  <div class="content">

<?php if ($ok): ?>
  <div class="alert alert-verde"><?= $ok === 'borrado' ? 'Slide eliminado.' : 'Slide guardado correctamente.' ?></div>
<?php endif; ?>
<?php if ($msg): ?>
  <div class="alert alert-coral"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($action === 'new' || $action === 'edit'): ?>
  <div class="card">
    <div class="card-title"><?= $action === 'edit' ? 'Editar slide' : 'Nuevo slide' ?></div>
    <form method="POST" enctype="multipart/form-data" action="hero.php<?= $id ? "?action=edit&id=$id" : '?action=new' ?>">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="form_action" value="save">
      <div class="form-group">
        <label>Foto del artista <?= $action === 'new' ? '*' : '' ?></label>
        <input type="file" name="imagen" accept="image/*" <?= $action === 'new' ? 'required' : '' ?>>
        <div class="form-hint">JPG, PNG o WebP. Se muestra en la card del hero.</div>
        <?php if (!empty($row['imagen'])): ?>
          <img src="<?= $UPLOAD_URL . htmlspecialchars($row['imagen']) ?>" style="height:120px;margin-top:12px;border-radius:10px;object-fit:cover;">
        <?php endif; ?>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Nombre del artista *</label>
          <input type="text" name="nombre" value="<?= htmlspecialchars($row['nombre'] ?? '') ?>" placeholder="ej: Las Trillizas de Oro" required>
        </div>
        <div class="form-group">
          <label>Subtitulo</label>
          <input type="text" name="subtitulo" value="<?= htmlspecialchars($row['subtitulo'] ?? 'Artistas · Babidibu Records') ?>" placeholder="ej: Artistas · Babidibu Records">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Color de la card</label>
          <input type="color" name="color" value="<?= htmlspecialchars($row['color'] ?? '#F6D51F') ?>">
          <div class="form-hint">Color de fondo de la card en el hero.</div>
        </div>
        <div class="form-group">
          <label>Descripcion de la imagen (alt)</label>
          <input type="text" name="alt" value="<?= htmlspecialchars($row['alt'] ?? '') ?>" placeholder="ej: Foto de Las Trillizas de Oro">
        </div>
        <div class="form-group">
          <label>Orden</label>
          <input type="number" name="orden" value="<?= $row['orden'] ?? 0 ?>" min="0">
        </div>
      </div>
      <div class="form-group">
        <label><input type="checkbox" name="publicado" <?= ($row['publicado'] ?? 1) ? 'checked' : '' ?>> &nbsp;Publicado</label>
      </div>
      <div style="display:flex;gap:12px;align-items:center;">
        <button type="submit" class="btn btn-verde">Guardar</button>
        <a href="hero.php" class="btn btn-outline">Cancelar</a>
        <?php if ($id): ?>
          <form method="POST" action="hero.php?action=edit&id=<?= $id ?>" style="margin:0" onsubmit="return confirm('Eliminar este slide?')">
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
    <a href="hero.php?action=new" class="btn btn-verde">+ Nuevo slide</a>
  </div>
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Imagen</th><th>Artista</th><th>Subtitulo</th><th>Orden</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
          <?php
          $rows = db()->query("SELECT * FROM hero_slides ORDER BY orden ASC, id ASC")->fetchAll();
          foreach ($rows as $r):
          ?>
          <tr>
            <td><img class="thumb" src="<?= $UPLOAD_URL . htmlspecialchars($r['imagen']) ?>" alt=""></td>
            <td><?= htmlspecialchars($r['nombre'] ?: '—') ?></td>
            <td><?= htmlspecialchars($r['subtitulo'] ?: '—') ?></td>
            <td><?= $r['orden'] ?></td>
            <td><span class="badge <?= $r['publicado'] ? 'badge-verde' : 'badge-coral' ?>"><?= $r['publicado'] ? 'Publicado' : 'Oculto' ?></span></td>
            <td><a href="hero.php?action=edit&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">Editar</a></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?>
          <tr><td colspan="5"><div class="empty"><p>No hay slides todavia.</p></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

  </div>
</div>
<?php layout_foot(); ?>

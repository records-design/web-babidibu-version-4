<?php
require_once 'config.php';
auth();
require_once 'layout.php';

$msg = '';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $titulo    = trim($_POST['titulo'] ?? '');
  $artista   = trim($_POST['artista'] ?? '');
  $youtube_id = trim($_POST['youtube_id'] ?? '');
  $orden     = (int)($_POST['orden'] ?? 0);
  $publicado = isset($_POST['publicado']) ? 1 : 0;

  if ($_POST['form_action'] === 'delete' && $id) {
    db()->prepare("DELETE FROM babidibu_tv WHERE id=?")->execute([$id]);
    header('Location: babidibu-tv.php?ok=borrado'); exit;
  }

  if ($titulo && $artista && $youtube_id) {
    if ($id) {
      db()->prepare("UPDATE babidibu_tv SET titulo=?,artista=?,youtube_id=?,orden=?,publicado=? WHERE id=?")
         ->execute([$titulo,$artista,$youtube_id,$orden,$publicado,$id]);
    } else {
      db()->prepare("INSERT INTO babidibu_tv (titulo,artista,youtube_id,orden,publicado) VALUES (?,?,?,?,?)")
         ->execute([$titulo,$artista,$youtube_id,$orden,$publicado]);
    }
    header('Location: babidibu-tv.php?ok=1'); exit;
  }
  $msg = 'Completa todos los campos obligatorios.';
}

$ok = $_GET['ok'] ?? '';
$row = null;
if ($action === 'edit' && $id) {
  $stmt = db()->prepare("SELECT * FROM babidibu_tv WHERE id=?");
  $stmt->execute([$id]);
  $row = $stmt->fetch();
}

layout_head('Babidibu TV');
layout_sidebar('tv');
?>
<div class="main">
  <div class="topbar">
    <div class="topbar-title">Babidibu TV</div>
  </div>
  <div class="content">

<?php if ($ok): ?>
  <div class="alert alert-verde"><?= $ok === 'borrado' ? 'Video eliminado.' : 'Video guardado correctamente.' ?></div>
<?php endif; ?>
<?php if ($msg): ?>
  <div class="alert alert-coral"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($action === 'new' || $action === 'edit'): ?>
  <div class="card">
    <div class="card-title"><?= $action === 'edit' ? 'Editar video' : 'Nuevo video' ?></div>
    <form method="POST" action="babidibu-tv.php<?= $id ? "?action=edit&id=$id" : '?action=new' ?>">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="form_action" value="save">
      <div class="form-row">
        <div class="form-group">
          <label>Titulo *</label>
          <input type="text" name="titulo" required value="<?= htmlspecialchars($row['titulo'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Artista / Canal *</label>
          <input type="text" name="artista" required value="<?= htmlspecialchars($row['artista'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>ID de YouTube *</label>
          <input type="text" name="youtube_id" required value="<?= htmlspecialchars($row['youtube_id'] ?? '') ?>" placeholder="ej: dQw4w9WgXcQ">
          <div class="form-hint">El codigo despues de ?v= en la URL.</div>
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
        <a href="babidibu-tv.php" class="btn btn-outline">Cancelar</a>
        <?php if ($id): ?>
          <form method="POST" action="babidibu-tv.php?action=edit&id=<?= $id ?>" style="margin:0" onsubmit="return confirm('Eliminar este video?')">
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
    <a href="babidibu-tv.php?action=new" class="btn btn-verde">+ Nuevo video</a>
  </div>
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Video</th><th>Titulo</th><th>Artista</th><th>Orden</th><th>Estado</th><th>Acciones</th></tr>
        </thead>
        <tbody>
          <?php
          $rows = db()->query("SELECT * FROM babidibu_tv ORDER BY orden ASC, id ASC")->fetchAll();
          foreach ($rows as $r):
          ?>
          <tr>
            <td><img class="thumb" src="https://img.youtube.com/vi/<?= htmlspecialchars($r['youtube_id']) ?>/mqdefault.jpg" alt=""></td>
            <td style="font-weight:700"><?= htmlspecialchars($r['titulo']) ?></td>
            <td><?= htmlspecialchars($r['artista']) ?></td>
            <td><?= $r['orden'] ?></td>
            <td><span class="badge <?= $r['publicado'] ? 'badge-verde' : 'badge-coral' ?>"><?= $r['publicado'] ? 'Publicado' : 'Oculto' ?></span></td>
            <td><a href="babidibu-tv.php?action=edit&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">Editar</a></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?>
          <tr><td colspan="6"><div class="empty"><p>No hay videos todavia.</p></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

  </div>
</div>
<?php layout_foot(); ?>

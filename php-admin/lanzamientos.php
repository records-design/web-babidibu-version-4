<?php
require_once 'config.php';
auth();
require_once 'layout.php';

$msg = '';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $titulo     = trim($_POST['titulo'] ?? '');
  $artista    = trim($_POST['artista'] ?? '');
  $youtube_id = trim($_POST['youtube_id'] ?? '');
  $orden      = (int)($_POST['orden'] ?? 0);
  $publicado  = isset($_POST['publicado']) ? 1 : 0;

  if ($_POST['form_action'] === 'delete' && $id) {
    db()->prepare("DELETE FROM lanzamientos WHERE id=?")->execute([$id]);
    header('Location: lanzamientos.php?ok=borrado'); exit;
  }

  if ($titulo && $artista && $youtube_id) {
    if ($id) {
      db()->prepare("UPDATE lanzamientos SET titulo=?,artista=?,youtube_id=?,orden=?,publicado=? WHERE id=?")
         ->execute([$titulo,$artista,$youtube_id,$orden,$publicado,$id]);
    } else {
      db()->prepare("INSERT INTO lanzamientos (titulo,artista,youtube_id,orden,publicado) VALUES (?,?,?,?,?)")
         ->execute([$titulo,$artista,$youtube_id,$orden,$publicado]);
    }
    header('Location: lanzamientos.php?ok=1'); exit;
  }
  $msg = 'Completa todos los campos obligatorios.';
}

$ok = $_GET['ok'] ?? '';
$row = null;
if ($action === 'edit' && $id) {
  $row = db()->prepare("SELECT * FROM lanzamientos WHERE id=?")->execute([$id]) ? db()->prepare("SELECT * FROM lanzamientos WHERE id=?")->execute([$id]) : null;
  $stmt = db()->prepare("SELECT * FROM lanzamientos WHERE id=?");
  $stmt->execute([$id]);
  $row = $stmt->fetch();
}

layout_head('Lanzamientos');
layout_sidebar('lanzamientos');
?>
<div class="main">
  <div class="topbar">
    <div class="topbar-title">Lanzamientos</div>
  </div>
  <div class="content">

<?php if ($ok): ?>
  <div class="alert alert-verde"><?= $ok === 'borrado' ? 'Lanzamiento eliminado.' : 'Lanzamiento guardado correctamente.' ?></div>
<?php endif; ?>
<?php if ($msg): ?>
  <div class="alert alert-coral"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($action === 'new' || $action === 'edit'): ?>
  <div class="card">
    <div class="card-title"><?= $action === 'edit' ? 'Editar lanzamiento' : 'Nuevo lanzamiento' ?></div>
    <form method="POST" action="lanzamientos.php<?= $id ? "?action=edit&id=$id" : '?action=new' ?>">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="form_action" value="save">
      <div class="form-row">
        <div class="form-group">
          <label>Titulo *</label>
          <input type="text" name="titulo" required value="<?= htmlspecialchars($row['titulo'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Artista *</label>
          <input type="text" name="artista" required value="<?= htmlspecialchars($row['artista'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>ID de YouTube *</label>
          <input type="text" name="youtube_id" required value="<?= htmlspecialchars($row['youtube_id'] ?? '') ?>" placeholder="ej: 3z8yO5MOOAk">
          <div class="form-hint">Es el codigo despues de ?v= en la URL del video.</div>
        </div>
        <div class="form-group">
          <label>Orden</label>
          <input type="number" name="orden" value="<?= $row['orden'] ?? 0 ?>" min="0">
        </div>
      </div>
      <div class="form-group">
        <label>
          <input type="checkbox" name="publicado" <?= ($row['publicado'] ?? 1) ? 'checked' : '' ?>>
          &nbsp;Publicado
        </label>
      </div>
      <div style="display:flex;gap:12px;align-items:center;">
        <button type="submit" class="btn btn-verde">Guardar</button>
        <a href="lanzamientos.php" class="btn btn-outline">Cancelar</a>
        <?php if ($id): ?>
          <form method="POST" action="lanzamientos.php?action=edit&id=<?= $id ?>" style="margin:0" onsubmit="return confirm('Eliminar este lanzamiento?')">
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
    <a href="lanzamientos.php?action=new" class="btn btn-verde">+ Nuevo lanzamiento</a>
  </div>
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Video</th>
            <th>Titulo</th>
            <th>Artista</th>
            <th>Orden</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $rows = db()->query("SELECT * FROM lanzamientos ORDER BY orden ASC, id ASC")->fetchAll();
          foreach ($rows as $r):
          ?>
          <tr>
            <td><img class="thumb" src="https://img.youtube.com/vi/<?= htmlspecialchars($r['youtube_id']) ?>/mqdefault.jpg" alt=""></td>
            <td style="font-weight:700"><?= htmlspecialchars($r['titulo']) ?></td>
            <td><?= htmlspecialchars($r['artista']) ?></td>
            <td><?= $r['orden'] ?></td>
            <td><span class="badge <?= $r['publicado'] ? 'badge-verde' : 'badge-coral' ?>"><?= $r['publicado'] ? 'Publicado' : 'Oculto' ?></span></td>
            <td><a href="lanzamientos.php?action=edit&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">Editar</a></td>
          </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?>
          <tr><td colspan="6"><div class="empty"><div class="empty-icon"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg></div><p>No hay lanzamientos todavia.</p></div></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

  </div>
</div>
<?php layout_foot(); ?>

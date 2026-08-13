<?php
require_once 'config.php';
auth();
if (!is_admin()) { header('Location: index.php'); exit; }
require_once 'layout.php';

$msg = '';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $email  = trim($_POST['email'] ?? '');
  $nombre = trim($_POST['nombre'] ?? '');
  $rol    = in_array($_POST['rol'] ?? '', ['admin','editor']) ? $_POST['rol'] : 'editor';
  $pass   = $_POST['password'] ?? '';

  if ($_POST['form_action'] === 'delete' && $id && $id !== (int)$_SESSION['user_id']) {
    db()->prepare("DELETE FROM usuarios WHERE id=?")->execute([$id]);
    header('Location: usuarios.php?ok=borrado'); exit;
  }

  if ($email && $nombre) {
    if ($id) {
      if ($pass) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        db()->prepare("UPDATE usuarios SET email=?,nombre=?,rol=?,password=? WHERE id=?")
           ->execute([$email,$nombre,$rol,$hash,$id]);
      } else {
        db()->prepare("UPDATE usuarios SET email=?,nombre=?,rol=? WHERE id=?")
           ->execute([$email,$nombre,$rol,$id]);
      }
    } else {
      if (!$pass) { $msg = 'La contraseña es obligatoria para nuevos usuarios.'; goto render; }
      $hash = password_hash($pass, PASSWORD_BCRYPT);
      db()->prepare("INSERT INTO usuarios (email,nombre,rol,password) VALUES (?,?,?,?)")
         ->execute([$email,$nombre,$rol,$hash]);
    }
    header('Location: usuarios.php?ok=1'); exit;
  }
  $msg = 'Email y nombre son obligatorios.';
}

render:
$ok = $_GET['ok'] ?? '';
$row = null;
if ($action === 'edit' && $id) {
  $stmt = db()->prepare("SELECT * FROM usuarios WHERE id=?");
  $stmt->execute([$id]);
  $row = $stmt->fetch();
}

layout_head('Usuarios');
layout_sidebar('usuarios');
?>
<div class="main">
  <div class="topbar">
    <div class="topbar-title">Usuarios</div>
  </div>
  <div class="content">

<?php if ($ok): ?>
  <div class="alert alert-verde"><?= $ok === 'borrado' ? 'Usuario eliminado.' : 'Usuario guardado.' ?></div>
<?php endif; ?>
<?php if ($msg): ?>
  <div class="alert alert-coral"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($action === 'new' || $action === 'edit'): ?>
  <div class="card">
    <div class="card-title"><?= $action === 'edit' ? 'Editar usuario' : 'Nuevo usuario' ?></div>
    <form method="POST" action="usuarios.php<?= $id ? "?action=edit&id=$id" : '?action=new' ?>">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="form_action" value="save">
      <div class="form-row">
        <div class="form-group">
          <label>Nombre *</label>
          <input type="text" name="nombre" required value="<?= htmlspecialchars($row['nombre'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" required value="<?= htmlspecialchars($row['email'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Contraseña <?= $action === 'edit' ? '(dejar vacio para no cambiar)' : '*' ?></label>
          <input type="password" name="password" <?= $action === 'new' ? 'required' : '' ?>>
        </div>
        <div class="form-group">
          <label>Rol</label>
          <select name="rol">
            <option value="editor" <?= ($row['rol'] ?? '') === 'editor' ? 'selected' : '' ?>>Editor</option>
            <option value="admin"  <?= ($row['rol'] ?? '') === 'admin'  ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>
      </div>
      <div style="display:flex;gap:12px;align-items:center;">
        <button type="submit" class="btn btn-verde">Guardar</button>
        <a href="usuarios.php" class="btn btn-outline">Cancelar</a>
        <?php if ($id && $id !== (int)$_SESSION['user_id']): ?>
          <form method="POST" action="usuarios.php?action=edit&id=<?= $id ?>" style="margin:0" onsubmit="return confirm('Eliminar este usuario?')">
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
    <a href="usuarios.php?action=new" class="btn btn-verde">+ Nuevo usuario</a>
  </div>
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th></tr>
        </thead>
        <tbody>
          <?php foreach (db()->query("SELECT * FROM usuarios ORDER BY nombre")->fetchAll() as $r): ?>
          <tr>
            <td style="font-weight:700"><?= htmlspecialchars($r['nombre']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><span class="badge <?= $r['rol'] === 'admin' ? 'badge-verde' : 'badge-muted' ?>"><?= $r['rol'] ?></span></td>
            <td><a href="usuarios.php?action=edit&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm">Editar</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

  </div>
</div>
<?php layout_foot(); ?>

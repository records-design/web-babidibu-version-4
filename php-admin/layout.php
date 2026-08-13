<?php
function layout_head(string $title): void { ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?> — Babidibu CMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --verde: #5FA55A;
      --turquesa: #01B4BC;
      --amarillo: #F6D51F;
      --naranja: #FA8925;
      --coral: #FA5457;
      --negro: #3A3F44;
      --bg: #F8F9FA;
      --white: #fff;
      --border: #E8EAED;
      --text-muted: #6B7280;
      --sidebar-w: 240px;
    }
    body { font-family: 'Nunito', sans-serif; background: var(--bg); color: var(--negro); min-height: 100vh; }

    /* Sidebar */
    .sidebar {
      position: fixed; top: 0; left: 0; width: var(--sidebar-w); height: 100vh;
      background: var(--negro); display: flex; flex-direction: column; z-index: 100;
    }
    .sidebar-logo {
      padding: 24px 20px 20px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .sidebar-logo img { height: 36px; width: auto; }
    .sidebar-label {
      font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.35);
      text-transform: uppercase; letter-spacing: 0.12em;
      padding: 20px 20px 8px;
    }
    .sidebar-nav { flex: 1; overflow-y: auto; padding: 4px 12px; }
    .sidebar-nav a {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 12px; border-radius: 10px; margin-bottom: 2px;
      color: rgba(255,255,255,0.65); font-size: 14px; font-weight: 600;
      text-decoration: none; transition: background 0.15s, color 0.15s;
    }
    .sidebar-nav a:hover { background: rgba(255,255,255,0.08); color: #fff; }
    .sidebar-nav a.active { background: var(--verde); color: #fff; }
    .sidebar-nav a svg { flex-shrink: 0; }
    .sidebar-footer {
      padding: 16px 20px;
      border-top: 1px solid rgba(255,255,255,0.08);
      font-size: 12px; color: rgba(255,255,255,0.4);
    }
    .sidebar-footer a { color: var(--coral); text-decoration: none; font-weight: 700; }

    /* Main */
    .main { margin-left: var(--sidebar-w); min-height: 100vh; }
    .topbar {
      background: var(--white); border-bottom: 1px solid var(--border);
      padding: 0 32px; height: 64px;
      display: flex; align-items: center; justify-content: space-between;
      position: sticky; top: 0; z-index: 50;
    }
    .topbar-title { font-size: 18px; font-weight: 800; color: var(--negro); }
    .topbar-user { font-size: 13px; color: var(--text-muted); font-weight: 600; }
    .content { padding: 32px; }

    /* Cards */
    .card {
      background: var(--white); border-radius: 16px;
      border: 1px solid var(--border); padding: 24px;
      margin-bottom: 24px;
    }
    .card-title { font-size: 16px; font-weight: 800; margin-bottom: 20px; color: var(--negro); }

    /* Table */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th { text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); border-bottom: 2px solid var(--border); }
    td { padding: 14px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #F8F9FA; }

    /* Badges */
    .badge { display: inline-block; padding: 3px 10px; border-radius: 100px; font-size: 11px; font-weight: 700; }
    .badge-verde { background: #E8F5E9; color: var(--verde); }
    .badge-coral { background: #FEE2E2; color: var(--coral); }
    .badge-muted { background: var(--border); color: var(--text-muted); }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 10px; font-family: 'Nunito', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
    .btn-verde { background: var(--verde); color: #fff; }
    .btn-verde:hover { background: #4d8f48; }
    .btn-coral { background: var(--coral); color: #fff; }
    .btn-coral:hover { background: #e03c3f; }
    .btn-outline { background: transparent; color: var(--negro); border: 1.5px solid var(--border); }
    .btn-outline:hover { border-color: var(--negro); }
    .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }

    /* Forms */
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--negro); }
    .form-group input[type=text],
    .form-group input[type=email],
    .form-group input[type=number],
    .form-group input[type=password],
    .form-group select,
    .form-group textarea {
      width: 100%; padding: 10px 14px; border: 1.5px solid var(--border);
      border-radius: 10px; font-family: 'Nunito', sans-serif; font-size: 14px;
      color: var(--negro); background: var(--white); transition: border 0.15s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { outline: none; border-color: var(--verde); }
    .form-group textarea { min-height: 100px; resize: vertical; }
    .form-hint { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    /* Alerts */
    .alert { padding: 12px 16px; border-radius: 10px; font-size: 14px; font-weight: 600; margin-bottom: 20px; }
    .alert-verde { background: #E8F5E9; color: #2E7D32; border: 1px solid #A5D6A7; }
    .alert-coral { background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; }

    /* Actions row */
    .actions { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .actions-right { display: flex; gap: 8px; }

    /* Toggle */
    .toggle { position: relative; display: inline-block; width: 40px; height: 22px; }
    .toggle input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; inset: 0; background: var(--border); border-radius: 22px; cursor: pointer; transition: 0.2s; }
    .toggle-slider::before { content: ''; position: absolute; width: 16px; height: 16px; left: 3px; top: 3px; background: white; border-radius: 50%; transition: 0.2s; }
    input:checked + .toggle-slider { background: var(--verde); }
    input:checked + .toggle-slider::before { transform: translateX(18px); }

    /* Drag handle */
    .drag-handle { cursor: grab; color: var(--border); }
    .drag-handle:hover { color: var(--text-muted); }

    /* Thumbnail */
    .thumb { width: 80px; height: 52px; object-fit: cover; border-radius: 8px; background: var(--border); }

    /* Empty state */
    .empty { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .empty-icon { margin-bottom: 12px; opacity: 0.3; }
    .empty p { font-size: 14px; font-weight: 600; }
  </style>
</head>
<body>
<?php }

function layout_sidebar(string $active): void {
  $user = $_SESSION['nombre'] ?? $_SESSION['email'] ?? 'Usuario';
  $rol  = $_SESSION['rol'] ?? 'editor';
  ?>
  <aside class="sidebar">
    <div class="sidebar-logo">
      <img src="../imagenes-babidibu-records/logo nuevo babidibu .png" alt="Babidibu Records">
    </div>
    <div class="sidebar-label">Contenido</div>
    <nav class="sidebar-nav">
      <a href="index.php" class="<?= $active==='inicio' ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Inicio
      </a>
      <a href="lanzamientos.php" class="<?= $active==='lanzamientos' ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
        Lanzamientos
      </a>
      <a href="artistas.php" class="<?= $active==='artistas' ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        Artistas
      </a>
      <a href="babidibu-tv.php" class="<?= $active==='tv' ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="15" rx="2"/><polyline points="17 2 12 7 7 2"/></svg>
        Babidibu TV
      </a>
      <a href="hero.php" class="<?= $active==='hero' ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        Hero
      </a>
      <?php if ($rol === 'admin'): ?>
      <div class="sidebar-label" style="margin-top:8px">Admin</div>
      <a href="usuarios.php" class="<?= $active==='usuarios' ? 'active' : '' ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M6 20v-1a6 6 0 0112 0v1"/></svg>
        Usuarios
      </a>
      <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
      <?= htmlspecialchars($user) ?> &middot; <?= $rol ?><br>
      <a href="logout.php">Cerrar sesion</a> &middot; <a href="../index.html" target="_blank">Ver web</a>
    </div>
  </aside>
<?php }

function layout_foot(): void { ?>
</body>
</html>
<?php }

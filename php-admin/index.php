<?php
require_once 'config.php';
auth();
require_once 'layout.php';

$stats = [];
foreach (['lanzamientos','artistas','babidibu_tv','hero_slides'] as $t) {
  $stats[$t] = db()->query("SELECT COUNT(*) FROM $t WHERE publicado=1")->fetchColumn();
}

layout_head('Inicio');
layout_sidebar('inicio');
?>
<div class="main">
  <div class="topbar">
    <div class="topbar-title">Bienvenida, <?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></div>
    <div class="topbar-user"><?= htmlspecialchars($_SESSION['email']) ?></div>
  </div>
  <div class="content">

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:32px;">
      <?php
      $items = [
        ['Lanzamientos', $stats['lanzamientos'], 'lanzamientos.php', '#5FA55A'],
        ['Artistas',     $stats['artistas'],     'artistas.php',     '#01B4BC'],
        ['Babidibu TV',  $stats['babidibu_tv'],  'babidibu-tv.php',  '#FA8925'],
        ['Hero slides',  $stats['hero_slides'],  'hero.php',         '#FA5457'],
      ];
      foreach ($items as [$label, $count, $url, $color]):
      ?>
      <a href="<?= $url ?>" style="text-decoration:none;">
        <div class="card" style="border-top: 4px solid <?= $color ?>;margin-bottom:0;transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow=''">
          <div style="font-size:28px;font-weight:900;color:<?= $color ?>"><?= $count ?></div>
          <div style="font-size:13px;font-weight:700;color:#6B7280;margin-top:4px"><?= $label ?> publicados</div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <div class="card">
      <div class="card-title">Accesos rapidos</div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="lanzamientos.php?action=new" class="btn btn-verde">+ Nuevo lanzamiento</a>
        <a href="artistas.php?action=new" class="btn btn-outline">+ Nuevo artista</a>
        <a href="babidibu-tv.php?action=new" class="btn btn-outline">+ Video Babidibu TV</a>
        <a href="hero.php?action=new" class="btn btn-outline">+ Slide hero</a>
      </div>
    </div>

  </div>
</div>
<?php layout_foot(); ?>

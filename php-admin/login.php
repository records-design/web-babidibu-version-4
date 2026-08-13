<?php
session_start();
if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }

require_once 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  if ($email && $pass) {
    $stmt = db()->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($pass, $user['password'])) {
      session_regenerate_id(true);
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['email']   = $user['email'];
      $_SESSION['nombre']  = $user['nombre'];
      $_SESSION['rol']     = $user['rol'];
      header('Location: index.php'); exit;
    }
  }
  $error = 'Email o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingresar — Babidibu CMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root { --verde: #5FA55A; --coral: #FA5457; --negro: #3A3F44; --border: #E8EAED; }
    body { font-family: 'Nunito', sans-serif; background: #F8F9FA; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-wrap { width: 100%; max-width: 400px; padding: 20px; }
    .login-logo { text-align: center; margin-bottom: 32px; }
    .login-logo img { height: 48px; }
    .login-card { background: #fff; border-radius: 20px; border: 1px solid var(--border); padding: 36px 32px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
    .login-title { font-size: 22px; font-weight: 900; color: var(--negro); margin-bottom: 6px; }
    .login-sub { font-size: 14px; color: #6B7280; margin-bottom: 28px; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; }
    .form-group input { width: 100%; padding: 11px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-family: 'Nunito', sans-serif; font-size: 14px; color: var(--negro); transition: border 0.15s; }
    .form-group input:focus { outline: none; border-color: var(--verde); }
    .btn-login { width: 100%; padding: 12px; background: var(--verde); color: #fff; border: none; border-radius: 12px; font-family: 'Nunito', sans-serif; font-size: 15px; font-weight: 800; cursor: pointer; transition: background 0.15s; margin-top: 8px; }
    .btn-login:hover { background: #4d8f48; }
    .error { background: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600; margin-bottom: 18px; }
  </style>
</head>
<body>
  <div class="login-wrap">
    <div class="login-logo">
      <img src="../imagenes-babidibu-records/logo nuevo babidibu .png" alt="Babidibu Records">
    </div>
    <div class="login-card">
      <div class="login-title">Panel de contenido</div>
      <div class="login-sub">Ingresa con tu cuenta para administrar el sitio.</div>
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Contraseña</label>
          <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn-login">Ingresar</button>
      </form>
    </div>
  </div>
</body>
</html>

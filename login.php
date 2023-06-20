<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $contrasena = $_POST['contrasena'];

  // Obtener los datos del usuario desde la base de datos
  $sql = "SELECT * FROM usuarios WHERE email = '$email'";
  $result = $conn->query($sql);

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hashedContrasena = $row['contrasena'];

    // Verificar la contraseña ingresada con la contraseña almacenada en la base de datos
    if (password_verify($contrasena, $hashedContrasena)) {
      // Inicio de sesión exitoso
      $_SESSION['usuario_id'] = $row['id'];
      $_SESSION['usuario_nombre'] = $row['nombre'];
      header("Location: dashboard.php");
      exit();
    } else {
      // Contraseña incorrecta
      $error = "Contraseña incorrecta";
    }
  } else {
    // Usuario no encontrado
    $error = "Usuario no encontrado";
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <style>
    .container {
      max-width: 400px;
      margin-top: 50px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Iniciar Sesión</h2>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <div class="form-group">
        <label for="email">Correo Electrónico:</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="contrasena">Contraseña:</label>
        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
      </div>
      <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>
      <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
      <a class="button" href="registro.php">Registro</a>
    </form>
  </div>
</body>
</html>

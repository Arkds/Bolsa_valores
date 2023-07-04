<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['nombre'];
  $email = $_POST['email'];
  $contrasena = $_POST['contrasena'];

  // Aplica el hashing a la contraseña
  $hashedContrasena = password_hash($contrasena, PASSWORD_DEFAULT);

  // Inserta los datos del usuario en la base de datos
  $sql = "INSERT INTO usuarios (nombre, email, contrasena) VALUES ('$nombre', '$email', '$hashedContrasena')";

  if ($conn->query($sql) === TRUE) {
    header("Location: login.php");
    exit;
  } else {
    echo "Error al registrar el usuario: " . $conn->error;
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  <link rel="stylesheet" href="bootstrap.min.css">
  <style>
    .container {
      max-width: 400px;
      margin-top: 50px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Registro de Usuario</h2>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
      </div>
      <div class="form-group">
        <label for="email">Correo Electrónico:</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="form-group">
        <label for="contrasena">Contraseña:</label>
        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
      </div>
      <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
  </div>
</body>
</html>

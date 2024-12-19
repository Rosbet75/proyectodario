<?php
function verificarCredenciales($nickname, $contrasena) {
  // Configuración de la base de datos
  $servername = "localhost";
  $username = "root";
  $password = "eneto";
  $dbname = "eneto";

  // Crear conexión
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Verificar si hubo errores en la conexión
  if ($conn->connect_error) {
      die("Conexion fallida: " . $conn->connect_error);
  }

  // Consulta SQL para verificar credenciales
  $sql = "SELECT COUNT(*) AS total
          FROM usuarios
          WHERE nickname = ?
            AND contra = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $nickname, $contrasena);
  $stmt->execute();
  $stmt->bind_result($total);
  $stmt->fetch();

  // Cerrar la consulta y conexión
  $stmt->close();
  $conn->close();

  // Retorna verdadero si se encontró una coincidencia, falso de lo contrario
  return $total > 0;
}
function verificarCredencialesAdmin($nickname, $contrasena) {
  // Configuración de la base de datos
  $servername = "localhost";
  $username = "root";
  $password = "eneto";
  $dbname = "eneto";

  // Crear conexión
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Verificar si hubo errores en la conexión
  if ($conn->connect_error) {
      die("Conexion fallida: " . $conn->connect_error);
  }

  // Consulta SQL para verificar credenciales
  $sql = "SELECT COUNT(*) AS total
          FROM admins
          WHERE nickname = ?
            AND contra = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $nickname, $contrasena);
  $stmt->execute();
  $stmt->bind_result($total);
  $stmt->fetch();

  // Cerrar la consulta y conexión
  $stmt->close();
  $conn->close();

  // Retorna verdadero si se encontró una coincidencia, falso de lo contrario
  return $total > 0;
}


if (isset($_COOKIE['logeo'])) {
  $cred = explode(":", $_COOKIE["logeo"]);
  
  $resultado = verificarCredenciales($cred[0], $cred[1]);
  
  
  $resultado = verificarCredenciales($cred[0], $cred[1]);
  
  if($resultado > 0){
      if(isset($_POST['unlog'])){
        setcookie("logeo", "", time() - 3600, "/", $_SERVER['SERVER_ADDR']);
        header("Location: login.php");
        exit;
      }
  }
  $resultado = verificarCredencialesAdmin($cred[0], $cred[1]);
  if($resultado > 0){
      header("Location: BarraAdmin.php");
      exit;
  }
} else {
  header("Location: login.php");
  exit;
}
//---------------------------------------------------------------------------------
$cnn = new mysqli("localhost", "root", "eneto", "eneto");

if ($cnn->connect_error) {
    die("Error de conexion: " . $cnn->connect_error);
}
$cred = explode(":", $_COOKIE["logeo"]);
  

$consul = $cnn->query("SELECT * from usuarios where nickname = '". $cred[0] . "';");

$tablas = "";
$ren = $consul -> fetch_array(MYSQLI_ASSOC);
if (isset($_POST['nombre'])) {
  // Base SQL query
  $meagarras = "UPDATE usuarios SET ";
  $params = [];
  $types = ""; 
  if (!empty($_POST['apellidoMaterno'])) {
      $meagarras .= "apellidomaterno = ?, ";
      $params[] = $_POST['apellidoMaterno'];
      $types .= "s";
  }
  if (!empty($_POST['apellidoPaterno'])) {
      $meagarras .= "apellidoPaterno = ?, ";
      $params[] = $_POST['apellidoPaterno'];
      $types .= "s";
  }
  if (!empty($_POST['nombre'])) {
      $meagarras .= "nombre = ?, ";
      $params[] = $_POST['nombre'];
      $types .= "s";
  }
  if (!empty($_POST['sexo'])) {
      $meagarras .= "sexo = ?, ";
      $params[] = $_POST['sexo'];
      $types .= "s";
  }
  if (!empty($_POST['correo'])) {
      $meagarras .= "correo = ?, ";
      $params[] = $_POST['correo'];
      $types .= "s";
  }
  if (!empty($_POST['nuevaCon']) && $_POST['nuevaCon'] === $_POST['confCon']) {
      $meagarras .= "contra = ?, ";
      $params[] = $_POST['nuevaCon'];
      $types .= "s";
  }

  
  $meagarras = rtrim($meagarras, ", ") . " WHERE nickname = ?";
  $params[] = $_POST['nickname'];
  $types .= "s";

  
  $stmt = $cnn->prepare($meagarras);
  if ($stmt === false) {
      die("Error preparando statement: " . $cnn->error);
  }

  $stmt->bind_param($types, ...$params);

  if ($stmt->execute()) {
      echo "Usuario actualizado exitosamente.";
  } else {
      echo "Error ACtualizando usuario: " . $stmt->error;
  }

  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/barra.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary color">
    <div class="container-fluid color">
      <a class="navbar-brand white" href="barra.php">Eneto.Inc</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav color">
          <li class="nav-item"><a class="nav-link white" href="solicitarViajeUsuario.php">Solicitar viaje</a></li>
          <li class="nav-item"><a class="nav-link white" href="historialViajesUsuario.php">Historial de viajes</a></li>
          <li class="nav-item"><a class="nav-link white" href="metodosPagoUsuario.php">Metodos de pago</a></li>
          <li class="nav-item"><a class="nav-link white" href="miperfil.php">Mi perfil</a></li>
          <li class="nav-item"><a class="nav-link white" href="pagos.php">Pagos</a></li>
          <li class="nav-item">
            <form action="" method="post" name="logout" id="logout">
              <input type="hidden" value="1" name="unlog">
              <button type='submit' form='logout' class='btn color white'>Log out</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-15">
            <div class="card">
                <br>
                <h1 class="text-success text-center">Mi Perfil</h1>
                <div class="card-body">
                    <form id="perfil" method="POST">
                        <div class="form-group">
                            <label for="nickname">Nickname</label>
                            <input type="text" class="form-control" id="nickname" value="<?php echo $ren['nickname']?>" readonly name="nickname">
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" value="<?php echo $ren['nombre']?>" name="nombre">
                        </div>
                        <div class="form-group">
                            <label for="apellidoPaterno">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellidoPaterno" value="<?php echo $ren['apellidoPaterno']?>" name="apellidoMaterno">
                        </div>
                        <div class="form-group">
                            <label for="apellidoMaterno">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellidoMaterno" value="<?php echo $ren['apellidomaterno']?>" name="apellidoPaterno">
                        </div>
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sexo" id="masculino" value="Masculino" <?php echo $ren['sexo'] == 'Masculino' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="masculino">Masculino</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sexo" id="femenino" value="Femenino" <?php echo $ren['sexo'] == 'Femenino' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="femenino">Femenino</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo Electronico</label>
                            <input type="email" class="form-control" id="correo" value="<?php echo $ren['correo']?>" required>
                        </div>
                        <h2 class="mt-4">Cambiar Contrasena</h2>
                        <div class="form-group">
                            <label for="contrasenaActual">Contrasena Actual</label>
                            <input type="password" class="form-control" id="contrasenaActual" required name="contrapres">
                        </div>
                        <div class="form-group">
                            <label for="nuevaContrasena">Nueva Contrasena</label>
                            <input type="password" class="form-control" id="nuevaContrasena" oninput="verificarContrasenas()" name="nuevaCon">
                        </div>
                        <div class="form-group">
                            <label for="confirmarContrasena">Confirmar Nueva Contrasena</label>
                            <input type="password" class="form-control" id="confirmarContrasena" oninput="verificarContrasenas()" name="confCon">
                            <div id="mensajeContrasena" style="font-size: 0.875em; margin-top: 5px;"></div>
                        </div>

                        <br>
                        <button class="btn color white" type="submit">Actualizar Datos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<!--hasta aqui funciona bien-->
<script src="scripts/utileria.js"></script>
</html>

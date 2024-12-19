

<?php

function verificarCredenciales($nickname, $contrasena) {
  $servername = "localhost";
  $username = "root";
  $password = "eneto";
  $dbname = "eneto";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Conexión fallida: " . $conn->connect_error);
  }

  $sql = "SELECT COUNT(*) AS total
          FROM usuarios
          WHERE nickname = ?
            AND contra = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $nickname, $contrasena);
  $stmt->execute();
  $stmt->bind_result($total);
  $stmt->fetch();

  $stmt->close();
  $conn->close();

  return $total > 0;
}
function verificarCredencialesAdmin($nickname, $contrasena) {
  $servername = "localhost";
  $username = "root";
  $password = "eneto";
  $dbname = "eneto";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Conexión fallida: " . $conn->connect_error);
  }

  $sql = "SELECT COUNT(*) AS total
          FROM admins
          WHERE nickname = ?
            AND contra = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $nickname, $contrasena);
  $stmt->execute();
  $stmt->bind_result($total);
  $stmt->fetch();

  $stmt->close();
  $conn->close();

  return $total > 0;
}

if(isset($_COOKIE['logeo'])){
  $cred = explode(":", $_COOKIE["logeo"]);
        
  
  $resultado = verificarCredenciales($cred[0], $cred[1]);
  if ($resultado) {
    echo htmlspecialchars($cred[0]); //aqui esta el nickname alfin
   
}

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

function obtenerDatosUsuario($nickname) {
    $servername = "localhost";
    $username = "root";
    $password = "eneto";
    $dbname = "eneto";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexion fallida: " . $conn->connect_error);
    }

    $sql = "SELECT nombre, apellidoPaterno, apellidomaterno, sexo, correo
            FROM usuarios
            WHERE nickname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $stmt->bind_result($nombre, $apellidoPaterno, $apellidoMaterno, $sexo, $correo);
    $stmt->fetch();

    $stmt->close();
    $conn->close();

    return [
        'nombre' => $nombre,
        'apellidoPaterno' => $apellidoPaterno,
        'apellidoMaterno' => $apellidoMaterno,
        'sexo' => $sexo,
        'correo' => $correo
    ];
}

function actualizarDatosUsuario($nickname, $nombre, $apellidoPaterno, $apellidoMaterno, $sexo, $correo, $contrasenaActual, $nuevaContrasena) {
    $servername = "localhost";
    $username = "root";
    $password = "eneto";
    $dbname = "eneto";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexion fallida: " . $conn->connect_error);
    }

    $sql = "SELECT contra FROM usuarios WHERE nickname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $stmt->bind_result($contraActual);
    $stmt->fetch();
    $stmt->close();

    if ($contrasenaActual && $contrasenaActual === $contraActual) {
        if ($nuevaContrasena) {
            $sql = "UPDATE usuarios SET nombre = ?, apellidoPaterno = ?, apellidomaterno = ?, sexo = ?, correo = ?, contra = ? WHERE nickname = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $nombre, $apellidoPaterno, $apellidoMaterno, $sexo, $correo, $nuevaContrasena, $nickname);
        } else {
            $sql = "UPDATE usuarios SET nombre = ?, apellidoPaterno = ?, apellidomaterno = ?, sexo = ?, correo = ? WHERE nickname = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $nombre, $apellidoPaterno, $apellidoMaterno, $sexo, $correo, $nickname);
        }
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Contrasena actual incorrecta.";
    }

    $conn->close();
}

if (isset($_COOKIE['logeo'])) {
    $cred = explode(":", $_COOKIE["logeo"]);
    $nickname = $cred[0];

    $usuario = obtenerDatosUsuario($nickname);

    $servername = "localhost";
    $username = "root";
    $password = "eneto";
    $dbname = "eneto";
    $conn = new mysqli($servername, $username, $password, $dbname);
    $sql = "SELECT contra FROM usuarios WHERE nickname = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $stmt->bind_result($contraUsuario);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : $usuario['nombre'];
        $apellidoPaterno = isset($_POST['apellidoPaterno']) ? $_POST['apellidoPaterno'] : $usuario['apellidoPaterno'];
        $apellidoMaterno = isset($_POST['apellidoMaterno']) ? $_POST['apellidoMaterno'] : $usuario['apellidoMaterno'];
        $sexo = isset($_POST['sexo']) ? $_POST['sexo'] : $usuario['sexo'];
        $correo = isset($_POST['correo']) ? $_POST['correo'] : $usuario['correo'];
        $contrasenaActual = isset($_POST['contrasenaActual']) ? $_POST['contrasenaActual'] : null;
        $nuevaContrasena = isset($_POST['nuevaContrasena']) && $_POST['nuevaContrasena'] ? $_POST['nuevaContrasena'] : null;

        actualizarDatosUsuario($nickname, $nombre, $apellidoPaterno, $apellidoMaterno, $sexo, $correo, $contrasenaActual, $nuevaContrasena);
    }
} else {
    header("Location: login.php");
    exit;
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
                            <input type="text" class="form-control" id="nickname" value="<?php echo htmlspecialchars($nickname); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="apellidoPaterno">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellidoPaterno" value="<?php echo htmlspecialchars($usuario['apellidoPaterno']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="apellidoMaterno">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellidoMaterno" value="<?php echo htmlspecialchars($usuario['apellidoMaterno']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sexo" id="masculino" value="M" <?php echo $usuario['sexo'] === 'M' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="masculino">Masculino</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sexo" id="femenino" value="F" <?php echo $usuario['sexo'] === 'F' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="femenino">Femenino</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo Electronico</label>
                            <input type="email" class="form-control" id="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contrasenaActual">Contrasena Actual</label>
                            <input type="password" class="form-control" id="contrasenaActual" required>
                        </div>
                        <h2 class="mt-4">Cambiar Contrasena</h2>
                        <div class="form-group">
                            <label for="nuevaContrasena">Nueva Contrasena</label>
                            <input type="password" class="form-control" id="nuevaContrasena" oninput="verificarContrasenas()">
                        </div>
                        <div class="form-group">
                            <label for="confirmarContrasena">Confirmar Nueva Contrasena</label>
                            <input type="password" class="form-control" id="confirmarContrasena" oninput="verificarContrasenas()">
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

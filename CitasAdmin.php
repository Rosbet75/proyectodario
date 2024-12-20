<?php

function verificarCredenciales($nickname, $contrasena) {
  // Configuración de la base de datos
  $servername = "localhost";
  $username = "eneto";
  $password = "eneto";
  $dbname = "eneto";

  // Crear conexión
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Verificar si hubo errores en la conexión
  if ($conn->connect_error) {
      die("Conexión fallida: " . $conn->connect_error);
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
  $username = "eneto";
  $password = "eneto";
  $dbname = "eneto";

  // Crear conexión
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Verificar si hubo errores en la conexión
  if ($conn->connect_error) {
      die("Conexión fallida: " . $conn->connect_error);
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

if(isset($_COOKIE['logeo'])){
  $cred = explode(":", $_COOKIE["logeo"]);
        
  $resultado = verificarCredenciales($cred[0], $cred[1]);
  
  if($resultado > 0){
      header("Location: barra.php");
      exit;
  }
  $resultado = verificarCredencialesAdmin($cred[0], $cred[1]);
  if($resultado > 0){
    if(isset($_POST['unlog'])){
      $metadataUrl = 'http://169.254.169.254/latest/meta-data/public-ipv4';
        $ch = curl_init($metadataUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $publicIp = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "Error: " . curl_error($ch);
            $publicIp = "Unable to fetch IP";
        }
        curl_close($ch);
        setcookie("logeo", "", time() - 3600, "/", $publicIp);
        header("Location: login.php");
        exit;
      }
  }
} else {
  header("Location: login.php");
  exit;
}
//----------------------------------------------------
$cnn = new mysqli("localhost", "eneto", "eneto", "eneto");
if (mysqli_connect_errno()) {
    echo $cnn->connect_error;
    exit();
}

if (isset($_POST['idCita'])) {
    $idCita = intval($_POST['idCita']);
    
    $updateQuery = "UPDATE citas SET atendido = 1 WHERE idCita = ?";
    $stmt = $cnn->prepare($updateQuery);
    $stmt->bind_param("i", $idCita);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Cita marcada como atendida.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al marcar la cita: " . $stmt->error . "</div>";
    }
    
    $stmt->close();
}

$citasQuery = "SELECT idCita, idChofer, fechaCita, concepto, comentarios, sancion, atendido FROM citas";
$citasResult = $cnn->query($citasQuery);

$citasHTML = "";
if ($citasResult->num_rows > 0) {
    while ($cita = $citasResult->fetch_assoc()) {
        $citasHTML .= '
        <div class="container-fluid">
            <div class="container mt-9">
                <div class="column justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="">ID Cita: ' . $cita['idCita'] . '</label>
                                </div>
                                <div class="form-group">
                                    <label for="">ID Chofer: ' . $cita['idChofer'] . '</label>
                                </div>
                                <div class="form-group">
                                    <label for="">Fecha: ' . $cita['fechaCita'] . '</label>
                                </div>
                                <div class="form-group">
                                    <label for="">Concepto: ' . $cita['concepto'] . '</label>
                                </div>
                                <div class="form-group">
                                    <label for="">Comentarios: ' . $cita['comentarios'] . '</label>
                                </div>
                                <div class="form-group">
                                    <label for="">Sancion: ' . $cita['sancion'] . '</label>
                                </div>';
        
        if ($cita['atendido'] == 1) {
            $citasHTML .= '<div class="form-group"><label for="">Estado: Atendida</label></div>';
        } else {
            $citasHTML .= '
            <div class="d-flex justify-content-end">
                <form method="POST" action="">
                    <input type="hidden" name="idCita" value="' . $cita['idCita'] . '">
                    <button type="submit" class="btn btn-primary btn-success ms-2">Marcar como atendido</button>
                </form>
            </div>';
        }

        $citasHTML .= '
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
} else {
    $citasHTML = "No hay citas disponibles.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas</title>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha384-NTiQPhqQukTxrOSDUpQzUSkroTlvBAJc8fPskh1hJgnBwVnvWA/U+4Y39e7WuaDJ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/barra.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    
<nav class="navbar navbar-expand-lg bg-body-tertiary color">
    <div class="container-fluid color">
    <a class="navbar-brand white" href="BarraAdmin.php">Eneto.Inc</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav color">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Admins
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="administradorAdmin.php">Crear Usuario Administrador</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="EditarEliminarAdministrador.php">Editar/Eliminar Usuario Administrador</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Empleados
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="empleadosAdmin.php">Registrar Empleado</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="EditarEliminarEmpleado.php">Editar/Eliminar Empleado</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Choferes
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="choferesAdmin.php">Registrar Chofer</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="EditarEliminarChofer.php">Editar/Eliminar Chofer</a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="QuejasAdmin.php">Quejas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="CitasAdmin.php">Citas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="pagosAdmin.php">Pagos</a>
          </li>
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Vehiculos
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="vehiculosAdmin.php">Registrar Vehiculo</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="EditarEliminarVehiculo.php">Editar/Eliminar Vehiculo</a>
              </div>
          </li>
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

  <div class="container">
    <?php echo $citasHTML; ?>
  </div>


</body>
</html>
<?php
mysqli_close($cnn);
?>
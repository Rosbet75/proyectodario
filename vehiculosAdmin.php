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
  $username = "root";
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
        setcookie("logeo", "", time() - 3600, "/", "localhost");
        header("Location: login.php");
        exit;
      }
  }
} else {
  header("Location: login.php");
  exit;
}
//------------------------------------------------------------------------
$cnn = mysqli_connect("localhost", "root", "eneto", "eneto");

if (!$cnn) {
    die("Conexion fallida: " . mysqli_connect_error());
}

if (isset($_POST['idMatricula']) && isset($_POST['anoVehiculo']) && isset($_POST['modelo']) && isset($_POST['plazas']) && isset($_POST['color']) && isset($_POST['disponible'])) {
    $idMatricula = $_POST['idMatricula'];
    $anoVehiculo = $_POST['anoVehiculo'];
    $modelo = $_POST['modelo'];
    $plazas = $_POST['plazas'];
    $color = $_POST['color'];
    $disponible = $_POST['disponible'];

    if (empty($idMatricula) || empty($anoVehiculo) || empty($modelo) || empty($plazas) || empty($color) || empty($disponible)) {
        echo "<div class='alert alert-warning mt-4'>Todos los campos son obligatorios</div>";
    } else {
        $checkMatricula = "SELECT * FROM vehiculos WHERE idMatricula = '$idMatricula'";
        $resultMatricula = mysqli_query($cnn, $checkMatricula);

        if (mysqli_num_rows($resultMatricula) > 0) {
            echo "<div class='alert alert-warning mt-4'>La matricula ya esta registrada.</div>";
        } else {
            $sql = "insert into vehiculos (idMatricula, anoVehiculo, modelo, plazas, color, disponible, createdAt, updatedAt) values ('$idMatricula', '$anoVehiculo', '$modelo', '$plazas', '$color', '$disponible', NOW(), NOW())";

            if (mysqli_query($cnn, $sql)) {
                echo "<div class='alert alert-success mt-4'>Vehiculo registrado con exito</div>";
            } else {
                echo "<div class='alert alert-danger mt-4'>Error: " . mysqli_error($cnn) . "</div>";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vehiculo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/barra.css">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary color">
    <div class="container-fluid color">
      <a class="navbar-brand white" href="#">Eneto.Inc</a>
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
              <a class="dropdown-item" href="editareliminarAdministrador.php">Editar/Eliminar Usuario Administrador</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Empleados
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="empleadosAdmin.php">Registrar Empleado</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="editareliminarEmpleado.php">Editar/Eliminar Empleado</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Choferes
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="choferesAdmin.php">Registrar Chofer</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="editareliminarChofer.php">Editar/Eliminar Chofer</a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="QuejasAdmin.php">Quejas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="CitasAdmin.php">Citas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="pagos.php">Pagos</a>
          </li>
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Vehiculos
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="vehiculosAdmin.php">Registrar Vehiculo</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="editareliminarVehiculo.php">Editar/Eliminar Vehiculo</a>
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


  <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <h1 class="text-success text-center mt-3">Registrar Vehiculo</h1>
                <div class="card-body">
                    <form id="registroVehiculo" action="vehiculosAdmin.php" method="POST">
                        <div class="form-group">
                            <label for="idMatricula">Matricula</label>
                            <input type="text" class="form-control" id="idMatricula" name="idMatricula" placeholder="Ingresa la matricula del vehiculo" minlength="6" maxlength="8"    required>
                        </div>
                        <div class="form-group">
                            <label for="anoVehiculo">Año del vehiculo (AAAA)</label>
                            <input type="text" class="form-control" id="anoVehiculo" name="anoVehiculo" placeholder="Ingresa el año del vehiculo" onkeypress="permitirSoloNumeros(event)" minlength="4" maxlength="4" required>
                        </div>
                        <div class="form-group">
                            <label for="modelo">Modelo</label>
                            <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ingresa el modelo del vehiculo" required>
                        </div>
                        <div class="form-group">
                            <label for="plazas">Plazas</label>
                            <input type="number" class="form-control" id="plazas" name="plazas" placeholder="Ingresa el numero de plazas" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="text" class="form-control" id="color" name="color" placeholder="Ingresa el color del vehiculo" required>
                        </div>
                        <div class="form-group">
                            <label for="disponible">Disponible</label>
                            <select class="form-control" id="disponible" name="disponible" required>
                                <option value="" disabled selected>Selecciona si el vehiculo esta disponible</option>
                                <option value="1">Disponible</option>
                                <option value="0">No disponible</option>
                            </select>
                        </div>
                        <br>
                        <button class="btn color white w-100" type="submit">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </div>
</body>
<script src="scripts/utileria.js"></script>
</html>

<?php
mysqli_close($cnn);
?>

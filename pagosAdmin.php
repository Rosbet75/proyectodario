
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
  
    
    $stmt->close();
    $conn->close();
  
   
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
          setcookie("logeo", "", time() - 3600, "/", $_SERVER['SERVER_ADDR']);
          header("Location: login.php");
          exit;
        }
    }
  } else {
    header("Location: login.php");
    exit;
  }
  //--------------------------------------------------------
$cnn = new mysqli("localhost", "root", "eneto", "eneto");

if ($cnn->connect_error) {
    die("Error de conexion: " . $cnn->connect_error);
}

$consul = $cnn->query("SELECT idPago, idTarjeta, monto, idViaje, estadoPago, createdAt FROM pagos");

$tablas = "";
while ($ren = $consul->fetch_array(MYSQLI_ASSOC)) {
    $estadoPago = $ren['estadoPago'] == 1 ? 'Completado' : 'Pendiente'; // Suponiendo que 1 es completado y 0 es pendiente
    $tablas .= "<br>
    <div class='container-fluid'>
        <div class='container mt-9'>
            <div class='column justify-content-center'>
                <div class='col-lg-12'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='form-group'>
                                <label for=''>ID Pago: {$ren['idPago']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>ID Tarjeta: {$ren['idTarjeta']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>Monto: {$ren['monto']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>ID Viaje: {$ren['idViaje']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>Estado de Pago: {$estadoPago}</label>
                            </div>
                            <div class='d-flex justify-content-end'>
                                <button type='button' class='btn btn-warning me-2' data-bs-toggle='modal' data-bs-target='#modalModificar{$ren['idPago']}'>
                                    Actualizar
                                </button>
                                <button type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#modalEliminar{$ren['idPago']}'>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Modificar -->
    <div class='modal fade' id='modalModificar{$ren['idPago']}' tabindex='-1' aria-labelledby='modalModificarLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='modalModificarLabel'>Modificar Pago</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form action='pagosAdmin.php' method='POST' id='formModificar{$ren['idPago']}'>
                        <input type='hidden' name='idPago' value='{$ren['idPago']}'>
                        
                        <div class='form-group mb-3'>
                            <label for='monto{$ren['idPago']}' class='form-label'>Monto</label>
                            <input type='number' class='form-control' id='monto{$ren['idPago']}' name='monto' value='{$ren['monto']}' required>
                        </div>
                        <div class='form-group mb-3'>
                            <label for='idTarjeta{$ren['idPago']}' class='form-label'>ID Tarjeta</label>
                            <input type='text' class='form-control' id='idTarjeta{$ren['idPago']}' name='idTarjeta' value='{$ren['idTarjeta']}' readonly  required>
                        </div>
                        <div class='form-group mb-3'>
                            <label for='idViaje{$ren['idPago']}' class='form-label'>ID Viaje</label>
                            <input type='number' class='form-control' id='idViaje{$ren['idPago']}' name='idViaje' value='{$ren['idViaje']}' readonly  required>
                        </div>
                        <div class='form-group mb-3'>
                            <label for='estadoPago{$ren['idPago']}' class='form-label'>Estado de Pago</label>
                            <select class='form-control' id='estadoPago{$ren['idPago']}' name='estadoPago'>
                                <option value='1' " . ($ren['estadoPago'] == 1 ? 'selected' : '') . ">Completado</option>
                                <option value='0' " . ($ren['estadoPago'] == 0 ? 'selected' : '') . ">Pendiente</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class='modal-footer d-flex justify-content-between w-100'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                    <button type='submit' class='btn btn-primary' form='formModificar{$ren['idPago']}' name='modificar'>Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div class='modal fade' id='modalEliminar{$ren['idPago']}' tabindex='-1' aria-labelledby='modalEliminarLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='modalEliminarLabel'>¿Estas seguro de eliminar este pago?</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form method='POST' action='pagosAdmin.php'>
                        <input type='hidden' name='idPago' value='{$ren['idPago']}'>
                        <p>Esta acción no se puede deshacer.</p>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                        <button type='submit' class='btn btn-danger' name='eliminar'>Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    ";
}



// Verificar si se realizó la acción de eliminar
// Verificar si se realizó la acción de eliminar
if (isset($_POST['eliminar'])) {
  $idPago = $_POST['idPago'];

  try {
      $query = "DELETE FROM pagos WHERE idPago = ?";
      $stmt = $cnn->prepare($query);
      $stmt->bind_param("s", $idPago);

      if ($stmt->execute()) {
          echo "<div class='alert alert-info'>Pago eliminado correctamente.</div>";
          // Redirigir a la misma página para actualizar los datos
          header("Location: " . $_SERVER['PHP_SELF']);
          exit;
      } else {
          throw new Exception("Error al eliminar el pago: " . $stmt->error);
      }

  } catch (Exception $e) {
      echo "<div class='alert alert-danger'>No se puede eliminar el pago debido a una dependencia.</div>";
  }
}

if (isset($_POST['modificar'])) {
  $idPago = $_POST['idPago'];
  $fecha = $_POST['fecha'];
  $monto = $_POST['monto'];
  $idTarjeta = $_POST['idTarjeta'];
  $idViaje = $_POST['idViaje'];
  $estadoPago = $_POST['estadoPago'];

  $query = "UPDATE pagos SET createdAt = ?, monto = ?, idTarjeta = ?, idViaje = ?, estadoPago = ? WHERE idPago = ?";
  $stmt = $cnn->prepare($query);
  $stmt->bind_param("sssssi", $fecha, $monto, $idTarjeta, $idViaje, $estadoPago, $idPago);

  if ($stmt->execute()) {
      echo "<div class='alert alert-info'>Pago actualizado correctamente.</div>";
      // Redirigir a la misma página para actualizar los datos
      header("Location: " . $_SERVER['PHP_SELF']);
      exit;
  } else {
      echo "<div class='alert alert-danger'>Error al actualizar el pago.</div>";
  }

  $stmt->close();
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h1>Gestión de Pagos</h1>
        <?= $tablas ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

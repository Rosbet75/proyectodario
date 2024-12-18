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
        if(isset($_POST['unlog'])){
          setcookie("logeo", "", time() - 3600, "/", "127.0.0.1");
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
$cnn = new mysqli("localhost", "root", "eneto", "eneto");

if ($cnn->connect_error) {
    die("Error de conexión: " . $cnn->connect_error);
}

if (isset($_POST['submit'])) {
    $numTarjeta = $_POST['numeroTarjeta'];
    $fechaExpiracion = $_POST['fechaExpiracion'];
    $cvv = $_POST['cvv'];

    $fechaExpiracion = date("m/y", strtotime($fechaExpiracion));

    $query = "INSERT INTO tarjetas (numTar, fechaExp, cvv) VALUES ('$numTarjeta', '$fechaExpiracion', '$cvv')";

    if ($cnn->query($query)) {
        $message = "Metodo de pago agregado correctamente.";
    } else {
        $message = "Error al agregar el metodo de pago: " . $cnn->error;
    }
}


if (isset($_POST['eliminar'])) {
    $idTarjeta = $_POST['idTarjeta'];

    $query = "DELETE FROM tarjetas WHERE numTar = '$idTarjeta'";

    if ($cnn->query($query)) {
        $message = "Metodo de pago eliminado correctamente.";
    } else {
        $message = "Error al eliminar el metodo de pago: " . $cnn->error;
    }
}

$consul = $cnn->query("SELECT * FROM tarjetas");
$tablas = "";
while ($ren = $consul->fetch_array(MYSQLI_ASSOC)) {
    $tablas .= "<br>
    <div class='container-fluid'>
        <div class='container mt-9'>
            <div class='column justify-content-center'>
                <div class='col-lg-12'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='form-group'>
                                <label for=''>Numero de Tarjeta: {$ren['numTar']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>Fecha de Expiracion: {$ren['fechaExp']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>CVV: {$ren['cvv']}</label>
                            </div>
                            <div class='d-flex justify-content-end'>
                                <button type='button' class='btn btn-danger ms-2' data-bs-toggle='modal' data-bs-target='#modalEliminar{$ren['numTar']}'>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='modal fade' id='modalEliminar{$ren['numTar']}' tabindex='-1' aria-labelledby='modalEliminarLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' numTar='modalEliminarLabel'>¿Estas seguro de eliminar esta tarjeta?</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form method='POST' action='metodosPagoUsuario.php'>
                        <input type='hidden' name='idTarjeta' value='{$ren['numTar']}'>
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Metodo de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
          <li class="nav-item">
            <a class="nav-link white" aria-current="page" href="solicitarViajeUsuario.php">Solicitar viaje</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="historialViajesUsuario.php">Historial de viajes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="metodosPagoUsuario.php">Metodos de pago</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="miperfil.php">Mi perfil</a>
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

    <div class="d-flex justify-content-center">
        <a href="metodosPagoUsuario.php" class="btn color white btn-lg" style="flex: 1; margin: 0 10px; text-align: center;" data-bs-toggle="modal" data-bs-target="#metodoPagoModal">
            <span style="font-size: 3rem;">+</span><br> 
            Agregar Metodo de Pago
        </a>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="container mt-5">
        <h2>Metodos de Pago Registrados</h2>
        <?= $tablas ?>
    </div>

    <div class="modal fade" id="metodoPagoModal" tabindex="-1" aria-labelledby="metodoPagoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="metodoPagoModalLabel">Agregar Metodo de Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formMetodoPago" action="metodosPagoUsuario.php" method="POST">
                        <div class="form-group mb-3">
                            <label for="numeroTarjeta" class="form-label">Numero de tarjeta</label>
                            <input type="text" class="form-control ancho-input-2" id="numeroTarjeta" name="numeroTarjeta" placeholder="1234 5678 9012 3456" minlength="13" maxlength="18" required onkeypress="permitirSoloNumeros(event)">
                        </div>
                        <div class="fila-flex mb-3">
                            <div class="form-group">
                                <label for="fechaExpiracion" class="form-label">Fecha de expiracion</label>
                                <input type="month" class="form-control ancho-input-2" id="fechaExpiracion" name="fechaExpiracion" required>
                            </div>
                            <div class="form-group">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control ancho-input-2" id="cvv" name="cvv" placeholder="123" minlength="3" maxlength="3" required onkeypress="permitirSoloNumeros(event)">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between w-100">
                    <a href="metodosPagoUsuario.php" class="btn btn-secondary" data-bs-dismiss="modal">Regresar</a>
                    <button type="submit" class="btn color white" form="formMetodoPago" name="submit">Guardar</button>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="scripts/utileria.js"></script>

</html>

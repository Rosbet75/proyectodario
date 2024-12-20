<?php
function verificarCredenciales($nickname, $contrasena) {
    $servername = "localhost";
    $username = "eneto";
    $password = "eneto";
    $dbname = "eneto";
  
    $conn = new mysqli($servername, $username, $password, $dbname);
  
    if ($conn->connect_error) {
        die("Conexion fallida: " . $conn->connect_error);
    }
  
    $sql = "SELECT COUNT(*) AS total
            FROM usuarios
            WHERE nickname = ? AND contra = ?";
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
    // Configuración de la base de datos
    $servername = "localhost";
    $username = "eneto";
    $password = "eneto";
    $dbname = "eneto";
  
    $conn = new mysqli($servername, $username, $password, $dbname);
  
    if ($conn->connect_error) {
        die("Conexion fallida: " . $conn->connect_error);
    }
  
    $sql = "SELECT COUNT(*) AS total
            FROM admins
            WHERE nickname = ? AND contra = ?";
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
    $resultado = verificarCredencialesAdmin($cred[0], $cred[1]);
    if($resultado > 0){
        header("Location: BarraAdmin.php");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}

//-----------------------------------------------------------------------------------
$cnn = new mysqli("localhost", "eneto", "eneto", "eneto");

if ($cnn->connect_error) {
    die("Error de conexion: " . $cnn->connect_error);
}

if (isset($_POST['deleteCard'])) {
    $numTarjetaEliminar = $_POST['numTar'];

    if ($cred[0] != "") {
        $deleteQuery = "DELETE FROM tarjetas WHERE numTar = ? AND nickname = ?";
        $stmt = $cnn->prepare($deleteQuery);
        $stmt->bind_param("ss", $numTarjetaEliminar, $cred[0]);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Tarjeta eliminada correctamente.</div>";;
        } else {
            $message = "Error al eliminar la tarjeta: " . $cnn->error;
        }
        $stmt->close();
    } else {
        $message = "Usuario no valido para eliminar tarjeta.";
    }
}

if (isset($_POST['submit'])) {
    $numTarjeta = $_POST['numeroTarjeta'];
    $fechaExpiracion = $_POST['fechaExpiracion'];
    $cvv = $_POST['cvv'];

    $fechaExpiracion = date("m/y", strtotime($fechaExpiracion));

    $checkCardQuery = "SELECT COUNT(*) AS total FROM tarjetas WHERE numTar = ? AND nickname = ?";
    $stmt = $cnn->prepare($checkCardQuery);
    $stmt->bind_param("ss", $numTarjeta, $cred[0]);
    $stmt->execute();
    $stmt->bind_result($totalCards);
    $stmt->fetch();
    $stmt->close();

    if ($totalCards > 0) {
        $message = "<div class='alert alert-danger'>Este numero de tarjeta ya esta registrado para tu cuenta.</div>";
    } else {
        $query = "INSERT INTO tarjetas (numTar, fechaExp, cvv, nickname) VALUES ('$numTarjeta', '$fechaExpiracion', '$cvv', '$cred[0]')";

        if ($cnn->query($query)) {
            $message = "<div class='alert alert-success'>Metodo de pago agregado correctamente.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error al agregar el metodo de pago: " . $cnn->error . "</div>";
        }
    }
}




$consul = $cnn->query("SELECT * FROM tarjetas WHERE nickname = '$cred[0]'");

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
                        <form method='POST' action='metodosPagoUsuario.php'>
                            <input type='hidden' name='numTar' value='{$ren['numTar']}'>
                            <div class='d-flex justify-content-end'>
                                <button type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#deleteCardModal{$ren['numTar']}'>Eliminar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id='deleteCardModal{$ren['numTar']}' tabindex='-1' aria-labelledby='deleteCardModalLabel{$ren['numTar']}' aria-hidden='true'>
    <div class='modal-dialog modal-dialog-centered'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='deleteCardModalLabel{$ren['numTar']}'>Confirmacion de eliminacion</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <p>¿Esta seguro que desea eliminar la tarjeta <strong>{$ren['numTar']}</strong>?</p> 
                <p class='text-danger'><strong>Esta accion no se puede deshacer.</strong></p>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                <form method='POST' action='metodosPagoUsuario.php'>
                    <input type='hidden' name='numTar' value='{$ren['numTar']}'>
                    <button type='submit' class='btn btn-danger' name='deleteCard'>Eliminar</button>
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

<?php if (isset($message)) { echo $message; } ?>

<nav class="navbar navbar-expand-lg bg-body-tertiary color">
    <div class="container-fluid color">
      <a class="navbar-brand white" href="barra.php">Eneto.Inc</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav color">
          <li class="nav-item">
            <a class="nav-link white" aria-current="page" href="solicitarViajeUsuario.php">Solicitar viaje</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="HistorialViajesUsuario.php">Historial de viajes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="metodosPagoUsuario.php">Metodos de pago</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="miperfil.php">Mi perfil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="pagos.php">Pagos</a>
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

    <div class="modal fade" id="metodoPagoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar metodo de pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="metodosPagoUsuario.php" method="POST">
                        <div class="mb-3">
                            <label for="numeroTarjeta" class="form-label">Numero de Tarjeta</label>
                            <input type="text" class="form-control" id="numeroTarjeta" name="numeroTarjeta" placeholder="Ingresa el numero de tarjeta" minlength="13" maxlength="18" onkeypress="permitirSoloNumeros(event)" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaExpiracion" class="form-label">Fecha de Expiracion</label>
                            <input type="month" class="form-control" id="fechaExpiracion" name="fechaExpiracion" required>
                        </div>
                        <div class="mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" placeholder="Ingresa el CVV"  minlength="3" maxlength="3" onkeypress="permitirSoloNumeros(event)" required>
                        </div>
                        <button type="submit" class="btn color white" name="submit">Agregar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <h4>Metodos de pago registrados</h4>
        <div class="row">
            <?php echo $tablas; ?>
        </div>
    </div>
    <script src="scripts/utileria.js"></script>

</body>
</html>
<?php
mysqli_close($cnn);
?>
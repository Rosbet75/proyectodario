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
////------------------------------------------
$cnn = mysqli_connect("localhost", "root", "eneto", "eneto");

if (!$cnn) {
    die("Conexion fallida: " . mysqli_connect_error());
}

$sqlTarjetas = "SELECT numTar FROM tarjetas";
$resultTarjetas = mysqli_query($cnn, $sqlTarjetas);

$sqlConductor = "SELECT * FROM choferes ORDER BY RAND() LIMIT 1";
$resultConductor = mysqli_query($cnn, $sqlConductor);
$conductor = mysqli_fetch_assoc($resultConductor);

$sqlVehiculo = "SELECT * FROM vehiculos WHERE disponible = 1 ORDER BY RAND() LIMIT 1";
$resultVehiculo = mysqli_query($cnn, $sqlVehiculo);
$vehiculo = mysqli_fetch_assoc($resultVehiculo);

if (isset($_POST['destino']) && isset($_POST['cuota'])) {
    $idUsuario = 1; 
    $destino = $_POST['destino'];
    $costoViaje = 100;
    $cuotaGanancia = $_POST['cuota'];

    $sqlInsert = "INSERT INTO viajes (idChofer, idUsuario, destino, costo_viaje, cuota_ganancia, idMatricula)
                  VALUES ('" . $conductor['idChofer'] . "', '$idUsuario', '$destino', '$costoViaje', '$cuotaGanancia', '" . $vehiculo['idMatricula'] . "')";

    if (mysqli_query($cnn, $sqlInsert)) {
        echo "Viaje solicitado con exito!";
    } else {
        echo "Error al solicitar el viaje: " . mysqli_error($cnn);
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Viaje</title>
    
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

  <div class="contenedor">
    <h2>Solicitar Viaje</h2>
    <form method="POST">
        <div>
            <br>
            <label for="tarjeta" class="form-label">Tarjeta seleccionada</label>
            <select id="tarjeta" class="form-control" name="tarjeta" required>
                <option value="" disabled selected>Selecciona una tarjeta</option>
                <?php
                if (mysqli_num_rows($resultTarjetas) > 0) {
                    while($row = mysqli_fetch_assoc($resultTarjetas)) {
                        echo "<option value='" . $row['numTar'] . "'>" . $row['numTar'] . "</option>";
                    }
                } else {
                    echo "<option value='' disabled>No hay tarjetas disponibles</option>";
                }
                ?>
            </select>
            <a href="metodosPagoUsuario.php" class="btn btn-link">Agregar nuevo metodo de pago</a>
        </div>
        <div>
            <br>
            <label for="conductor" class="form-label">Conductor asignado</label>
            <input type="text" class="form-control" id="conductor" name="conductor" value="<?php echo $conductor['curp'] . ' (Licencia: ' . $conductor['num_licencia'] . ')'; ?>" readonly>
        </div>
        <div>
            <br>
            <label for="vehiculo" class="form-label">Vehículo asignado</label>
            <input type="text" class="form-control" id="vehiculo" name="vehiculo" value="<?php echo $vehiculo['modelo'] . ' - ' . $vehiculo['color'] . ' (' . $vehiculo['anoVehiculo'] . ')'; ?>" readonly>
        </div>
        <div>
            <br>
            <label for="destino" class="form-label">Destino</label>
            <input type="text" class="form-control ancho-input" id="destino" name="destino" placeholder="Ingrese la direccion de destino" required>
        </div>
        <div>
            <br>
            <label for="cuota" class="form-label">Cuota</label>
            <input type="text" class="form-control ancho-input" id="cuota" name="cuota" placeholder="80" required>
        </div>

        <br>
        <button type="submit" class="btn color white">Solicitar Viaje</button>
    </form>
    <br>
</div>

<?php
mysqli_close($cnn);
?>
</body>
</html>

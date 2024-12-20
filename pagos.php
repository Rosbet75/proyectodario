<?php

function verificarCredenciales($nickname, $contrasena) {
  $servername = "localhost";
  $username = "root";
  $password = "eneto";
  $dbname = "eneto";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Conexion fallida: " . $conn->connect_error);
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
      die("Conexion fallida: " . $conn->connect_error);
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
function obtenerPagos($nickname) {
    $servername = "localhost";
    $username = "root";
    $password = "eneto";
    $dbname = "eneto";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexion fallida: " . $conn->connect_error);
    }

    $sql = "SELECT p.idPago, p.idTarjeta, p.monto, p.idViaje, p.estadoPago
            FROM pagos p
            JOIN viajes v ON p.idViaje = v.idViaje
            WHERE v.idUsuario = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $stmt->bind_result($idPago, $idTarjeta, $monto, $idViaje, $estadoPago);

    $pagos = [];
    while ($stmt->fetch()) {
        $pagos[] = [
            'idPago' => $idPago,
            'idTarjeta' => $idTarjeta,
            'monto' => $monto,
            'idViaje' => $idViaje,
            'estadoPago' => $estadoPago
        ];
    }

    $stmt->close();
    $conn->close();

    return $pagos;
}

if (isset($_COOKIE['logeo'])) {
    $cred = explode(":", $_COOKIE["logeo"]);
    $nickname = $cred[0];

    $pagos = obtenerPagos($nickname);
} else {
    header("Location: login.php");
    exit;
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Pagos</title>
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

<div class="container-fluid">
    <div class="container mt-9">
        <h2>Listado de Pagos</h2>
        <?php if (!empty($pagos)): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($pagos as $pago): ?>
                                <div class="form-group">
                                    <label for="">ID Pago: <?= $pago['idPago'] ?></label>
                                </div>
                                <div class="form-group">
                                    <label for="">ID Tarjeta: <?= $pago['idTarjeta'] ?></label>
                                </div>
                                <div class="form-group">
                                    <label for="">Monto: $<?= number_format($pago['monto'], 2) ?></label>
                                </div>
                                <div class="form-group">
                                    <label for="">ID Viaje: <?= $pago['idViaje'] ?></label>
                                </div>
                                
                                <hr>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>No hay pagos disponibles para este usuario.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
<?php
mysqli_close($cnn);
?>
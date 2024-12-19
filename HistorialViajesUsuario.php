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

if (isset($_COOKIE['logeo'])) {
  $cred = explode(":", $_COOKIE["logeo"]);

  $resultado = verificarCredenciales($cred[0], $cred[1]);
  if ($resultado) {
      echo htmlspecialchars($cred[0]);
  }

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

function obtenerHistorialViajes($nickname) {
    $servername = "localhost";
    $username = "root";
    $password = "eneto";
    $dbname = "eneto";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = "SELECT destino, costo_viaje, cuota_ganancia, idMatricula, createdAt
            FROM viajes
            WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $stmt->close();
    $conn->close();

    return $resultado;
}


function insertarQueja($idViaje, $comentarios) {
  $servername = "localhost";
  $username = "root";
  $password = "eneto";
  $dbname = "eneto";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Conexión fallida: " . $conn->connect_error);
  }

  $sql = "INSERT INTO queja (idViaje, comentarios, atendido) VALUES (?, ?, 0)"; // 'atendido' se inicializa en 0
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("is", $idViaje, $comentarios);

  if ($stmt->execute()) {
      echo "Queja enviada correctamente.";
  } else {
      echo "Error al enviar la queja: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
$quejaEnviada = false; // Inicializar la variable

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentarios'])) {
  $comentarios = $_POST['comentarios'];
  $idViaje = 1;
  insertarQueja($idViaje, $comentarios);
  $quejaEnviada = true;  // Marca que la queja ha sido enviada
}


function insertarReview($idViaje, $rating, $comentarios) {
  $servername = "localhost";
  $username = "root";
  $password = "eneto";
  $dbname = "eneto";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Conexión fallida: " . $conn->connect_error);
  }

  $sql = "INSERT INTO reviews (idViaje, rating, comentarios) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iis", $idViaje, $rating, $comentarios);

  if ($stmt->execute()) {
      echo "Review enviado correctamente.";
  } else {
      echo "Error al enviar el review: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewComentarios']) && isset($_POST['reviewRating'])) {
  $comentarios = $_POST['reviewComentarios'];
  $rating = $_POST['reviewRating'];
  $idViaje = 1;
  insertarReview($idViaje, $rating, $comentarios);
}



?>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Historial de Viajes</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
      <link rel="stylesheet" href="css/barra.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>

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

    <div class="container mt-5">
  <h2>Historial de Viajes</h2>
  <?php
    $historial = obtenerHistorialViajes($cred[0]);
    while ($row = $historial->fetch_assoc()) {
      echo '<div class="container-fluid">
              <div class="container mt-1">
                <div class="row justify-content-center">
                  <div class="col-lg-8">
                    <div class="card">
                      <div class="card-body">
                        <div class="form-group">
                          <label>Destino: ' . htmlspecialchars($row['destino']) . '</label>
                        </div>
                        <div class="form-group">
                          <label>Fecha: ' . htmlspecialchars($row['createdAt']) . '</label>
                        </div>
                        <div class="form-group">
                          <label>Costo: $' . htmlspecialchars($row['costo_viaje']) . '</label>
                        </div>
                        <div class="form-group">
                          <label>Cuota de Ganancia: $' . htmlspecialchars($row['cuota_ganancia']) . '</label>
                        </div>
                        <div class="form-group">
                          <label>Matrícula: ' . htmlspecialchars($row['idMatricula']) . '</label>
                        </div>
                        <div class="d-flex justify-content-end">
                          <!-- El botón de queja se oculta si la queja ya fue enviada -->
                          <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalQueja" id="btnQueja' . $row['idMatricula'] . '" ' . ($quejaEnviada ? 'style="display:none;"' : '') . '>Queja</a>
                          <a href="reviewViaje.html" class="btn btn-primary btn-success ms-2" data-bs-toggle="modal" data-bs-target="#modalReview">Review</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
    }
  ?>
</div>


    <!-- Modal de Queja -->
    <div class="modal fade" id="modalQueja" tabindex="-1" aria-labelledby="modalQuejaLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalQuejaLabel">Queja</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="" method="POST">
              <div class="form-group mb-3">
                <label for="comentarios" class="form-label">Comentarios</label>
                <textarea class="form-control" id="comentarios" name="comentarios" required></textarea>
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-success" id="mandarQueja">Enviar queja</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    
<div class="modal fade" id="modalReview" tabindex="-1" aria-labelledby="modalReviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-success" id="modalReviewLabel">Review del Viaje</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="review">
        <div class="form-group">
                          <label for="atendido" class="form-label">Rating (1 - 10)</label> 
                          <!-- <input type="number" class="form-control ancho-input-2" id="cantidad" name="atendido" min="0" max="10" step="1" value="0"> -->
                          <div id="rateYo"></div> 
                          <script type="text/javascript" src="jquery.min.js"></script> 
                          <script type="text/javascript" src="jquery.rateyo.min.js"></script> 
                          <script> 
                          $("#rateYo").rateYo({ 
                          rating:  5, 
                          spacing: "36px", 
                          numStars: 10, 
                          minValue: 0, 
                          maxValue: 10, 
                          normalFill: 'grey', 
                          ratedFill: 'orange',}) </script> 
                        </div>
                        <br>
                        <div class="form-group">
                          <label for="comentarios" class="form-label">Comentarios</label>
                          <textarea type="text" class="form-control ancho-input-2" id="comentarios" required> </textarea>
                          </div>
                          <br>
                      
                      <br>
                      <button class="btn color white" type="submit" id="mandarReview">Enviar review</button>

                      
      </form>
      </div>
    </div>
  </div>
</div>
  </body>
</html>

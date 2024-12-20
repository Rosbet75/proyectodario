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

  $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE nickname = ? AND contra = ?";
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

  $sql = "SELECT COUNT(*) AS total FROM admins WHERE nickname = ? AND contra = ?";
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
  

  if (isset($_POST['unlog'])) {
    setcookie("logeo", "", time() - 3600, "/", $_SERVER['SERVER_ADDR']);
    header("Location: login.php");
    exit;
  }

  if (verificarCredencialesAdmin($cred[0], $cred[1])) {
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
      die("Conexion fallida: " . $conn->connect_error);
  }

  $sql = "SELECT idViaje, destino, costo_viaje, cuota_ganancia, idMatricula, createdAt FROM viajes WHERE idUsuario = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $nickname);
  $stmt->execute();
  $resultado = $stmt->get_result();

  $stmt->close();
  $conn->close();

  return $resultado;
}

$quejaEnviada = false;
$reviewEnviado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['comentarios']) && isset($_POST['idViaje']) && !empty($_POST['idViaje'])) {
    $comentarios = $_POST['comentarios'];
    $idViaje = $_POST['idViaje'];  
    insertarQueja($idViaje, $comentarios);
    $quejaEnviada = true;
  }

  if (isset($_POST['reviewComentarios']) && isset($_POST['reviewRating']) && isset($_POST['idViaje']) && !empty($_POST['idViaje'])) {
    $comentarios = $_POST['reviewComentarios'];
    $rating = $_POST['reviewRating'];
    $idViaje = $_POST['idViaje'];  
    insertarReview($idViaje, $rating, $comentarios);
    $reviewEnviado = true;
  }
}

function insertarQueja($idViaje, $comentarios) {
  $servername = "localhost";
  $username = "root";
  $password = "eneto";
  $dbname = "eneto";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Conexion fallida: " . $conn->connect_error);
  }

  $sql = "INSERT INTO queja (idViaje, comentarios, atendido) VALUES (?, ?, 0)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("is", $idViaje, $comentarios);

  if ($stmt->execute()) {
      echo "<div class='alert alert-success mt-4'>Queja enviada</div>";
  } else {
      echo "Error al enviar la queja: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
}

function insertarReview($idViaje, $rating, $comentarios) {
  $servername = "localhost";
  $username = "root";
  $password = "eneto";
  $dbname = "eneto";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Conexion fallida: " . $conn->connect_error);
  }

  $sql = "INSERT INTO reviews (idViaje, rating, comentarios) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iis", $idViaje, $rating, $comentarios);

  if ($stmt->execute()) {
      echo "<div class='alert alert-success mt-4'>Review enviado correctamente. </div>";
  } else {
      echo "Error al enviar el review: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
}
?>

<?php
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historial de Viajes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="css/barra.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css" rel="stylesheet"/>
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary color">
    <div class="container-fluid color">
      <a class="navbar-brand white" href="barra.php">Eneto.Inc</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
              <button type="submit" form="logout" class="btn color white">Log out</button>
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
                            <label>Id del Viaje: ' . htmlspecialchars($row['idViaje']) . '</label>
                          </div>
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
                            <label>Matricula: ' . htmlspecialchars($row['idMatricula']) . '</label>
                          </div>
                          <div class="d-flex justify-content-end">
                            <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalQueja" 
                            data-id="' . $row['idViaje'] . '">Queja</a>
                            <button type="button" class="btn color white" data-bs-toggle="modal" data-bs-target="#reviewModal" 
                            data-id="' . $row['idViaje'] . '">Review</button>
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


  <div class="modal fade" id="modalQueja" tabindex="-1" aria-labelledby="modalQuejaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalQuejaLabel">Enviar Queja</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" method="post">
            <input type="hidden" id="idViajeQueja" name="idViaje">
            <div class="form-group">
              <label for="comentariosQueja">Comentarios</label>
              <textarea class="form-control" id="comentariosQueja" name="comentarios" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-danger">Enviar Queja</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


</div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> 
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reviewModalLabel">Dejar Review</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" method="post">
          <input type="hidden" id="idViajeReview" name="idViaje">
          <div class="form-group">
            <label for="rating">Calificacion</label>
            <div id="rating" class="mb-2"></div>
            <input type="hidden" id="reviewRating" name="reviewRating">
          </div>
          <div class="form-group">
            <label for="comentariosReview">Comentarios</label>
            <textarea class="form-control" id="comentariosReview" name="reviewComentarios" rows="3" required></textarea>
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary mt-3">Enviar Review</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function() {
  $("#rating").rateYo({
    rating: 0, // empieza a 0
    numStars: 10, // numero de estrellas
    fullStar: true, // el selector de 1 estrella
    maxValue: 10, // el maximo que puee haber
    precision: 0, // estara complemtamente seleccionada
    starWidth: "75px", // tamaño de las estrellas
    onSet: function(rating, rateYoInstance) {
      $('#reviewRating').val(rating); // asi se guarda el raiting
      console.log("Calificacion seleccionada: " + rating); // muestra la calificacion
    }
  });
  $('#modalQueja').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var idViaje = button.data('id');
            var modal = $(this);
            modal.find('#idViajeQueja').val(idViaje);
        });
  $('#reviewModal').on('show.bs.modal', function(e) {
    var viajeId = $(e.relatedTarget).data('id');
    $('#idViajeReview').val(viajeId);
  });
});

</script>

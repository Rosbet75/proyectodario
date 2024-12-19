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
?>

<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Barra Menu</title>
      
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
      <link rel="stylesheet" href="css/barra.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css" rel="stylesheet"/>
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
        <br>
        <div class="container-fluid">
          <div class="container mt-9">
            <div class="column justify-content-center">
              <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="form-group">
                    <label for="">Destino: a</label>
                    
                  </div>
                  <div class="form-group">
                    <label for="">Fecha: dd/mm/yyyy</label>
                    
                  </div>
                  <div class="form-group">
                    <label for="">Costo: $xxxx.xx</label>
                    
                  </div>
                  <div class="d-flex justify-content-end">
                    <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalQueja">Queja</a>
                    <a href="reviewViaje.html" class="btn btn-primary btn-success ms-2" data-bs-toggle="modal" data-bs-target="#modalReview">Review</a>
                </div>
                
                
                
                </div>
              </div>
              </div>
            </div>
            
          </div>
        </div>
        <br>
        <div class="container-fluid">
          <div class="container mt-9">
            <div class="column justify-content-center">
              <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="form-group">
                    <label for="">Destino: a</label>
                    
                  </div>
                  <div class="form-group">
                    <label for="">Fecha: dd/mm/yyyy</label>
                    
                  </div>
                  <div class="form-group">
                    <label for="">Costo: $xxxx.xx</label>
                  </div>
                  <div class="form-group">
                    <label for="">Queja: <span style="color: red;">Pendiente</span></label>
                  </div>
                </div>
              </div>
              </div>
            </div>
          </div>
        </div>
        <br>
        <div class="container-fluid">
          <div class="container mt-9">
            <div class="column justify-content-center">
              <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                  <div class="form-group">
                    <label for="">Destino: a</label>
                    
                  </div>
                  <div class="form-group">
                    <label for="">Fecha: dd/mm/yyyy</label>
                    
                  </div>
                  <div class="form-group">
                    <label for="">Costo: $xxxx.xx</label>
                  </div>
                  <div class="form-group">
                    <label for="">Review: <span style="color: red;">5/10</span></label>
                  </div>
                </div>
              </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="modalQueja" tabindex="-1" aria-labelledby="modalQuejaLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalQuejaLabel">Queja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="quejaForm">
                  <div class="form-group mb-3">
                    <label for="comentarios" class="form-label">Comentarios</label>
                    <textarea class="form-control" id="comentarios" required></textarea>
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
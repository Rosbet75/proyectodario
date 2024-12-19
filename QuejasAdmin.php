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
        setcookie("logeo", "", time() - 3600, "/", $_SERVER['SERVER_ADDR']);
        header("Location: login.php");
        exit;
      }
  }
} else {
  header("Location: login.php");
  exit;
}
//----------------------------------------------------------------------------------------------
$cnn = new mysqli("localhost", "root", "eneto", "eneto");
if(mysqli_connect_errno()){
    echo $cnn->connect_error;
    exit();
}
$recientes = isset($_GET['recientes']) ? $_GET['recientes'] : '1';
$pendientes = isset($_GET['pendientes']) ? $_GET['pendientes'] : '1';


if(isset($_POST['actualizar'])){
    $viaje = $_POST['viaje'];
    $correo = $_POST['correo'];
    $privilegios = $_POST['privilegios'];

    $stmt = $cnn->prepare("UPDATE admins SET correo = ?, privilegios = ? WHERE nickname = ?");
    $stmt->bind_param("sis", $correo, $privilegios, $nickname);

    if ($stmt->execute()) {
        echo "Admin actualizado correctamente.";
    } else {
        echo "Error al actualizar el admin: " . $stmt->error;
    }
    $stmt->close();
    if(isset($_POST['contra']) & $_POST['contra'] != ""){
        $upt = $cnn -> query("update admins set contra = '{$_POST['contra']}' where nickname = '{$nickname}';");
        if ($upt) {
            echo "Amins actualizado correctamente.";
        } else {
            echo "Error al actualizar el admin: " . $cnn->error;
        }
    }

} else if (isset($_POST['eliminar'])){
    $del = $cnn->query("DELETE FROM queja WHERE idQueja = '{$_POST['quejaId']}';");
    if ($del) {
        echo "Queja eliminada correctamente.";
    } else {
        echo "Error al eliminar la queja: " . $cnn->error;
    }
} else if (isset($_POST['marcar'])){
  $del = $cnn->query("update queja set atendido = TRUE WHERE idQueja = '{$_POST['quejaId']}';");
  if ($del) {
    echo "Queja marcada correctamente.";
  } else {
    echo "Error al marcas queja: " . $cnn->error;
  }
} else if (isset($_POST['citacion'])){
  $sancion = $_POST['sancion'] == "" ? null : $_POST['sancion'];
  $conceptoCita = $_POST['conceptoCita'] == "" ? null : $_POST['conceptoCita'];
  $stmt = $cnn->prepare("INSERT INTO citas (idChofer, fechaCita, concepto, comentarios, idQueja, sancion) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssii", $_POST['idChofer'], $_POST['fechaCita'], $conceptoCita, $_POST['comentario'], $_POST['quejaId'], $sancion);

    if ($stmt->execute()) {
        echo "Admin actualizado correctamente.";
    } else {
        echo "Error al actualizar el admin: " . $stmt->error;
    }
    $del = $cnn->query("update queja set atendido = TRUE WHERE idQueja = '{$_POST['quejaId']}';");
  if ($del) {
    echo "Queja marcada correctamente.";
  } else {
    echo "Error al marcas queja: " . $cnn->error;
  }
}
$opcionesSancion = "";
$querySancion = "select * from conceptoSanciones;";
$sanc = $cnn -> query($querySancion);
while($row = $sanc -> fetch_array(MYSQLI_ASSOC)){
  $opcionesSancion .= "<option value='{$row['idSancion']}'>{$row['sancion']}</option>";
}
$opcionesCitas = "";
$queryCitas = "select * from conceptoCitas;";
$citas = $cnn -> query($queryCitas);
while($row = $citas -> fetch_array(MYSQLI_ASSOC)){
  $opcionesCitas .= "<option value='{$row['idConcepto']}'>{$row['concepto']}</option>";
}
$meagarras = "select * from queja join viajes on viajes.idViaje = queja.idViaje join choferes on choferes.idChofer = viajes.idChofer join empleados on empleados.curp = choferes.curp order by queja.createdAt ";

if ($recientes == "1") {
    $meagarras .= "desc, atendido ";
} else {
    $meagarras .= "asc, atendido ";
}
if ($recientes == "1") {
  $meagarras .= "asc;";
} else {
  $meagarras .= "desc;";
}


$consul = $cnn->query($meagarras);
$tablas = "";

while($ren = $consul -> fetch_array(MYSQLI_ASSOC)){

 $pendiente = $ren['atendido'] == 0 ? "Pendiente" : "Atendido";
 $marcador = "";
 if($ren['atendido'] == 0){
  $marcador = "<button type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#modalCita{$ren['idQueja']}'>
                  Citar
                </button>                
                <button class='btn btn-primary btn-danger ms-2' data-bs-toggle='modal' data-bs-target='#modalSancion{$ren['idQueja']}'>Sancionar</button>
  <form method='post' name='marcAtend'>
                  <input type='hidden' name='quejaId' value='{$ren['idQueja']}'>
                  <input type='hidden' name='marcar' value='1'>
                  <button class='btn btn-primary btn-success ms-2'>Marcar como atendido</button>
                </form>";
 }
    $tablas .= "
<br>
    <div class='container-fluid'>
      <div class='container mt-9'>
        <div class='column justify-content-center'>
          <div class='col-lg-12'>
          <div class='card'>
            <div class='card-body'>
              <div class='form-group'>
                <label for=''>Viaje: 
                  <button type='button' class='btn btn-link p-0' data-bs-toggle='modal' data-bs-target='#modalViaje'>
                    {$ren['idViaje']}
                  </button>
                </label>
              </div>
              <div class='form-group'>
                <label for=''>Comentarios: {$ren['comentarios']}</label>
              </div>
              <div class='form-group'>
                <label for=''>Atendido: <span style='color: red;'>{$pendiente}</span></label>
              </div>
              <div class='form-group'>
                <label for=''>Fecha: {$ren['createdAt']}</label>
              </div>
              <div class='form-group'>
                <label for=''>Chofer: {$ren['nombre']} {$ren['apellidoPaterno']} {$ren['apellidoMaterno']}</label>
              </div>
              <div class='d-flex justify-content-end'>
                
                {$marcador}
              </div>
            </div>
          </div>
          </div>
        </div>
        
      </div>
    </div>
   

    <div class='modal fade' id='modalCita{$ren['idQueja']}' tabindex='-1' aria-labelledby='modalCitaLabel' aria-hidden='true'>
      <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h5 class='modal-title' id='modalCitaLabel'>Nueva Cita</h5>
            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>
          <div class='modal-body'>
            <form id='formCita' method='post'>
            <input type='hidden' value='citacion' name='citacion'>
            <input type='hidden' value='{$ren['idQueja']} name='quejaId'>

              <div class='row mb-3'>
                <div class='col-md-6 '>
                  <label for='fechaCita' class='form-label'>Id del Chofer</label>
                  <input type='text' class='form-control ancho-input-2' value='{$ren['idChofer']}' id='IdChofer' readonly name='idChofer'>
                </div>
                <div class='col-md-6 '>
                  <label for='fechaCita' class='form-label'>Fecha de Cita</label>
                  <input type='datetime-local' class='form-control ancho-input-2' id='fechaCita' required name='fechaCita'>
                </div>
                
              </div>

              <div class='row mb-3'>
                <div class='col-md-6'>
                  <label for='concepto' class='form-label'>Concepto</label>
                  <select class='form-select' id='concepto' name='conceptoCita'>
                    <option value=''>Seleccionar concepto</option>
                    {$opcionesCitas}
                  

                  </select>
                </div>
                <div class='col-md-6'>
                  <label for='sancion' class='form-label'>Sancion</label>
                  <select class='form-select' id='sancion' name='sancion'>
                    <option value=''>Seleccionar sancion</option>
                    {$opcionesSancion}

                  </select>
                </div>
              </div>

              <div class='mb-3'>
                <label for='comentarios' class='form-label'>Comentarios</label>
                <textarea class='form-control' id='comentarios' rows='4' required name='comentario'></textarea>
              </div>
            
          </div>
          <div class='modal-footer'>
            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
            <button type='submit' form='formCita' class='btn color white'>Guardar Cita</button>
          </div>
          </form>
        </div>
      </div>
    </div>
    
    
    
    <div class='modal fade' id='modalViaje{$ren['idQueja']}' tabindex='-1' aria-labelledby='modalViajeLabel' aria-hidden='true'>
      <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h5 class='modal-title' id='modalViajeLabel'>Informacion del Viaje</h5>
            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>
          <div class='modal-body'>
            <div class='container-fluid'>
              <div class='row mb-3'>
                <div class='col-md-6'>
                  <label class='form-label fw-bold'>ID del Viaje</label>
                  <input type='text' class='form-control' value='?' readonly>
                </div>
                <div class='col-md-6'>
                  <label class='form-label fw-bold'>Fecha de Creacion</label>
                  <input type='text' class='form-control'  value='?' readonly>
                </div>
              </div>
              
              <div class='row mb-3'>
                <div class='col-md-6'>
                  <label class='form-label fw-bold'>ID del Chofer</label>
                  <input type='text' class='form-control' value='?' readonly>
                </div>
                <div class='col-md-6'>
                  <label class='form-label fw-bold'>ID del Usuario</label>
                  <input type='text' class='form-control' value='?' readonly>
                </div>
              </div>
    
              <div class='row mb-3'>
                <div class='col-md-12'>
                  <label class='form-label fw-bold'>Destino</label>
                  <input type='text' class='form-control' value='?' readonly>
                </div>
              </div>
    
              <div class='row mb-3'>
                <div class='col-md-6'>
                  <label class='form-label fw-bold'>Costo del Viaje</label>
                  <input type='text' class='form-control' value='?' readonly>
                </div>
                <div class='col-md-6'>
                  <label class='form-label fw-bold'>Cuota de Ganancia</label>
                  <input type='text' class='form-control'  value='?' readonly>
                </div>
              </div>
    
              <div class='row mb-3'>
                <div class='col-md-6'>
                  <label class='form-label fw-bold'>Matrícula del Vehiculo</label>
                  <input type='text' class='form-control'  value='?' readonly>
                </div>
                <div class='col-md-6'>
                  <label class='form-label fw-bold'>Última Actualizacion</label>
                  <input type='text' class='form-control'  value='?' readonly>
                </div>
              </div>
            </div>
          </div>
          <div class='modal-footer'>
            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <div class='modal fade' id='modalSancion' tabindex='-1' aria-labelledby='modalSancionLabel' aria-hidden='true'>
      <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h5 class='modal-title' id='modalSancionLabel'>Sancionar</h5>
            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>
          <div class='modal-body'>
            <form id='formSancion'>
              <div class='row mb-3'>
               
                <div class='col-md-15 '>
                  <label for='sancion' class='form-label'>Concepto</label>
                  <input type='text' class='form-control ancho-input-2' id='sancion' required maxlength='100'>
                </div>
              </div>
    
            </form>
          </div>
          <div class='modal-footer'>
            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
            <button type='submit' form='formSancion' class='btn color white'>Guardar Sanción</button>
          </div>
        </div>
      </div>
    </div>

";

};

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quejas Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
            <a class="nav-link white" href="pagosAdmin.php">Pagos</a>
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
    <div class="container-fluid">
        <div class="d-flex justify-content-end">
        <form action="" method="get" class="d-flex justify-content-end">
            <input type="hidden" name="fecha" value="1">
            <input type="hidden" name="atencion" value="1">
            <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="fecha" onchange="javascript:this.form.submit(); console.log('Form submitted!')">
              <option value="1" >Mas reciente</option>
              <option value="2" >Mas antiguo</option>
            </select>
            
            <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="atencion" onchange="javascript:this.form.submit(); console.log('Form submitted!')">
              <option value="1" >Pendientes primero</option>
              <option value="2" >Atendidos primero</option>
            </select>
            
          </form>
        </div>
    </div>
    <?php echo $tablas; ?>
</body>
</html>
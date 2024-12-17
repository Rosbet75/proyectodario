<?php
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
  $del = $cnn->query("DELETE FROM queja WHERE idQueja = '{$_POST['quejaId']}';");
  if ($del) {
    echo "Queja marcada correctamente.";
  } else {
    echo "Error al marcas queja: " . $cnn->error;
  }
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
  $marcador = "<form method='post' name='marcAtend'>
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
                <button type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#modalCita{$ren['idQueja']}'>
                  Citar
                </button>                
                <button class='btn btn-primary btn-danger ms-2' data-bs-toggle='modal' data-bs-target='#modalSancion{$ren['idQueja']}'>Sancionar</button>
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
            <form id='formCita'>
              <div class='row mb-3'>
                <div class='col-md-6 '>
                  <label for='fechaCita' class='form-label'>Id del Chofer</label>
                  <input type='text' class='form-control ancho-input-2' value='{$ren['idChofer']}' id='IdChofer' readonly>
                </div>
                <div class='col-md-6 '>
                  <label for='fechaCita' class='form-label'>Fecha de Cita</label>
                  <input type='datetime-local' class='form-control ancho-input-2' id='fechaCita' required>
                </div>
                
              </div>

              <div class='row mb-3'>
                <div class='col-md-6'>
                  <label for='concepto' class='form-label'>Concepto</label>
                  <select class='form-select' id='concepto' required>
                    <option value=''>Seleccionar concepto</option>
                    <option value='1'>motivo1</option>

                  </select>
                </div>
                <div class='col-md-6'>
                  <label for='sancion' class='form-label'>Sancion</label>
                  <select class='form-select' id='sancion' required>
                    <option value=''>Seleccionar sancion</option>
                    <option value='1'>sancion1</option>

                  </select>
                </div>
              </div>

              <div class='mb-3'>
                <label for='comentarios' class='form-label'>Comentarios</label>
                <textarea class='form-control' id='comentarios' rows='4' required></textarea>
              </div>
            </form>
          </div>
          <div class='modal-footer'>
            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
            <button type='submit' form='formCita' class='btn color white'>Guardar Cita</button>
          </div>
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
              <a class="dropdown-item" href="administradorAdmin.html">Crear Usuario Administrador</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="editareliminarAdministrador.html">Editar/Eliminar Usuario Administrador</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Empleados
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="empleadosAdmin.html">Registrar Empleado</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="editareliminarEmpleado.html">Editar/Eliminar Empleado</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Choferes
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="choferesAdmin.html">Registrar Chofer</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="editareliminarChofer.html">Editar/Eliminar Chofer</a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="QuejasAdmin.html">Quejas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="CitasAdmin.html">Citas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="pagos.html">Pagos</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Quejas
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#">Action</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">Another action</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Quejas
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#">Action</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">Another action</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
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
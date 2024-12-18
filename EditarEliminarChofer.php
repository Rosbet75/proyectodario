<?php
$cnn = new mysqli("localhost", "root", "eneto", "eneto");
if(mysqli_connect_errno()){
    echo $cnn->connect_error;
    exit();
}
$quejas = isset($_GET['quejas']) ? $_GET['quejas'] : '1';
$viajes = isset($_GET['viajes']) ? $_GET['viajes'] : '1';

if(isset($_POST['actualizar'])){
    $idChofer = $_POST['idChofer'];
    $curp = $_POST['curp'];
    $numLic = $_POST['num_licencia'];
    $upt = $cnn -> query("update choferes set num_licencia = '{$numLic}' where idChofer = '{$idChofer}';");

    if ($upt) {
        echo "Chofer actualizado correctamente.";
    } else {
        echo "Error al actualizar el chofer: " . $cnn->error;
    }

} else if (isset($_POST['eliminar'])){
    $idChofer = $_POST['idChofer'];
    $del = $cnn->query("DELETE FROM choferes WHERE idChofer = {$idChofer}");
    if ($del) {
        echo "Chofer eliminado correctamente.";
    } else {
        echo "Error al eliminar el chofer: " . $cnn->error;
    }
}
$meagarras = "SELECT 
    c.idChofer,
    c.curp,
    e.nombre AS nombreChofer,
    e.apellidoPaterno,
    e.apellidoMaterno,
    c.num_licencia,
    IFNULL(COUNT(v.idViaje), 0) AS totalViajes,
    IFNULL(SUM(CASE WHEN q.idQueja IS NOT NULL THEN 1 ELSE 0 END), 0) AS totalQuejas
FROM 
    choferes c
INNER JOIN 
    empleados e ON c.curp = e.curp
LEFT JOIN 
    viajes v ON c.idChofer = v.idChofer
LEFT JOIN 
    queja q ON v.idViaje = q.idViaje
GROUP BY 
    c.idChofer, e.nombre, e.apellidoPaterno, e.apellidoMaterno, c.num_licencia 
ORDER BY 
    totalQuejas ";

if ($quejas == "1") {
    $meagarras .= "DESC, ";
} else {
    $meagarras .= "ASC, ";
}

$meagarras .= "totalViajes ";

if ($viajes == "1") {
    $meagarras .= "DESC;";
} else {
    $meagarras .= "ASC;";
}

$consul = $cnn->query($meagarras);

$tablas = "";
while($ren = $consul -> fetch_array(MYSQLI_ASSOC)){
 $tablas .= "<br>
            <div class='container-fluid'>
  <div class='container mt-9'>
    <div class='column justify-content-center'>
      <div class='col-lg-12'>
        <div class='card'>
          <div class='card-body'>
            <div class='row mb-3'>
                <div class='col-md-4'>
                    <div class='form-group'>
                    <label for=''>ID: {$ren['idChofer']}</label>
                    </div>
                    <div class='form-group'>
                    <label for=''>CURP: {$ren['curp']}</label>
                    </div>
                    <div class='form-group'>
                    <label for=''>Numero de Licencia: {$ren['num_licencia']}</label>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                    <label for=''>Quejas: {$ren['totalQuejas']}</label>
                    </div>
                    <div class='form-group'>
                    <label for=''>Viajes: {$ren['totalViajes']}</label>
                    </div>
                </div>
            </div>
            <div class='d-flex justify-content-end'>
              <button type='button' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#modalActualizar{$ren['idChofer']}'>
                Actualizar
              </button>
              <button type='button' class='btn btn-danger ms-2' data-bs-toggle='modal' data-bs-target='#modalEliminar{$ren['idChofer']}'>
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<br>

<div class='modal fade' id='modalActualizar{$ren['idChofer']}' tabindex='-1' aria-labelledby='modalActualizarLabel' aria-hidden='true'>
  <div class='modal-dialog modal-lg'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='modalActualizarLabel'>Actualizar Chofer</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <form id='formActualizar' method='post'>
      <div class='modal-body'>
        <input type='hidden' name='actualizar' value='1'>
          <div class='row mb-3'>
            <div class='col-md-4'>
              <label class='form-label'>ID</label>
              <input type='text' class='form-control' value='{$ren['idChofer']}' readonly name='idChofer'>
              <small class='text-muted'>El ID no se puede modificar</small>
            </div>
            <div class='col-md-4'>
              <label class='form-label'>CURP</label>
              <input type='text' class='form-control' value='{$ren['curp']}' maxlength='18' required name='curp' readonly>
              <small class='text-muted'>La CURP no se puede modificar</small>
            </div>
            <div class='col-md-4'>
              <label class='form-label'>Numero de Licencia</label>
              <input type='text' class='form-control' value='{$ren['num_licencia']}' maxlength='12' required name='num_licencia'>
            </div>
          </div>
        
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
        <button type='submit' form='formActualizar' class='btn btn-warning'>Guardar Cambios</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class='modal fade' id='modalEliminar{$ren['idChofer']}' tabindex='-1' aria-labelledby='modalEliminarLabel' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='modalEliminarLabel'>Confirmar Eliminacion</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <div class='modal-body'>
        <p>¿Esta seguro que desea eliminar al chofer con ID <strong>{$ren['idChofer']}</strong>?</p>
        <p class='text-danger'><strong>Esta accion no se puede deshacer.</strong></p>
      </div>
      <div class='modal-footer'>
      <form method='post' name='eliminacion'>
        <input type='hidden' name='idChofer' value='{$ren['idChofer']}'>
        <input type='hidden' name='eliminar' value='1'>
        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
        <button type='submit' class='btn btn-danger'>Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

";

};

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choferes</title>
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
        </ul>
      </div>
    </div>
  </nav>
  <div class="container-fluid">
        <div class="d-flex justify-content-end">
          <form action="" method="get" class="d-flex justify-content-end">
            <input type="hidden" name="viajes" value="1">
            <input type="hidden" name="quejas" value="1">
            <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="viajes" onchange="javascript:this.form.submit(); console.log('Form submitted!')">
              <option value="1" <?php if($viajes == 1) echo "selected"?>>Mas viajes primero</option>
              <option value="2" <?php if($viajes == 2) echo "selected"?>>Menos viajes primero</option>
            </select>
            
            <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="quejas" onchange="javascript:this.form.submit(); console.log('Form submitted!')">
              <option value="1" <?php if($quejas == 1) echo "selected"?>>Mas quejas primero</option>
              <option value="2" <?php if($quejas == 2) echo "selected"?>>Menos quejas primero</option>
            </select>
            
          </form>
            </div>
        </div>
    </div>

    <br>

    <?php echo $tablas; ?>
</body>
</html>
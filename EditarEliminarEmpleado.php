<?php
$cnn = new mysqli("localhost", "root", "eneto", "eneto");
if(mysqli_connect_errno()){
    echo $cnn->connect_error;
    exit();
}
$cargo = isset($_GET['cargo']) ? $_GET['cargo'] : '1';

if(isset($_POST['actualizar'])){
    $curp = $_POST['curp'];
    $apellidomaterno = $_POST['apellidomaterno'];
    $nombre = $_POST['nombre'];
    $idCargo = $_POST['idCargo'];
    $horarioentrada = $_POST['horarioentrada'];
    $horariosalida = $_POST['horariosalida'];
    $apellidopaterno = $_POST['apellidopaterno'];

    $stmt = $cnn->prepare("UPDATE empleados SET apellidopaterno = ?, apellidomaterno = ?, nombre = ?, idCargo = ?, horarioentrada = ?, horariosalida = ? WHERE curp = ?;");
    $stmt->bind_param("sssisss", $apellidopaterno, $apellidomaterno, $nombre, $idCargo, $horarioentrada, $horariosalida, $curp);

    if ($stmt->execute()) {
        echo "Empleado actualizado correctamente.";
    } else {
        echo "Error al actualizar el empleado: " . $stmt->error;
    }
    $stmt->close();
    

} else if (isset($_POST['eliminar'])){
    $curp = $_POST['curp'];
    $del = $cnn->query("DELETE FROM empleados WHERE curp = '{$curp}';");
    if ($del) {
        echo "Empleado eliminado correctamente.";
    } else {
        echo "Error al eliminar el empleado: " . $cnn->error;
    }
}
$meagarras = "select * from empleados join cargoLaboral on empleados.idCargo = cargoLaboral.idCargo";

if ($cargo == "1") {
    $meagarras .= ";";
} else if ($cargo == "2") {
    $meagarras .= " where empleados.idCargo = 1;";
} else if ($cargo == "3"){
  $meagarras .= " where empleados.idCargo = 2;";
} else {
  $meagarras .= " where empleados.idCargo = 3;";
}

$consul = $cnn->query($meagarras);
$tablas = "";
while($ren = $consul -> fetch_array(MYSQLI_ASSOC)){
    $tablas .= "

<br>
<div class='container-fluid'>
  <div class='container mt-9'>
    <div class='column justify-content-center'>
      <div class='col-lg-12'>
        <div class='card'>
          <div class='card-body'>
            <div class='form-group'>
              <label for=''>CURP: {$ren['curp']}</label>
            </div>
            <div class='form-group'>
              <label for=''>Nombre: {$ren['nombre']} {$ren['apellidoPaterno']} {$ren['apellidoMaterno']}</label>
            </div>
            <div class='form-group'>
              <label for=''>Cargo: <span style='color: rgb(93, 209, 255);'>{$ren['cargo']}</span></label>
            </div>
            <div class='form-group'>
              <label for=''>Horario: {$ren['horarioEntrada']} - {$ren['horarioSalida']}</label>
            </div>
            <div class='form-group'>
              <label for=''>Fecha de Registro: {$ren['createdAt']}</label>
            </div>
            <div class='d-flex justify-content-end'>
              <button type='button' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#modalActualizar{$ren['curp']}'>
                Actualizar
              </button>
              <button type='button' class='btn btn-danger ms-2' data-bs-toggle='modal' data-bs-target='#modalEliminar{$ren['curp']}'>
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

<div class='modal fade' id='modalActualizar{$ren['curp']}' tabindex='-1' aria-labelledby='modalActualizarLabel' aria-hidden='true'>
  <div class='modal-dialog modal-lg'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='modalActualizarLabel'>Actualizar Empleado</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <form id='formActualizar' method='post'>
      <input type='hidden' name='actualizar' value='1'>
      <div class='modal-body'>
        
          <div class='row mb-3'>
            <div class='col-md-6'>
              <label class='form-label'>CURP</label>
              <input type='text' class='form-control' value='{$ren['curp']}' readonly name='curp'>
              <small class='text-muted'>La CURP no se puede modificar</small>
            </div>
            <div class='col-md-6'>
              <label class='form-label'>Cargo</label>
              <select class='form-select' required name='idCargo'>
                <option value='1'>Jefe de comunicaciones</option>
                <option value='2'>Atencion de radio</option>
                <option value='3'>Chofer</option>
              </select>
            </div>
          </div>
          
          <div class='row mb-3'>
            <div class='col-md-4'>
              <label class='form-label'>Nombre</label>
              <input type='text' class='form-control' value='{$ren['nombre']}' required name='nombre'>
            </div>
            <div class='col-md-4'>
              <label class='form-label'>Apellido Paterno</label>
              <input type='text' class='form-control' value='{$ren['apellidoPaterno']}' required name='apellidopaterno'>
            </div>
            <div class='col-md-4'>
              <label class='form-label'>Apellido Materno</label>
              <input type='text' class='form-control' value='{$ren['apellidoMaterno']}' required name='apellidomaterno'>
            </div>
          </div>

          <div class='row mb-3'>
            <div class='col-md-6'>
              <label class='form-label'>Horario Entrada</label>
              <input type='time' class='form-control' value='{$ren['horarioEntrada']}' required name='horarioentrada'>
            </div>
            <div class='col-md-6'>
              <label class='form-label'>Horario Salida</label>
              <input type='time' class='form-control' value='{$ren['horarioSalida']}' required name='horariosalida'>
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

<div class='modal fade' id='modalEliminar{$ren['curp']}' tabindex='-1' aria-labelledby='modalEliminarLabel' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='modalEliminarLabel'>Confirmar Eliminacion</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <div class='modal-body'>
        <p>¿Esta seguro que desea eliminar al empleado con CURP <strong>{$ren['curp']}</strong>?</p>
        <p class='text-danger'><strong>Esta accion no se puede deshacer.</strong></p>
      </div>
      <div class='modal-footer'>
        <form method='post' name='eliminacion'>
            <input type='hidden' name='curp' value='{$ren['curp']}'>
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
$cnn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados</title>
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
              <a class="dropdown-item" href="empleadosAdmin.php">Registrar Empleado</a>
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
            <input type="hidden" name="cargo" value="1">
            <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="cargo" onchange="javascript:this.form.submit(); console.log('Form submitted!')">
              <option value="1" <?php if($cargo == 1) echo "selected" ?>>Todos</option>
              <option value="2" <?php if($cargo == 2) echo "selected" ?>>Jefe de comunicaciones</option>
              <option value="3" <?php if($cargo == 3) echo "selected" ?>>Atencion de radio</option>
              <option value="4" <?php if($cargo == 4) echo "selected" ?>>Chofer</option>
            </select>
          </form>
        </div>
    </div>

    <?php echo $tablas;?>

</body>
</html>
<?php
$cnn = new mysqli("localhost", "root", "eneto", "eneto");
if(mysqli_connect_errno()){
    echo $cnn->connect_error;
    exit();
}
$privilegios = isset($_GET['privilegios']) ? $_GET['privilegios'] : '1';


if(isset($_POST['actualizar'])){
    $nickname = $_POST['nickname'];
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
    $nickname = $_POST['nickname'];
    $del = $cnn->query("DELETE FROM admins WHERE nickname = '{$nickname}';");
    if ($del) {
        echo "Admins eliminado correctamente.";
    } else {
        echo "Error al eliminar el admin: " . $cnn->error;
    }
}
$meagarras = "select * from admins join privilegios on privilegios.idPriv = admins.privilegios where rol = '";

if ($privilegios == "1") {
    $meagarras .= "root';";
} else {
    $meagarras .= "admin';";
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
            <div class='form-group'>
              <label for=''>Nickname: {$ren['nickname']}</label>
            </div>
            <div class='form-group'>
              <label for=''>Correo: {$ren['correo']}</label>
            </div>
            <div class='form-group'>
              <label for=''>CURP: {$ren['curp']}</label>
            </div>
            <div class='form-group'>
              <label for=''>Privilegios: <span style='color: rgb(93, 209, 255);'>{$ren['privilegios']}</span></label>
            </div>
            <div class='form-group'>
              <label for=''>Fecha de Registro: {$ren['createdAt']}</label>
            </div>
            <div class='d-flex justify-content-end'>
              <button type='button' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#modalActualizar{$ren['nickname']}'>
                Actualizar
              </button>
              <button type='button' class='btn btn-danger ms-2' data-bs-toggle='modal' data-bs-target='#modalEliminar{$ren['nickname']}'>
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

<div class='modal fade' id='modalActualizar{$ren['nickname']}' tabindex='-1' aria-labelledby='modalActualizarLabel' aria-hidden='true'>
  <div class='modal-dialog modal-lg'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='modalActualizarLabel'>Actualizar Administrador</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <form id='formActualizar' method='post'>
      <input type='hidden' name='actualizar' value='1'>
      <div class='modal-body'>
          <div class='row mb-3'>
            <div class='col-md-6'>
              <label class='form-label'>Nickname</label>
              <input type='text' class='form-control' name='nickname' value='{$ren['nickname']}' readonly>
              <small class='text-muted'>El nickname no se puede modificar</small>
            </div>
            <div class='col-md-6'>
              <label class='form-label'>CURP</label>
              <input type='text' class='form-control' name='curp' value='{$ren['curp']}' minlength='18' maxlength='18' required>
            </div>
          </div>
          
          <div class='row mb-3'>
            <div class='col-md-12'>
              <label class='form-label'>Correo Electronico</label>
              <input type='email' class='form-control' value='{$ren['correo']}' name='correo' required>
            </div>
          </div>

          <div class='row mb-3'>
            <div class='col-md-6'>
              <label class='form-label'>Contraseña</label>
              <input type='password' class='form-control' placeholder='Nueva contraseña' name='contra'>
              <small class='text-muted'>Dejar en blanco si no desea cambiar la contraseña</small>
            </div>
            
            <div class='col-md-6'>
              <label class='form-label'>Privilegios</label>
              <select class='form-select' required name='privilegios'>
                <option value='1'>root</option>
                <option value='2'>admin</option>
              </select>
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


<div class='modal fade' id='modalEliminar{$ren['nickname']}' tabindex='-1' aria-labelledby='modalEliminarLabel' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='modalEliminarLabel'>Confirmar Eliminacion</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <div class='modal-body'>
        <p>¿Esta seguro que desea eliminar al administrador <strong>{$ren['nickname']}</strong>?</p>
        <p class='text-danger'><strong>Esta accion no se puede deshacer.</strong></p>
      </div>
      <div class='modal-footer'>
        <form method='post' name='eliminacion'>
            <input type='hidden' name='nickname' value='{$ren['nickname']}'>
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
    <title>Administradores</title>
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
            <a class="nav-link white" href="pagos.html">Pagos</a>
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
                <input type="hidden" name="privilegios" value="1">
                <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="privilegios" onchange="javascript:this.form.submit();">
                    <option value="1" <?php if($privilegios == 1) echo "selected"?>>root</option>
                    <option value="2" <?php if($privilegios == 2) echo "selected"?>>administradores</option>
                </select>
            </form>
            </div>
        </div>
    </div>

    <br>

    <?php echo $tablas ?>

</body>
</html>
<?php
$cnn = mysqli_connect("localhost", "root", "eneto", "eneto");

if (!$cnn) {
    die("Conexion fallida: " . mysqli_connect_error());
}

if (isset($_POST['idMatricula']) && isset($_POST['anoVehiculo']) && isset($_POST['modelo']) && isset($_POST['plazas']) && isset($_POST['color']) && isset($_POST['disponible'])) {
    $idMatricula = $_POST['idMatricula'];
    $anoVehiculo = $_POST['anoVehiculo'];
    $modelo = $_POST['modelo'];
    $plazas = $_POST['plazas'];
    $color = $_POST['color'];
    $disponible = $_POST['disponible'];

    if (empty($idMatricula) || empty($anoVehiculo) || empty($modelo) || empty($plazas) || empty($color) || empty($disponible)) {
        echo "<div class='alert alert-warning mt-4'>Todos los campos son obligatorios</div>";
    } else {
        $checkMatricula = "SELECT * FROM vehiculos WHERE idMatricula = '$idMatricula'";
        $resultMatricula = mysqli_query($cnn, $checkMatricula);

        if (mysqli_num_rows($resultMatricula) > 0) {
            echo "<div class='alert alert-warning mt-4'>La matricula ya esta registrada.</div>";
        } else {
            $sql = "insert into vehiculos (idMatricula, anoVehiculo, modelo, plazas, color, disponible, createdAt, updatedAt) values ('$idMatricula', '$anoVehiculo', '$modelo', '$plazas', '$color', '$disponible', NOW(), NOW())";

            if (mysqli_query($cnn, $sql)) {
                echo "<div class='alert alert-success mt-4'>Vehiculo registrado con exito</div>";
            } else {
                echo "<div class='alert alert-danger mt-4'>Error: " . mysqli_error($cnn) . "</div>";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Vehiculo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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


  <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <h1 class="text-success text-center mt-3">Registrar Vehiculo</h1>
                <div class="card-body">
                    <form id="registroVehiculo" action="vehiculosAdmin.php" method="POST">
                        <div class="form-group">
                            <label for="idMatricula">Matricula</label>
                            <input type="text" class="form-control" id="idMatricula" name="idMatricula" placeholder="Ingresa la matricula del vehiculo" minlength="6" maxlength="8"    required>
                        </div>
                        <div class="form-group">
                            <label for="anoVehiculo">Año del vehiculo (AAAA)</label>
                            <input type="text" class="form-control" id="anoVehiculo" name="anoVehiculo" placeholder="Ingresa el año del vehiculo" onkeypress="permitirSoloNumeros(event)" minlength="4" maxlength="4" required>
                        </div>
                        <div class="form-group">
                            <label for="modelo">Modelo</label>
                            <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ingresa el modelo del vehiculo" required>
                        </div>
                        <div class="form-group">
                            <label for="plazas">Plazas</label>
                            <input type="number" class="form-control" id="plazas" name="plazas" placeholder="Ingresa el numero de plazas" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="text" class="form-control" id="color" name="color" placeholder="Ingresa el color del vehiculo" required>
                        </div>
                        <div class="form-group">
                            <label for="disponible">Disponible</label>
                            <select class="form-control" id="disponible" name="disponible" required>
                                <option value="" disabled selected>Selecciona si el vehiculo esta disponible</option>
                                <option value="1">Disponible</option>
                                <option value="0">No disponible</option>
                            </select>
                        </div>
                        <br>
                        <button class="btn color white w-100" type="submit">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </div>
</body>
<script src="scripts/utileria.js"></script>
</html>

<?php
mysqli_close($cnn);
?>

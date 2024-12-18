<?php
$cnn = new mysqli("localhost", "root", "eneto", "eneto");

if ($cnn->connect_error) {
    die("Error de conexion: " . $cnn->connect_error);
}

if (isset($_POST['modificar'])) {
    $idMatricula = $_POST['idMatricula'];
    $anoVehiculo = $_POST['anoVehiculo'];
    $modelo = $_POST['modelo'];
    $plazas = $_POST['plazas'];
    $color = $_POST['color'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    $query = "UPDATE vehiculos SET anoVehiculo = ?, modelo = ?, plazas = ?, color = ?, disponible = ? WHERE idMatricula = ?";
    
    $stmt = $cnn->prepare($query);
    $stmt->bind_param("ssisss", $anoVehiculo, $modelo, $plazas, $color, $disponible, $idMatricula);

    if ($stmt->execute()) {
        $message = "Datos del vehiculo modificados correctamente.";
    } else {
        $message = "Error al modificar el vehiculo: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_POST['eliminar'])) {
    $idMatricula = $_POST['idMatricula'];

    try {
        $query = "DELETE FROM vehiculos WHERE idMatricula = ?";
        $stmt = $cnn->prepare($query);
        $stmt->bind_param("s", $idMatricula);

        if ($stmt->execute()) {
            $message = "Vehículo eliminado correctamente.";
        } else {
            throw new Exception("Error al eliminar el vehículo: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        $message = "No se puede eliminar el vehículo ya que esta     relacionado con quejas existentes.";
    }
}


$consul = $cnn->query("SELECT * FROM vehiculos");
$tablas = "";
while ($ren = $consul->fetch_array(MYSQLI_ASSOC)) {
    $disponibilidad = $ren['disponible'] ? 'Disponible' : 'No Disponible';
    $disponibleChecked = $ren['disponible'] ? 'checked' : '';
    $tablas .= "<br>
    <div class='container-fluid'>
        <div class='container mt-9'>
            <div class='column justify-content-center'>
                <div class='col-lg-12'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='form-group'>
                                <label for=''>Matricula: {$ren['idMatricula']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>Año: {$ren['anoVehiculo']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>Modelo: {$ren['modelo']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>Plazas: {$ren['plazas']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>Color: {$ren['color']}</label>
                            </div>
                            <div class='form-group'>
                                <label for=''>Disponibilidad: {$disponibilidad}</label>
                            </div>
                            <div class='d-flex justify-content-end'>
                                <button type='button' class='btn btn-warning me-2' data-bs-toggle='modal' data-bs-target='#modalModificar{$ren['idMatricula']}'>
                                    Actualizar
                                </button>
                                <button type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#modalEliminar{$ren['idMatricula']}'>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='modal fade' id='modalModificar{$ren['idMatricula']}' tabindex='-1' aria-labelledby='modalModificarLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='modalModificarLabel'>Modificar Vehiculo</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form id='formModificar{$ren['idMatricula']}' action='EditarEliminarVehiculo.php' method='POST'>
                        <input type='hidden' name='idMatricula' value='{$ren['idMatricula']}'>
                        <div class='form-group mb-3'>
                            <label for='anoVehiculo{$ren['idMatricula']}' class='form-label'>Año</label>
                            <input type='text' class='form-control ancho-input-2' id='anoVehiculo{$ren['idMatricula']}' name='anoVehiculo' value='{$ren['anoVehiculo']}' onkeypress='permitirSoloNumeros(event)' minlength=' 4' maxlength='4' required>
                        </div>
                        <div class='form-group mb-3'>
                            <label for='modelo{$ren['idMatricula']}' class='form-label'>Modelo</label>
                            <input type='text' class='form-control ancho-input-2' id='modelo{$ren['idMatricula']}' name='modelo' value='{$ren['modelo']}' required>
                        </div>
                        <div class='form-group mb-3'>
                            <label for='plazas{$ren['idMatricula']}' class='form-label'>Plazas</label>
                            <input type='number' class='form-control ancho-input-2' id='plazas{$ren['idMatricula']}' name='plazas' value='{$ren['plazas']}' min='0' required>
                        </div>
                        <div class='form-group mb-3'>
                            <label for='color{$ren['idMatricula']}' class='form-label'>Color</label>
                            <input type='text' class='form-control ancho-input-2' id='color{$ren['idMatricula']}' name='color' value='{$ren['color']}' required>
                        </div>
                        <div class='form-group mb-3 form-check'>
                            <input type='checkbox' class='form-check-input' id='disponible{$ren['idMatricula']}' name='disponible' $disponibleChecked>
                            <label class='form-check-label' for='disponible{$ren['idMatricula']}'>Disponible</label>
                        </div>
                    </form>
                </div>
                <div class='modal-footer d-flex justify-content-between w-100'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                    <button type='submit' class='btn color white' form='formModificar{$ren['idMatricula']}' name='modificar'>Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class='modal fade' id='modalEliminar{$ren['idMatricula']}' tabindex='-1' aria-labelledby='modalEliminarLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='modalEliminarLabel'>¿Estas seguro de eliminar este vehiculo?</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <form method='POST' action='EditarEliminarVehiculo.php'>
                        <input type='hidden' name='idMatricula' value='{$ren['idMatricula']}'>
                        <p>Esta acción no se puede deshacer.</p>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                        <button type='submit' class='btn btn-danger' name='eliminar'>Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    ";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehiculos</title>
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
                    <li class="nav-item">
                        <a class="nav-link white" aria-current="page" href="#">Solicitar viaje</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link white" href="#">Historial de viajes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link white" href="#">Vehiculos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link white" href="#">Mi perfil</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="container mt-5">
        <h2>Vehiculos Registrados</h2>
        <?= $tablas ?>
    </div>

</body>
<script src="scripts/utileria.js"></script>

</html>
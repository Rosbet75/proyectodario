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
 $tablas .= "

";

};


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas</title>
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
    
    <br>
    <div class="container-fluid">
        <div class="container mt-9">
            <div class="column justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="">ID Cita: 1</label>
                            </div>
                            <div class="form-group">
                                <label for="">ID Chofer: 101</label>
                            </div>
                            <div class="form-group">
                                <label for="">Fecha: 2024-12-14 10:30:00</label>
                            </div>
                            <div class="form-group">
                                <label for="">Concepto: 3</label>
                            </div>
                            <div class="form-group">
                                <label for="">Comentarios: Cliente no se presento</label>
                            </div>
                            <div class="form-group">
                                <label for="">Sancion: 2</label>
                            </div>
                            <div class="d-flex justify-content-end">

                                <button class="btn btn-primary btn-success ms-2">Marcar como atendido</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$cnn = mysqli_connect("localhost", "root", "eneto", "eneto");

if (!$cnn) {
    die("Conexion fallida: " . mysqli_connect_error());
}

if (isset($_POST['nickname']) && isset($_POST['correo']) && isset($_POST['contra']) && isset($_POST['curp']) && isset($_POST['privilegios'])) {
    $nickname = $_POST['nickname'];
    $correo = $_POST['correo'];
    $contra = $_POST['contra'];
    $curp = $_POST['curp'];
    $privilegios = $_POST['privilegios'];

    
    if (empty($nickname) || empty($correo) || empty($contra) || empty($curp) || empty($privilegios)) {
      echo "<div class='alert alert-warning mt-4'>Todos los campos son obligatorios</div>";
  } else {
      $checkNickname = "SELECT * FROM admins WHERE nickname = '$nickname'";
      $resultNickname = mysqli_query($cnn, $checkNickname);
  
      if (mysqli_num_rows($resultNickname) > 0) {
          echo "<div class='alert alert-warning mt-4'>El nombre de usuario ya esta registrado.</div>";
      } else {
          $checkCorreo = "SELECT * FROM admins WHERE correo = '$correo'";
          $resultCorreo = mysqli_query($cnn, $checkCorreo);
  
          if (mysqli_num_rows($resultCorreo) > 0) {
              echo "<div class='alert alert-warning mt-4'>El correo electronico ya esta registrado.</div>";
          } else {
              $checkCurpAdmins = "SELECT * FROM admins WHERE curp = '$curp'";
              $resultCurpAdmins = mysqli_query($cnn, $checkCurpAdmins);
  
              if (mysqli_num_rows($resultCurpAdmins) > 0) {
                  echo "<div class='alert alert-warning mt-4'>La CURP ya esta registrada como administrador.</div>";
              } else {
                  $sql = "INSERT INTO admins (nickname, correo, contra, curp, privilegios, createdAt, updatedAt)
                          VALUES ('$nickname', '$correo', '$contra', '$curp', '$privilegios', NOW(), NOW())";
  
                  if (mysqli_query($cnn, $sql)) {
                      echo "<div class='alert alert-success mt-4'>Administrador registrado con exito</div>";
                  } else {
                      echo "<div class='alert alert-danger mt-4'>Error: " . mysqli_error($cnn) . "</div>";
                  }
              }
          }
      }
  }
}

$query = "SELECT curp FROM empleados";
$result = mysqli_query($cnn, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($cnn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Administrador</title>
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
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <h1 class="text-success text-center mt-3">Registrar Administrador</h1>
                <div class="card-body">
                    <form id="registroAdmin" action="administradorAdmin.php" method="POST">
                        <div class="form-group">
                            <label for="nickname">Nombre de usuario</label>
                            <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Ingresa tu nombre de usuario" required>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingresa el correo electrónico" required>
                        </div>
                        <div class="form-group">
                            <label for="contra">Contraseña</label>
                            <input type="password" class="form-control" id="contra" name="contra" placeholder="Ingresa la contraseña" required>
                        </div>
                        <div class="form-group">
                            <label for="curp">CURP</label>
                            <select class="form-control" id="curp" name="curp" required>
                                <option value="" disabled selected>Selecciona una CURP</option>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='" . $row["curp"] . "'>" . $row["curp"] . "</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>No se encontraron CURPs</option>";
                                }
                                ?>
                            </select>   
                        </div>
                        <div class="form-group">
                          <label for="privilegios">Privilegios</label>
                          <select class="form-control" id="privilegios" name="privilegios" required>
                              <option value="" disabled selected>Selecciona los privilegios</option>
                              <?php
                              $query = "SELECT idPriv, rol FROM privilegios";
                              $result = mysqli_query($cnn, $query);
                              if (mysqli_num_rows($result) > 0) {
                                  while ($row = mysqli_fetch_assoc($result)) {
                                      echo "<option value='" . $row["idPriv"] . "'>" . $row["rol"] . "</option>";
                                  }
                              } else {
                                  echo "<option value='' disabled>No se encontraron privilegios</option>";
                                  }
                                  ?>
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
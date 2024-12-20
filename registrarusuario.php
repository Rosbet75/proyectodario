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
  
  
  if (isset($_COOKIE['logeo'])) {
    $cred = explode(":", $_COOKIE["logeo"]);
    
    $resultado = verificarCredenciales($cred[0], $cred[1]);
    
    if ($resultado) {
        echo htmlspecialchars($cred[0]); //aqui esta el nickname alfin
       
    }
    $resultado = verificarCredenciales($cred[0], $cred[1]);
    
    if($resultado > 0){
        header("Location: barra.php");
        exit;
    }
    $resultado = verificarCredencialesAdmin($cred[0], $cred[1]);
    if($resultado > 0){
        header("Location: BarraAdmin.php");
        exit;
    }
  } 
//--------------------------------------
$cnn = mysqli_connect("localhost", "root", "eneto", "eneto");

if (!$cnn) {
    die("Conexion fallida: " . mysqli_connect_error());
}

if (isset($_POST['nickname']) && isset($_POST['correo']) && isset($_POST['contra']) && isset($_POST['confirmContra']) && isset($_POST['nombre']) && isset($_POST['apellidoPaterno']) && isset($_POST['apellidomaterno']) && isset($_POST['sexo'])) {
    $nickname = $_POST['nickname'];
    $correo = $_POST['correo'];
    $contra = $_POST['contra'];
    $confirmContra = $_POST['confirmContra'];
    $nombre = $_POST['nombre'];
    $apellidoPaterno = $_POST['apellidoPaterno'];
    $apellidomaterno = $_POST['apellidomaterno'];
    $sexo = $_POST['sexo'];

    if (empty($nickname) || empty($correo) || empty($contra) || empty($confirmContra) || empty($nombre) || empty($apellidoPaterno) || empty($apellidomaterno) || empty($sexo)) {
        echo "<div class='alert alert-warning mt-4'>Todos los campos son obligatorios</div>";
    } else if ($contra != $confirmContra) {
        echo "<div class='alert alert-warning mt-4'>Las contrasenas no coinciden.</div>";
    } else {
        $checkNickname = "SELECT * FROM usuarios WHERE nickname = '$nickname'";
        $resultNickname = mysqli_query($cnn, $checkNickname);

        if (mysqli_num_rows($resultNickname) > 0) {
            echo "<div class='alert alert-warning mt-4'>El nombre de usuario ya esta registrado.</div>";
        } else {
            $checkCorreo = "SELECT * FROM usuarios WHERE correo = '$correo'";
            $resultCorreo = mysqli_query($cnn, $checkCorreo);

            if (mysqli_num_rows($resultCorreo) > 0) {
                echo "<div class='alert alert-warning mt-4'>El correo electronico ya esta registrado.</div>";
            } else {
                $sql = "INSERT INTO usuarios (nickname, correo, contra, nombre, apellidoPaterno, apellidomaterno, sexo, createdAt, updatedAt)
                        VALUES ('$nickname', '$correo', '$contra', '$nombre', '$apellidoPaterno', '$apellidomaterno', '$sexo', NOW(), NOW())";

                if (mysqli_query($cnn, $sql)) {
                    // Solo redirigir si la inserción fue exitosa
                    header('Location: barra.php');
                    exit;
                } else {
                    echo "<div class='alert alert-danger mt-4'>Error: " . mysqli_error($cnn) . "</div>";
                }
            }
        }
    }
}

mysqli_close($cnn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <h1 class="text-success text-center">Registrar Usuario</h1>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="nickname">Nombre de usuario</label>
                                <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Ingresa tu nombre de usuario" required>
                            </div>
                            <div class="form-group">
                                <label for="apellidoPaterno">Apellido Paterno</label>
                                <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" placeholder="Ingresa tu apellido paterno" onkeypress="permitirSoloLetras(event)" required>
                            </div>
                            <div class="form-group">
                                <label for="apellidomaterno">Apellido Materno</label>
                                <input type="text" class="form-control" id="apellidomaterno" name="apellidomaterno" placeholder="Ingresa tu apellido materno" onkeypress="permitirSoloLetras(event)" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre">Nombre(s)</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa tu nombre" onkeypress="permitirSoloLetras(event)" required>
                            </div>
                            <div class="form-group">
                                <label for="correo">Correo Electronico</label>
                                <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingresa tu correo" required>
                            </div>
                            <div class="form-group">
                                <label for="contra">Contrasena</label>
                                <input type="password" class="form-control" id="contra" name="contra" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmContra">Confirmar Contrasena</label>
                                <input type="password" class="form-control" id="confirmContra" name="confirmContra" required>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-10">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sexo" id="gridRadios1" value="M" checked>
                                        <label class="form-check-label" for="gridRadios1">
                                        Masculino
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sexo" id="gridRadios2" value="F">
                                        <label class="form-check-label" for="gridRadios2">
                                        Femenino
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <button class="btn btn-primary" type="submit" href="barra.php">Registrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

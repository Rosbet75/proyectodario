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
            header("Location: BarraAdmin.php");
            exit;
        }
    } else if(isset($_POST['login'])){
        
        $resultado = verificarCredenciales($_POST['username'], $_POST['contra']);
        if($resultado > 0){
            setcookie("logeo", $_POST['username'].":".$_POST['contra'], time() + 3600, "/", "localhost");
            header("Location: barra.php");
            exit;
        }
        $resultado = verificarCredencialesAdmin($_POST['username'], $_POST['contra']);
        if($resultado > 0){
            setcookie("logeo", $_POST['username'].":".$_POST['contra'], time() + 3600, "/", "localhost");
            header("Location: BarraAdmin.php");
            exit;
        }
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/barra.css">
</head>
<body>
    <h1 class="text-succes text-center bg-color">Eneto.Inc</h1>
    <br>
    <br>
    <br>
    <h6 class="text-succes text-center">No solucionamos tus problemas pero te llevamos mas rapido a ellos</h6>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <img src="img/dario.png" alt="" class="card-img-top">
                    <div class="card-body">
                        <form id="login" action="" method="post">
                            <input type="hidden" name="login" value="1">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" placeholder="Nombre de usuario" name="username">
                            </div>
                            <div class="form-group">
                                <label for="contra">Contraseña</label>
                                <input type="password" class="form-control" id="contra" placeholder="Contraseña" name="contra">
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Log in</button>
                        </form>
                        <label for="">No tienes Contraseña? <a href="registrarusuario.html">Registrarse</a></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
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
            <a class="nav-link white" aria-current="page" href="solicitarViajeUsuario.php">Solicitar viaje</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="historialViajesUsuario.php">Historial de viajes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="metodosPagoUsuario.php">Metodos de pago</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="miperfil.php">Mi perfil</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
      </nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-15">
            <div class="card">
                <br>
                <h1 class="text-success text-center">Mi Perfil</h1>

                
                <div class="card-body">
                    <form id="perfil">
                        <div class="form-group">
                            <label for="nickname">Nickname</label>
                            <input type="text" class="form-control" id="nickname">
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" onkeypress="permitirSoloLetras(event)">
                        </div>
                        <div class="form-group">
                            <label for="apellidoPaterno">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellidoPaterno" onkeypress="permitirSoloLetras(event)">
                        </div>
                        <div class="form-group">
                            <label for="apellidoMaterno">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellidoMaterno" onkeypress="permitirSoloLetras(event)">
                        </div>
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sexo" id="masculino" value="M" checked>
                                <label class="form-check-label" for="masculino">Masculino</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sexo" id="femenino" value="F">
                                <label class="form-check-label" for="femenino">Femenino</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo Electronico</label>
                            <input type="email" class="form-control" id="correo">
                        </div>
                        <div class="form-group">
                            <label for="fechaNacimiento">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fechaNacimiento">
                        </div>
                        <div class="form-group">
                            <label for="contrasenaActual">Contraseña Actual</label>
                            <input type="password" class="form-control" id="contrasenaActual">
                        </div>
                        <h2 class="mt-4">Cambiar Contraseña</h2>
                        <div class="form-group">
                            <label for="nuevaContrasena">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="nuevaContrasena" oninput="verificarContrasenas()">
                        </div>
                        <div class="form-group">
                            <label for="confirmarContrasena">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirmarContrasena" oninput="verificarContrasenas()">
                            <div id="mensajeContrasena" style="font-size: 0.875em; margin-top: 5px;"></div>
                        </div>

                        <br>
                        <button class="btn color white" type="submit" id="actualizarDatos">Actualizar Datos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

<script src="scripts/utileria.js"></script>

</html>
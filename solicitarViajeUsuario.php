<?php
$cnn = mysqli_connect("localhost", "root", "eneto", "eneto");

if (!$cnn) {
    die("Conexion fallida: " . mysqli_connect_error());
}

$sql = "SELECT numTar FROM tarjetas";
$result = mysqli_query($cnn, $sql);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Viaje</title>
    
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
            <a class="nav-link white" aria-current="page" href="solicitarViajeUsuario.html">Solicitar viaje</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="historialViajesUsuario.html">Historial de viajes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="metodosPagoUsuario.html">Metodos de pago</a>
          </li>
          <li class="nav-item">
            <a class="nav-link white" href="miperfil.html">Mi perfil</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="contenedor">
    <h2>Solicitar Viaje</h2>
    <form>
        <div>
            <br>
            <div>
                <label for="fecha" class="form-label">Fecha del Viaje</label>
                <input type="date" class="form-control ancho-input" id="fecha" required>
            </div>
        </div>
        
        <div>
            <br>
            <label for="tarjeta" class="form-label">Tarjeta seleccionada</label>
            <select id="tarjeta" class="form-control" name="tarjeta" required>
                <option value="" disabled selected>Selecciona una tarjeta</option>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['numTar'] . "'>" . $row['numTar'] . "</option>";
                    }
                } else {
                    echo "<option value='' disabled>No hay tarjetas disponibles</option>";
                }
                ?>
            </select>
            <a href="metodosPagoUsuario.php" class="btn btn-link">Agregar nuevo metodo de pago</a>
        </div>
        
        <div>
            <br>
            <label for="destino" class="form-label">Destino</label>
            <input type="text" class="form-control ancho-input" id="destino" placeholder="Ingrese la direccion de destino" required>
        </div>
        <div>
            <br>
            <label for="cuota" class="form-label">Cuota</label>
            <input type="text" class="form-control ancho-input" id="cuota" placeholder="80">
        </div>
        <br>
        <button type="submit" class="btn color white">Solicitar Viaje</button>
    </form>
    
    <br>
</div>

<?php
mysqli_close($cnn);
?>
</body>
</html>

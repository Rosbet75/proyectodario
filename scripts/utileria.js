function permitirSoloLetras(event) {
    const tecla = event.key;
    // Expresión regular: Permite solo letras (mayúsculas, minúsculas y espacios)
    const esLetra = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/;
    if (!esLetra.test(tecla)) {
        event.preventDefault(); // Evita que el carácter no permitido se escriba
    }
}
function permitirSoloNumeros(event) {
    const tecla = event.key;
    // Expresión regular: Permite solo letras (mayúsculas, minúsculas y espacios)
    const esLetra = /^[0-9\s]$/;
    if (!esLetra.test(tecla)) {
        event.preventDefault(); // Evita que el carácter no permitido se escriba
    }
}
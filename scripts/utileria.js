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

function verificarContrasenas() {
    const nuevaContrasena = document.getElementById('nuevaContrasena');
    const confirmarContrasena = document.getElementById('confirmarContrasena');
    const mensajeError = document.getElementById('mensajeContrasena');
    
    if (nuevaContrasena.value === '' && confirmarContrasena.value === '') {
        nuevaContrasena.style.borderColor = '';
        confirmarContrasena.style.borderColor = '';
        mensajeError.textContent = '';
    } else if (nuevaContrasena.value === confirmarContrasena.value) {
        nuevaContrasena.style.borderColor = '#28a745';
        confirmarContrasena.style.borderColor = '#28a745';
        mensajeError.textContent = 'Las contraseñas coinciden';
        mensajeError.style.color = '#28a745';
    } else {
        nuevaContrasena.style.borderColor = '#dc3545';
        confirmarContrasena.style.borderColor = '#dc3545';
        mensajeError.textContent = 'Las contraseñas no coinciden';
        mensajeError.style.color = '#dc3545';
    }
}

function sobrescribirValor(event) {
    // Obtener el campo de input
    var input = event.target;
    var valor = input.value;  // Valor que el usuario está escribiendo

    // Aquí podemos realizar una modificación en el valor
    // Forzamos el valor a tener un texto específico o hacer alguna transformación
    input.value = valor.toUpperCase();  // Ejemplo: convertir todo a mayúsculas

    console.log('Valor sobrescrito:', input.value); 
}

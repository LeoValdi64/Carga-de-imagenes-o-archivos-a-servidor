document.getElementById('formularioImagen').addEventListener('submit', function(event) {
    event.preventDefault();

    const archivos = document.getElementById('imagen').files;
    const totalArchivos = archivos.length;
    let archivosCargados = 0;

    const promesasDeCarga = Array.from(archivos).map(archivo => 
        cargarArchivoEnPartes(archivo).then(() => {
            archivosCargados++;
            actualizarProgreso(archivosCargados, totalArchivos);
        })
    );

    Promise.all(promesasDeCarga)
        .then(() => window.alert("Todos los archivos han sido cargados."))
        .catch(error => console.error("Error en la carga: ", error));
});

function actualizarProgreso(archivosCargados, totalArchivos) {
    const porcentaje = (archivosCargados / totalArchivos) * 100;
    document.getElementById('barraProgreso').value = porcentaje;
}

function cargarArchivoEnPartes(archivo) {
    return new Promise((resolve, reject) => {
        const TAMANO_PARTES = 1 * 1024 * 1024; // 1MB por parte
        const totalPartes = Math.ceil(archivo.size / TAMANO_PARTES);
        let parteActual = 0;

        function subirParte() {
            const inicio = parteActual * TAMANO_PARTES;
            const fin = Math.min(inicio + TAMANO_PARTES, archivo.size);
            const parte = archivo.slice(inicio, fin);

            const formData = new FormData();
            formData.append("parte", parte);
            formData.append("nombreArchivo", archivo.name);
            formData.append("parteActual", parteActual);
            formData.append("totalPartes", totalPartes);

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "carga.php", true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    parteActual++;
                    if (parteActual < totalPartes) {
                        subirParte();
                    } else {
                        console.log("Carga completa de: " + archivo.name);
                        resolve(); // Resuelve la promesa aquí
                    }
                } else {
                    console.error("Error en la carga de la parte para: " + archivo.name);
                    reject("Error en la carga de la parte para: " + archivo.name); // Rechaza la promesa aquí
                }
            };

            xhr.send(formData);
        }

        subirParte();
    });
}

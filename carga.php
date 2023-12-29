<?php
$directorioTemporal = "temp_img/";
$directorioDestino = "img/";
$directorioZip = "zip/";
$nombreZip = "archivos_cargados.zip"; // Nombre del archivo .zip

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreArchivo = $_POST['nombreArchivo'];
    $parteActual = $_POST['parteActual'];
    $totalPartes = $_POST['totalPartes'];
    $archivoTemporal = $directorioTemporal . $nombreArchivo . '.part' . $parteActual;

    move_uploaded_file($_FILES['parte']['tmp_name'], $archivoTemporal);

    if ($parteActual == $totalPartes - 1) {
        $archivoCompleto = $directorioDestino . $nombreArchivo;
        $archivoDestino = fopen($archivoCompleto, 'wb');

        for ($i = 0; $i < $totalPartes; $i++) {
            $parte = file_get_contents($directorioTemporal . $nombreArchivo . '.part' . $i);
            fwrite($archivoDestino, $parte);
            unlink($directorioTemporal . $nombreArchivo . '.part' . $i); // Elimina la parte
        }

        fclose($archivoDestino);

        // Agregar el archivo completo al zip
        $zip = new ZipArchive();
        if ($zip->open($directorioZip . $nombreZip, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($archivoCompleto, $nombreArchivo);
            $zip->close();

            // Opcionalmente, puedes eliminar el archivo del directorio de destino después de agregarlo al zip
            // unlink($archivoCompleto);
        }

        echo "console.log('Archivo completo subido: " . $nombreArchivo ." ')";
    }
}
?>


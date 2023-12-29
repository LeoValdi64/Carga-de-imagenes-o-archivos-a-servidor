<?php
$directorioTemporal = "temp_img/";
if (!file_exists($directorioTemporal)) {
    mkdir($directorioTemporal, 0777, true);
}
$directorioDestino = "img/";
if (!file_exists($directorioDestino)) {
    mkdir($directorioDestino, 0777, true);
}
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
    }
}
?>

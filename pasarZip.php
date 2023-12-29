<?php
//Función para reducir la resolución de las imágenes
function reducirResolucionImagenes($directorioOrigen, $directorioDestino, $nuevaAnchura, $nuevaAltura, $maxImg)
{
    // Verificar si el directorio origen existe
    if (!file_exists($directorioOrigen)) {
        return "El directorio de origen no existe.";
    }

    // Crear el directorio destino si no existe
    if (!file_exists($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    // Abrir el directorio
    $dir = opendir($directorioOrigen);

    // Procesar cada archivo en el directorio
    while (($archivo = readdir($dir)) !== false) {


        $rutaArchivo = $directorioOrigen . '/' . $archivo;
        $tipoMime = mime_content_type($rutaArchivo);

        // Verificar si es un archivo y es una imagen
        if (is_file($rutaArchivo) && in_array($tipoMime, ['image/jpeg', 'image/png', 'image/gif'])) {
            // Crear una nueva imagen con la nueva resolución
            list($anchura, $altura) = getimagesize($rutaArchivo);
            $ratio = $anchura / $altura;
            $new_altura = $nuevaAltura;
            $new_anchura = $nuevaAnchura;

            if ($new_anchura / $new_altura > $ratio) {
                $new_anchura = $new_altura * $ratio;
            } else {
                $new_altura = $new_anchura / $ratio;
            }

            $imagen = imagecreatetruecolor($new_anchura, $new_altura);

            switch ($tipoMime) {
                case 'image/jpeg':
                    $imagenOrigen = imagecreatefromjpeg($rutaArchivo);
                    break;
                case 'image/png':
                    $imagenOrigen = imagecreatefrompng($rutaArchivo);
                    break;
                case 'image/gif':
                    $imagenOrigen = imagecreatefromgif($rutaArchivo);
                    break;
            }

            imagecopyresampled($imagen, $imagenOrigen, 0, 0, 0, 0, $new_anchura, $new_altura, $anchura, $altura);

            // Guardar la imagen en el directorio destino
            $rutaDestino = $directorioDestino . '/' . $archivo;
            switch ($tipoMime) {
                case 'image/jpeg':
                    imagejpeg($imagen, $rutaDestino);
                    break;
                case 'image/png':
                    imagepng($imagen, $rutaDestino);
                    break;
                case 'image/gif':
                    imagegif($imagen, $rutaDestino);
                    break;
            }
            //Solo se puede subir un máximo de 5 imágenes
            $maxImg--;
            if ($maxImg <= 0) {
                break;
            }

            // Liberar memoria
            imagedestroy($imagen);
            imagedestroy($imagenOrigen);
        }
    }

    closedir($dir);

    return "Proceso completado.";
}

// Uso de la función
$carperaOrigen = 'C:\xampp\htdocs\Carga de imagenes con Ajax\img';
$carpetaDestino = 'C:\xampp\htdocs\Carga de imagenes con Ajax\low_img';
$ancho = 800;
$alto = 600;
$maxImg = 5;
echo reducirResolucionImagenes($carperaOrigen, $carpetaDestino, $ancho, $alto, $maxImg);

// Función para agregar un directorio al archivo ZIP
function agregarDirectorioAlZip($zip, $dir, $baseDir = '')
{
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Omitir directorios (los añadiremos al final)
        if (!$file->isDir()) {
            // Obtener la ruta relativa desde la carpeta base
            $filePath = $file->getRealPath();
            $relativePath = $baseDir . substr($filePath, strlen($dir) + 1);

            // Añadir al ZIP
            $zip->addFile($filePath, $relativePath);
        }
    }
}

// Ruta de la carpeta a comprimir
$carpeta = 'C:\xampp\htdocs\Carga de imagenes con Ajax\img';

// Ruta del directorio donde se guardará el archivo ZIP
$directorioZip = 'zip/';

// Asegurarse de que el directorio existe y crearlo si no existe
if (!file_exists($directorioZip)) {
    mkdir($directorioZip, 0755, true);
}

// Ruta completa del archivo ZIP de salida
$archivoZip = $directorioZip . 'archivo.zip';

// Crear un objeto ZipArchive
$zip = new ZipArchive();

// Abrir el archivo ZIP para escritura
if ($zip->open($archivoZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    // Agregar el contenido de la carpeta al ZIP
    agregarDirectorioAlZip($zip, $carpeta);

    // Cerrar el archivo ZIP
    if ($zip->close()) {
        echo "El archivo ZIP se ha creado con éxito.";

        // Ahora elimina los archivos después de cerrar el ZIP
        eliminarArchivos($carpeta);
    } else {
        echo "Hubo un problema al cerrar el archivo ZIP.";
    }
} else {
    echo "No se pudo abrir el archivo ZIP para escritura.";
}

// Función para eliminar archivos de una carpeta
function eliminarArchivos($dir)
{
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if (!$file->isDir()) {
            unlink($file->getRealPath());
        }
    }
}

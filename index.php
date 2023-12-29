<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga de imagenes con Ajax</title>
</head>
<body>
    <form id="formularioImagen" enctype="multipart/form-data">
        <input type="file" name="imagen[]" id="imagen" multiple>
        <button type="submit">Cargar Imagen</button>
    </form>

    <div>
        <progress id="barraProgreso" value="0" max="100"></progress>
    </div>

    <script src="script.js"></script>
</body>
</html>

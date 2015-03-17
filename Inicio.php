<!DOCTYPE html>
<html>
    
        <link rel="stylesheet" type= "text/css" href="MisEstilos2.css" />
	   <head>
		  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		  <title>Actividad: Lab 10: Sesiones en PHP </title>
	   </head>
<body>

    <p>Para subir una imágen solo da click en "Seleccionar Imágen" para buscar tu archivo</p>
    <p>hay algunas recomendaciones para que no tengas problema alguno: </p>
    <p>+ La imágen no puede pesar menos de 500 Kb. </p>
    <p>+ Las imágenes deben de ser en formato JPG, GIF, JPEG o PNG</p><br><br>
    
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <p>Selecciona la imagen que deseas subir:</p>
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Upload Image" name="submit" class="button">
    </form><br><br>
    
         <form method="post" action="ver.php">
                <p>Ver Imágenes subidas</p>
                <input class="button" type="submit" name="calcular" value="Ver" id="calcular"> 
         </form><br>
    
        <form method="post" action="Registro.php">
                <p>Ver datos de registro</p>
                <input class="button" type="submit" name="calcular" value="VerR" id="calcular"> 
         </form><br>
    
        <a href="PagInicial.php">Regresar a pagina Principal</a>
</body>
</html>
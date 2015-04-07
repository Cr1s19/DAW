<!DOCTYPE html>
<html>

        <link rel="stylesheet" type= "text/css" href="MisEstilos2.css" />
	   <head>
		  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		  <title>Subiendo imágenes</title>
	   </head>
<body>

        <?php
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Lo sentimos, YA existe un archivo con el mismo nombre :( ";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Upss tu archivo es muy grande!!! :O .";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Lo sentimos solo tienes permitido subir estos formatos: JPG, JPEG, PNG & GIF files. :/ ";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, NO se pudo subir tu archivo....intenta de nuevo más tarde";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                include 'AES.php';
                AES.function_construnc();
                echo "El archivo ". basename( $_FILES["fileToUpload"]["name"]). " has sido subido exitosamente! bien :D";
            } else {
                echo "Lo sentimos, hubo un error subiendo tu archivo :( .";
            }
        }
        ?>
    </body>
</html>
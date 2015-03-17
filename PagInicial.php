<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html>
    
        <link rel="stylesheet" type= "text/css" href="MisEstilos2.css" />
	   <head>
		  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		  <title>Proyecto 2 DAW </title>
	   </head>
<body>

                    <?php
                    // define variables and set to empty values
                    $nameErr = $emailErr =  "";
                    $name = $email =  "";

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                       if (empty($_POST["name"])) {
                        $_SESSION["nombre"] = "name";
                         $nameErr = "Es necesario poner un nombre";
                       } else {
                            $name = test_input($_POST["name"]);
                                if (empty($_POST["email"])) {
                                $_SESSION["corre"] = "email";
                                $emailErr = "Campo requerido";
                                }    else {
                                    $email = test_input($_POST["email"]); 
                                    header('Location: Inicio.php');    
                                    }
                        }
    
        
                    }

                    function test_input($data) {
                       $data = trim($data);
                       $data = stripslashes($data);
                       $data = htmlspecialchars($data);
                       return $data;
                    }
                    ?>

        <h2 class="subtitulo">Inicio y Registro</h2><br>
        
        <div class="trasfondo">
            <br><br>
            <p><span class="error">* Campo Requerido.</span></p>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
               Nombre: <input type="text" name="name">
               <span class="error">* <?php echo $nameErr;?></span>
               <br><br>
               Mail: <input type="text" name="email">
               <span class="error">* <?php echo $emailErr;?></span>
               <br><br>
               <input type="submit" name="submit" value="Entrar" class="button"> 
            </form><br><br>
        </div>
    
    

    
            <div>
            <h3 class="subtitulo cabecera2">Datos del alumno</h3>
            <p>José Luis Leal Urquiza</p>
            <p>A01203587</p>
            <p>mail: A01203587@itesm.mx</p>
            <p>editor: Brackets</p>
            </div>    
    
    

</body>
</html>
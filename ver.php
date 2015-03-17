<!DOCTYPE html>
<html>
    
        <link rel="stylesheet" type= "text/css" href="MisEstilos2.css" />
	   <head>
		  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		  <title>Actividad: Lab 10: Sesiones en PHP </title>
	   </head>
<body>


        <div style="width:100%;"> 
            <?php 
            $dir='uploads/';  //nombre de la carpeta
            $images = glob("$dir{*.gif,*.jpg,*.png}", GLOB_BRACE);  
            foreach($images as $v){  
            echo '<img src="'.$v.'" border="0" style="width:100px;float:left;margin:10px;" />';  
            }  
            ?>  
        </div>
    
</body>
</html>
<?php 
    $mysql=mysqli_connect( "localhost", "root", "Tisisve123", "DAW");
    if (!$mysql) {
    die("Connection failed: ".mysqli_connect_error());
    }
#echo "Connect Successful"?>

<?php
    $query = 'INSERT INTO ANIMES(Nombre, Descripcion) VALUES(?,?)';
        if (mysqli_query($mysql, $query)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
?>

<?php mysqli_close($mysql);?>

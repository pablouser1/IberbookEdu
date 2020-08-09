<?php
if(file_exists("setup.php")){
    header("Location: setup.php");
}
else{
    header("Location: login.php");
}
?>
<?php
// Input
function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Delete folder
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

        foreach($files as $file){
            delete_files($file);
        }

        rmdir($target);
    } elseif(is_file($target)) {
        unlink($target);  
    }
}
?>

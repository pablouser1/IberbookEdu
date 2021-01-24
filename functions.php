<?php
// Common functions
class Utils {
    static public function sendJSON($response) {
        header('Content-type: application/json');
        echo json_encode($response);
        exit;
    }
    // Copy files
    static public function recursiveCopy($source, $dest){
        foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),\RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } 
            else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    static public function recursiveRemove($directory) {
        foreach(glob("{$directory}/*") as $file) {
            if(is_dir($file)) { 
                Utils::recursiveRemove($file);
            }
            else {
                unlink($file);
            }
        }
        rmdir($directory);
    }
}

// DEPRECATED, TO BE REMOVED LATER
function sendJSON($response) {
    header('Content-type: application/json');
    echo json_encode($response);
    exit;
}

?>

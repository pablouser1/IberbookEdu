<?php
// Common functions
function sendJSON($response) {
    header('Content-type: application/json');
    echo json_encode($response);
    exit;
}
?>

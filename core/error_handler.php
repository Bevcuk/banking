<?php
function process_error($e) {
    error_log($e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    echo "500 Error: ".$e->getMessage();
    exit();
}
?>


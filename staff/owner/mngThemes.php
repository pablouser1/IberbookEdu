<?php
require_once("../../functions.php");
require_once("../../headers.php");
require_once("../../helpers/db.php");
require_once("../../helpers/zip.php");

// TODO handle themes
class MngThemes {
    private $db;
    function __construct() {
        $this->db = new DB;
    }

    public function addTheme($themezip) {

    }

    public function deleteTheme($id) {

    }

}
?>

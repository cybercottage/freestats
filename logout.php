<?php
require_once("config.php");
require_once("sesvars.php");

function flushSession() {
    unset($_SESSION["freestats"]);

    session_destroy();

    return true;
}
flushSession();
?>

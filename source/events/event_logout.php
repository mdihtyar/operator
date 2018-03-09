<?php
// Processing event - exit from the system
if(!defined( 'CONSTANT' )) { die; }
//
if ((isset($_GET["mode"])) && ($_GET["mode"]=="logout") && ($user_id>0)) {
    $session->StopSession();
}
RedirectHome();
?>

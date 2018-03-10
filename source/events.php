<?php
// Events
// Cehck, if this script was not launched directle, prividing scripts secure using by system
if(!defined( 'CONSTANT' )) { die; }
//
// 1. Login into account
if (isset($_POST["mode"]) && ($_POST["mode"]=="login")) {
    include $_LSET["events_dir"]."/event_login.php";
}
elseif (isset($_GET["mode"]) && ($_GET["mode"]=="logout")) {
    include $_LSET["events_dir"]."/event_logout.php";
}
elseif (isset($_GET["mode"]) && ($_GET["mode"]==$_LSET["ptmp_show_call_logs"])) {
    include $_LSET["events_dir"]."/event_show_call_logs.php";
}
elseif (isset($_GET["mode"]) && ($_GET["mode"]==$_LSET["ptmp_admin"])) {
    include $_LSET["events_dir"]."/event_admin_mode.php";
}
elseif (isset($_GET["mode"]) && ($_GET["mode"]==$_LSET["ptmp_get_call_record"])) {
    include $_LSET["events_dir"]."/event_get_call_record.php";
}
elseif (isset($_GET["mode"]) && ($_GET["mode"]==$_LSET["ptmp_get_line_status"])) {
    include $_LSET["events_dir"]."/event_get_line_status.php";
}
elseif (isset($_GET["mode"]) && ($_GET["mode"]==$_LSET["ptmp_phone_book"])) {
    include $_LSET["events_dir"]."/event_phone_book.php";
}
?>

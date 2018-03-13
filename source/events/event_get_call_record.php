<?php
// Event processing - download call records
// Check, if script was launched directly or no, providing security of script's using
// Allow to download call records only for users from admin group
if(!defined( 'CONSTANT' )) { die; }
//
if ((isset($_GET["mode"])) && ($_GET["mode"]==$_LSET["ptmp_get_call_record"]) && ($user_id>0) && ($user->isAdmin($user_id))) {
    //
    if (isset($_GET["id"])) {
        $output_file = basename($_GET["id"]);
        $output_file = $_LSET["asterisk_call_records_dir"]."/".$output_file.".wav";
    }
    //
    if (file_exists($output_file)) {
        // file exists, begin the procesure of downloading the file for user
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: audio/x-wav');
        header('Content-Disposition: attachment; filename=' . basename($output_file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($output_file));
        if ($fd = fopen($output_file, 'rb')) {
            while (!feof($fd)) {
          	    print fread($fd, 1024);
            }
            fclose($fd);
        }
        if (ob_get_level()) {
            ob_end_clean();
        }
        //
    }
    exit;
    //
} else {
    RedirectHome();
}
?>

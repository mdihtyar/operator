<?php
    // Describe constant for files that are included for provide secure
    define('CONSTANT', true);
    //
    include_once "templates/includes.php";
    // Define instance of class for user interface template
    $tmpl = new Template($_LSET["tmpl_main"],$lang);

    $session = new MySession($_LSET,$lang);
    $user = new Users($_LSET);

    $print_user_interface = true;
    $simple_user_interface = false; // Simple user interface mode

    $user_id = ($user->UserExistByID($session->id_user))?$session->id_user:-1;
    $adwindow = ""; // Define variable of additional window
    // If something wrong with sessions, then it is required to reinit the session of user
    if (($user_id!=$session->id_user)) {
        $session->StopSession();
        RedirectHome();
    }
    //
    $tmas = array();
    $buffer = ""; // Here is will be stored content, what is required to show on display
    $possible_message = "";
    $return_button = "";
    //
    //
    if (isset($_SESSION["language"]) && (isset($lang_package[$_SESSION["language"]]))) {
      	$lang = $lang_package[$_SESSION["language"]];
    }
    //
    $msg = new Message($lang,$_LSET);
    //
    // Processing input date and form date for printing to user on right interface side
    include "events.php";

    $tmas["title"] = $lang["System_title"];
    $tmas["css_file"] = $_LSET["css_file"];

    // Main user's menu, receive variable buffer
    if (($user_id==-1) && ($buffer=="")) {
        // Cleanup data, that was stored in current session, because there should not be any data instead of data about session
        include "login.php";
        //
    } elseif ($buffer=="") {
        include "main_window.php";
    }
    //  Interface
    if ($print_user_interface) {
        $tmas["header"] = $lang["System_title"];
        $tmas["body"]=$buffer; unset($buffer);
        // The part of interface below of title
        $tmas["current_time_on_server"] = $lang["Current_time_on_server"].": ".date("H:i (d.m.Y)");
        // Lower part of interface
        $tmas["footer"] = "<div style='text-align: right;'>".$lang["Copyrights"]."<br>".$lang["Support"]."</div>";
        $tmas["return_button"]=$return_button;
        // ------------------------------------------------------------------------------------------------------------
        // ------------------------------------------------------------------------------------------------------------
        // Check, maybe it is required to be shown simple user interface
        if ($simple_user_interface) {
          $tmpl->DisableBlocks(array("with_interface"),true,true);
          $tmas["body_style"] = "id=\"simple_interface_body\"";
          if (isset($body_style)) $tmas["body_style"].=" $body_style";
        } else {
          $tmas["body_style"] = "id=\"user_interface_body\"";
          $tmas["unprocessed_calls_page_url"] = $_LSET["common_index"]."?mode=".$_LSET["ptmp_show_call_logs"]."&submode=".$_LSET["ptmp_unprocessed"];
          $tmas["processed_calls_page_url"] = $_LSET["common_index"]."?mode=".$_LSET["ptmp_show_call_logs"]."&submode=".$_LSET["ptmp_processed"];
        }
        // Print on display our processed template
        $tmpl->PrintTemplate($tmas);
    }
    // ------------------------------------------------------------------------------------------------------------
    $tmpl->Close();
    $msg->Close();
?>

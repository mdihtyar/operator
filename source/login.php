<?php
    // check, does not been launch this script directly, providing of secure using of scripts
    if(!defined( 'CONSTANT' )) { die; }
    //
    $tmpl1 = new Template($_LSET["tmpl_login"],$lang);
    $tmas0 = array();
    //
    $tmas0["greeting"] = $lang["msg_greeting"]."<br>".$lang["msg_enter_login_and_password"];
    $tmas0["login"] = $lang["login_title"];
    $tmas0["password"] = $lang["user_password"];
    $tmas0["enter_to_the_system"] = $lang["enter_to_the_system"]."...";
    $tmas0["action_script"] = $_LSET["common_index"];
    $tmas0["possible_message"] = $possible_message;
    $tmas0["additional_parameters"] = ($_SERVER["QUERY_STRING"]!="")?"<input type='hidden' name='user_query_string' value='".addslashes($_SERVER["QUERY_STRING"])."'>":"";
    //
    $buffer = $tmpl1->PrintTemplate($tmas0,false);
?>

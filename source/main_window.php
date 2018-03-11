<?php
// Procedure describing
// Check, if we launch script directly or no, providing script security
if(!defined( 'CONSTANT' )) { die; }
//
$user_data = $user->GetUserInfo($user_id);
$tmpl1 = new Template($_LSET["tmpl_main_window"],$lang);

if (!isset($possible_message)) { $possible_message = ""; }

//
$tmas0["logout"] = "<a target='_blank' href='".$_LSET["common_index"]."?mode=".$_LSET["ptmp_phone_book"]."'>".$lang["Phone_book"]."</a><br>"."<a href='".$_LSET["common_index"]."?".$_LSET["param_logout"]."'>".$lang["Logout"]."</a>";
$tmas0["user_greeting"] = $lang["User"].": ".$user_data["login"].(($user->isAdmin($user_id))?"<br><a href='".$_LSET["admin_index"]."'>".$lang["admin_mode"]."</a>&nbsp":"");
$tmas0["possible_message"] = $possible_message;
$tmas0["header_title1"] = $lang["Lines_state"];
$tmas0["header_title2"] = $lang["Incoming_call_logs"];
$tmas0["line_status_url"] = $_LSET["common_index"]."?mode=".$_LSET["ptmp_get_line_status"]."&line_id=";

//
$buffer = $tmpl1->PrintTemplate($tmas0,false);
?>

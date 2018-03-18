<?php
// Event processing, show call logs
// Check, if this script is executed directly or no, it is require to providing scripts secure usage
if(!defined( 'CONSTANT' )) { die; }
//
if ((isset($_GET["mode"]) && ($_GET["mode"]==$_LSET["ptmp_show_call_logs"])) && ($user_id>0)) {
    $ast = new Asterisk($_LSET);
    $tmpl1 = new Template($_LSET["tmpl_calls_list"],$lang);
    $tmas1 = array();

    if (isset($_GET["submode"]) && ($_GET["submode"]==$_LSET["ptmp_unprocessed"])) {
        //
        $calls = $ast->GetIncomingCallLogs("WHERE when_processed='".CONST_NULL_DATE."'");
        // if we need to analyze, if data were changed in database, or, if is is require to reload the page, if value of this hash was changed, then updating is required
        if (isset($_GET["hash"])) {
            print md5(serialize($calls));
            exit();
        }
        //
        if (is_array($calls) && (count($calls)>0)) {
            //
            $tmas1["calls_list_title"] = $lang["Unprocessed_calls_list"];
            $tmas1["calls_list"] = "";
            $tmas1["call_date"] = $lang["Calldate"];
            $tmas1["calling_number"] = $lang["Calling_number"];
            $tmas1["calling_count"] = $lang["Count"];
            $tmas1["calling_count_description"] = $lang["Calling_count"];
            $tmas1["page_selector"] = "";
            foreach ($calls as $key=>$call) {
                // check, if operator tried to process the call
                $tried_style = "";
                if ($call["tried_count"]>0) {
                    // mark, that operator have tried to process the call, but attempt failed
                    $tried_style = "background-color: yellow;";
                    //
                }
                //
                $id_list = true;
                $abonent_name = "";
                if (($ast->PhoneNumberExist($call["remote_number"],$id_list)) && (is_array($id_list))) {
                    $pb_item = $ast->GetItemsFromPB($id_list[0]);
                    if (isset($pb_item[0]["importance"]) && ($pb_item[0]["importance"] == 1)) {
                        $tried_style .= "color: red' title='{$lang['Missed_important_call']}";
                    }
                    $abonent_name = (isset($pb_item[0]["name"])?$pb_item[0]["name"]."<br>":"");
                }
                //
                $tmas1["calls_list"] .= "<tr style='$tried_style' class='my_tr_reverse' >
                <td align='center'>".($key+1).".</td>
                <td align='center'>".GetTime($call["when_created"],true)."</td>
                <td align='center'><a href='sip:".$call["remote_number"]."@".$_LSET["asterisk_server_address"]."' class='MainMenu' title='".$lang["Make_call"]."'>".$abonent_name.FormatNumber($call["remote_number"])."</a></td>
                <td align='center'>".$call["calling_count"].(($call["tried_count"]>0)?" / <span title='".$lang["Tried_count_to_process_call"]."'>".$call["tried_count"]."</span>":"")."</td></tr>
                ";
            }
            $tmas1["calls_list"] .= "<tr><td colspan=4>&nbsp;<br>".$lang["Legend"].":</td></tr>
            <tr><td style='background-color: yellow; '></td><td colspan=3 style='padding-left: 10px;'> - ".$lang["tried_to_process_call"]."</td></tr>
            <tr><td style='color: red; '>X</td><td colspan=3 style='padding-left: 10px;'> - ".$lang["marked_important_call"]."</td></tr>";

            $buffer = $tmpl1->PrintTemplate($tmas1,false);
            //
        } else {
          $buffer = $lang["Unprocessed_calls_do_not_exist"];
        }
    }
    elseif (isset($_GET["submode"]) && ($_GET["submode"]==$_LSET["ptmp_processed"])) {
        // find out parameters of pages to review
        if (isset($_GET["page"])) {
            $_SESSION["processed_calls_list_page"] = (int)$_GET["page"];
        }
        $current_page = isset($_SESSION["processed_calls_list_page"])?(int)$_SESSION["processed_calls_list_page"]:1;
        $records_count = $ast->GetIncomingCallsCount("when_processed<>'".CONST_NULL_DATE."'");
        $pages_count = ceil(($records_count / $_LSET["processed_call_records_on_page"]));
        if (($pages_count>0) && ($current_page>0)) {
            $page_selector = "-- $current_page/$pages_count --";
            $page_selector = (($current_page!=1)?"<a href='".$_LSET["common_index"]."?mode=".$_LSET["ptmp_show_call_logs"]."&submode=".$_LSET["ptmp_processed"]."&page=".($current_page-1)."'><--</a> ":"").$page_selector;
            $page_selector.= ($current_page!=$pages_count)?"<a href='".$_LSET["common_index"]."?mode=".$_LSET["ptmp_show_call_logs"]."&submode=".$_LSET["ptmp_processed"]."&page=".($current_page+1)."'>--></a>":"";
            $page_selector = $lang["Page"].": ".$page_selector;
        } else {
            $page_selector = "";
        }
        //
        $current_page--;
        $limit_query = " LIMIT ".($current_page*($_LSET["processed_call_records_on_page"])).",".($_LSET["processed_call_records_on_page"]);
        if ($current_page<0) {
            $limit_query = "";
        }
        //

        $calls = $ast->GetIncomingCallLogs("WHERE when_processed<>'".CONST_NULL_DATE."'",$limit_query,"ORDER BY when_created DESC");
        if (isset($_GET["hash"])) {
            print md5(serialize($calls));
            exit();
        }
        if (is_array($calls) && (count($calls)>0)) {
            //
            $tmas1["calls_list_title"] = $lang["Processed_calls_list"];
            $tmas1["calls_list"] = "";
            $tmas1["call_date"] = $lang["Calldate"];
            $tmas1["calling_number"] = $lang["Calling_number"];
            $tmas1["calling_count"] = $lang["Count"];
            $tmas1["calling_count_description"] = $lang["Calling_count"];
            $tmas1["page_selector"] = $page_selector;
            $i = $current_page*$_LSET["processed_call_records_on_page"];
            foreach ($calls as $key=>$call) {
                // generate an inforation buklet for record about call
                $i++;
                $title = ""; $listen_onclick = ""; $cursor_style = "";
                if ($user->isAdmin($user_id)) {
                    // generate link for listening the phone call record, if such record exist
                    $cdr = $ast->GetCallInfo($call["uniqueid"]);
                    if (isset($cdr[0])) {
                        $possible_file = $ast->GetRecordFile($cdr[0]);
                        // generate a link about listening of the processed call
                        if ($possible_file !== false) {
                            $listen_onclick = "onclick='topLocation(\"".$_LSET["common_index"]."?mode=".$_LSET["ptmp_admin"]."&submode=".$_LSET["ptmp_play_call_record"]."&id=".$cdr[0]["uniqueid"]."\");'";
                            $title = $lang["Call_info"]."...";
                            $cursor_style = "cursor: pointer;";
                        }
                    //
                    }
                //
                }
                //
                $tmas1["calls_list"] .= "<tr class='my_tr_reverse' $listen_onclick title='$title' style='$cursor_style'>
                <td align='center'>".($i).".</td>
                <td align='center'>".GetTime($call["when_created"],true)."</td>
                <td align='center'>".FormatNumber($call["remote_number"])."</td>
                <td align='center'>".$call["calling_count"]."</td></tr>
                ";
            }
            $buffer = $tmpl1->PrintTemplate($tmas1,false);
            //
        } else {
            $buffer = $lang["Processed_calls_do_not_exist"];
        }
    }
    elseif (isset($_GET["submode"]) && ($_GET["submode"]==$_LSET["ptmp_all"])) {
        $buffer = $lang["list_of_all_calls"];
    }
    if ($buffer!="") {
        $simple_user_interface = true;
        $body_style="class='BODY_CALL_LOGS'";
    }
}
?>

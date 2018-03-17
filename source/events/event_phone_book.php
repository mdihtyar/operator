<?php
// Event processing - show phone book
// Check, if current script is not launched directly, provide secrity of script using
if(!defined( 'CONSTANT' )) { die; }
//
if ((isset($_GET["mode"]) && ($_GET["mode"]==$_LSET["ptmp_phone_book"])) && ($user_id>0)) {
    $ast = new Asterisk($_LSET);
    $tmpl1 = new Template($_LSET["tmpl_phone_book"],$lang);
    $possilbe_message = $head_possible_message = "";
    $add_completed = false;
    // --------------------------------------------------------------------------------------------
    // processing possible events
    // adding new contacts into phone book
    if ((isset($_GET["submode"])) && ($_GET["submode"]==$_LSET["ptmp_add"]) && (isset($_POST["procced"]))) {
        // checking correct of data, if everything is ok, then add the data, else print correcponding message
        $aname = addslashes(trim(isset($_POST["abonent_name"])?$_POST["abonent_name"]:""));
        $anumber = addslashes(trim(isset($_POST["abonent_number"])?$_POST["abonent_number"]:""));
        $importance = (isset($_POST["importance"])?1:0);
        $id_list = true;

        if (($aname == "") || ($anumber == "")) {
            $possible_message = $msg->ShowMessage($lang["Not_all_fields"]."!",false);
        } else {
            // checking, does there already exists identified data in phone book
            if (($ast->PhoneNumberExist($anumber,$id_list))) {
                //
                $possible_message = $msg->ShowMessage($lang["Record_is_exist"]."!",false);
            } elseif (!$ast->CheckPhoneNumber($anumber)) {
                $possible_message = $msg->ShowMessage($lang["Incorrect_phone_number"]."!",false);
            } else {
                // go ahead inside the procesure of adding a record into phone book
                if ($ast->AddToPhoneBook($aname,$anumber,$importance)) {
                    $possible_message = $msg->ShowMessage($lang["Contact_successfully_added"]."!",true,$_LSET["common_index"]."?mode=".$_LSET["ptmp_phone_book"]);
                    $add_completed = true;
                }
            }
        }
    }
    // delete a record from phone book
    elseif (isset($_GET["submode"]) && ($_GET["submode"]==$_LSET["ptmp_del"]) && (isset($_GET["id"]))) {
        $item = $ast->GetItemsFromPB((int)$_GET["id"]);
        if ($item !== false) {
            if (isset($_GET["msg_answer"]) && ($_GET["msg_answer"] == $lang["mrYes"])) {
                if ($ast->DelFromPhoneBook($item[0]["id"])) {
                    $head_possible_message = $msg->ShowMessage($lang["Record_deleted_successfully"]."!",true,"{$_LSET['common_index']}?mode={$_LSET['ptmp_phone_book']}");
                } else {
                    $head_possible_message = $msg->ShowMessage("{$lang['Error']}",true,"{$_LSET['common_index']}?mode={$_LSET['ptmp_phone_book']}");
                }
            } elseif (!isset($_GET["msg_answer"])) {
                $phone_number = FormatNumber($item[0]["phone_number"],(substr($item[0]["phone_number"],0,1) == "+")?false:true);
                $head_possible_message = $msg->SimpleQuestion($lang["Question_2"]."<div style='font-size: 10pt; color: black; padding-left: 50px; text-align: left;'>1. {$lang['abonent_name']}: {$item[0]['name']} <br>2. {$lang['Abonent_phone_number']}: $phone_number</div>");
            }
        } else {
            $head_possible_message = $msg->ShowMessage($lang["Record_is_not_exist"]."!",true,$_LSET["common_index"]."?mode={$_LSET['ptmp_phone_book']}");
        }
    }
    // --------------------------------------------------------------------------------------------
    // generate data for playback
    $pb_items = $ast->GetItemsFromPB();
    if ($pb_items === false) {
        // there are no records in phone book
        $tmas1["records_list"] = "<h3>".$lang["Phone_book_is_empty"]."</h3>";
    } else {
        // generate list of phone book records
        // use template for printing list of numbers
        $tmpl2 = new Template($_LSET["tmpl_calls_list"],$lang);
        $tmas2 = array();
        $tmas2["calls_list_title"] = $lang["Phone_book"];
        $tmas2["call_date"] = $lang["abonent_name"];
        $tmas2["calling_number"] = $lang["Abonent_phone_number"];
        $tmas2["calling_count"] = "";
        $tmas2["page_selector"] = "";
        $tmas2["calling_count_description"] = "' style='background-color: #cfdca9;'";
        $tmas2["calls_list"] = "";

        for ($i=0;$i<count($pb_items);$i++) {
            //
            $record_exist_style = "";
            $phone_number = $pb_items[$i]["phone_number"];
            $phone_number = FormatNumber($phone_number,(substr($phone_number,0,1) == "+")?false:true);
            $operations = "<div id='del$i' style='visibility: hidden;'><a href='".$_LSET["common_index"]."?mode=".$_LSET["ptmp_phone_book"]."&submode={$_LSET['ptmp_del']}&id={$pb_items[$i]['id']}' title='".$lang["Delete_record"]."'><img src='".$_LSET["www_img_delete"]."'></a><div>";
            //
            if ((isset($id_list)) && (is_array($id_list)) && (in_array($pb_items[$i]["id"],$id_list))) {
                $record_exist_style = "style='background-color: red;'";
            }
            //
            $tmas2["calls_list"] .= "<tr $record_exist_style title='".($pb_items[$i]["importance"]==1?$lang["Record_is_important"]:"")."' class='my_tr' onmouseover='document.getElementById(\"del$i\").style.visibility = \"visible\";' onmouseout='document.getElementById(\"del$i\").style.visibility = \"hidden\";'>
            <td align='center'>".($i+1).".</td>
            <td style='padding-left: 10px;'>".($pb_items[$i]["importance"]==1?"<u>":"").$pb_items[$i]["name"].($pb_items[$i]["importance"]==1?"</u>":"")."</td>
            <td style='padding-left: 10px;'><a href='sip:".$pb_items[$i]["phone_number"]."@".$_LSET["asterisk_server_address"]."' class='MainMenu' title='".$lang["Make_call"]."'>$phone_number</a></td>
            <td align='center' ".(((isset($id_list)) &&  (is_array($id_list)) && (in_array($pb_items[$i]["id"],$id_list)))?"style='background-color: #cfdca9;'":"").">".$operations."</td>
            </tr>";
        }
        //
        $tmpl2->DisableBlocks(array("template-script"),true);
        $tmas1["records_list"] = $tmpl2->PrintTemplate($tmas2,false);
    }
    //
    //
    $tmas1["add_new_number_title"] = $lang["Add_phone_number"];
    $tmas1["abonent_name_title"] = $lang["abonent_name"];
    $tmas1["abonent_phone_number_title"] = $lang["Abonent_phone_number"];
    $tmas1["abonent_phone_number_use_only"] = "";
    $tmas1["importance_title"] = $lang["important_phone_number"];
    $tmas1["add_title"] = $lang["Add"];
    $tmas1["return_button"] = "<a href='".$_LSET["common_index"]."'>".mb_ucfirst($lang["return"])."...</a>";
    $tmas1["action_script"] = $_LSET["common_index"]."?mode=".$_LSET["ptmp_phone_book"]."&submode=".$_LSET["ptmp_add"];
    $tmas1["possible_message"] = $possible_message;
    $tmas1["head_possible_message"] = $head_possible_message;
    $tmas1["PS"] = "<i>{$lang['Legend']} {$lang['and']} {$lang['explanation']}:</i><div style='padding-left: 20px;'><p>1. {$lang['message_2']}</p><p>2. {$lang['message_3']}</p><p>3. {$lang['message_4']}</p></div>";
    // possible values
    $tmas1["aname_value"] = ((isset($_POST["abonent_name"]) && !$add_completed)?$_POST["abonent_name"]:"");
    $tmas1["anumber_value"] = ((isset($_POST["abonent_number"]) && !$add_completed)?$_POST["abonent_number"]:"");
    $tmas1["importance_value"] = ((isset($_POST["importance"]) && !$add_completed)?" checked":"");
    //
    $buffer = $tmpl1->PrintTemplate($tmas1,false);
}
?>

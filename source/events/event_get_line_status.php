<?php
// Event processing - get line status
// Return a status of specific phone line
if(!defined( 'CONSTANT' )) { die; }
//
if (isset($_GET["mode"]) && ($_GET["mode"]==$_LSET["ptmp_get_line_status"]) && ($user_id>0)) {
    if (isset($_GET["line_id"])) {
        $ast = new Asterisk($_LSET);
        $line_id = (int)$_GET["line_id"];
        //
        $line_status = $ast->GetLineStatus($line_id);
        if ($line_status===false) {
            $line_status = $ast->SetLineStatus($line_id);
        }
        if (isset($_GET["channel_description"])) {
            $desc = "";
            // check, is there current number in phone book
            $id_list = true;
            $possible_abonent_name = "";
            if ($ast->PhoneNumberExist($line_status["phone_number"],$id_list)) {
                if (is_array($id_list) && (count($id_list)>0) && isset($id_list[0])) {
                    $pb_item = $ast->GetItemsFromPB($id_list[0]);
                    $possible_abonent_name = (isset($pb_item[0]["name"]))?$pb_item[0]["name"]."<br>":"";
                    if ($pb_item[0]["importance"] == 1) {
                        $possible_abonent_name = "<span style='font-weight: bold; color: red;'>$possible_abonent_name</span>";
                    }
                }
            }
            //
            switch ($line_status["way"]) {
                case _IN : $desc = $lang["Incoming_call"].":<br>$possible_abonent_name".FormatNumber($line_status["phone_number"]); break;
                case _OUT : $desc = $lang["Outgoing_call"].":<br>$possible_abonent_name".FormatNumber($line_status["phone_number"]); break;
                default : $desc = "";
            }
            print $desc;
        } else {
            print $line_status["current_state"];
        }
        //
    }
    exit;
}
?>

<?php
// Event processing - user login into system
// Checking, was it direct script executing or no
if(!defined( 'CONSTANT' )) { die; }
//
if ((isset($_GET["mode"])) && ($_GET["mode"]==$_LSET["ptmp_admin"]) && ($user_id>0) && ($user->isAdmin($user_id))) {
    $ast = new Asterisk($_LSET); // class to work with IP-PBX Asterisk
	  // show detailed information about calls
  	if (isset($_GET["submode"]) && ($_GET["submode"] == $_LSET["ptmp_show_calls_detailed"])) {
        // make interval as one day for today
        $start_date = GetTime(time(),false,"d.m.Y 00:00:00");
        $end_date = GetTime(time(),false,"d.m.Y 23:59:59");
        //
        $operators_list = "<option value="._NULL." selected>".$lang["All_operators"]."</option>";
        foreach ($_LSET["operators"] as $operator) {
            $operators_list .= "<option value='".$operator."'>".(isset($_LSET["phone_desc"][$operator])?$_LSET["phone_desc"][$operator]:$operator)."</option>";
        }
        $operations = $lang["Possible_operations"].": <ul><li><a href='".$_LSET["admin_index"]."&o=".$_LSET["ptmp_clean_unprocessed_calls_list"]."'>".$lang["Clean_unprocessed_calls_list"]."</a></li><br>
        <form method='get' action='".$_LSET["common_index"]."'>
        <input type='hidden' name='mode' value='".$_LSET["ptmp_admin"]."'><input type='hidden' name='submode' value='".$_LSET["ptmp_show_calls_report"]."'>
        <li>".$lang["Create_report_on_period"]." ".$lang["from"]." <input type='text' name='repbegin' value='$start_date'> ".$lang["till"]." <input type='text' name='repend' value='$end_date'>
        <select name='operator' title='".$lang["View_mode"]."'>
        $operators_list
        </select>
        <input type='submit' name='createreport' value='".$lang["Generate"]."...'>
        </li>
        </form>
        </ul>";
        if (isset($_GET["o"]) && ($_GET["o"] == $_LSET["ptmp_clean_unprocessed_calls_list"])) {
            // ask user about, does he desire to clear the list
            if (!isset($_GET["msg_answer"])) {
                $operations = $msg->SimpleQuestion($lang["Question_1"]);
            }
            elseif ($_GET["msg_answer"] == $lang["mrYes"]) {
                // execute the cleaning of unprocessed calls
                $ast->CleanIncommingCalls();
                $operations .= $msg->ShowMessage($lang["Operation_is_completed"]);
                //
            }
	      }
        $tmpl1 = new Template($_LSET["tmpl_show_calls_menu"],$lang);
        $tmas1["view"] = mb_ucfirst($lang["view"]);
        $tmas1["return_button"] = "<a href='".$_LSET["common_index"]."'>".mb_ucfirst($lang["return"])."...</a>";
        $tmas1["call_logs_title"] = mb_ucfirst($lang["detailed"])." ".$lang["call_logs"];
        $tmas1["calls_list"] = "";
        $tmas1["call_date"] = $lang["Calldate"];
        $tmas1["src_number"] = $lang["Src"]."?";
        $tmas1["dst_number"] = $lang["Dst"]."?";
        $tmas1["duration"] = $lang["Duration"];
        $tmas1["call_result"] = $lang["Disposition"];
        $tmas1["possible_message"] = $possible_message;
        $tmas1["operations"] = $operations;
        $current_page = (isset($_GET["page"])?(int)$_GET["page"]:1);
        //
        $cdrlist = $ast->GetCDRList($current_page);
        // make calls journal from received data
        $fixed_date="";
  	    if (count($cdrlist)>0) {
            // determine parameters of pages to review
        		$pages_count = ceil(($ast->GetCDRCount() / $_LSET["records_on_page"]));
        		if (($pages_count>0) && $current_page>0) {
        		    $page_selector = "-- $current_page/$pages_count --";
        		    $page_selector = (($current_page!=1)?"<a href='".$_LSET["admin_index"]."&page=".($current_page-1)."'><--</a> ":"").$page_selector;
        		    $page_selector.=($current_page!=$pages_count)?" <a href='".$_LSET["admin_index"]."&page=".($current_page+1)."'>--></a>":"";
        		    $page_selector = $lang["Page"].": ".$page_selector;
        		} else {
        		    $page_selector = "";
        		}
        		//
        		foreach ($cdrlist as $key=>$value) {
        		    $disposition = "";
        		    switch ($value["disposition"]) {
              			case "ANSWERED" : $disposition = mb_ucfirst($lang["answered"]); break;
              			case "NO ANSWER" : $disposition = mb_ucfirst($lang["no_answer"]); break;
              			case "BUSY" : $disposition = mb_ucfirst($lang["busy"]); break;
        		    }
        		    // split phone calls by date
                $current_date = GetTime($value["calldate"],true,"d.m.Y");
        		    if ($current_date!=$fixed_date) {
              			$fixed_date = $current_date;
              			$tmas1["calls_list"] .= "<tr><td colspan=6 style='padding: 10px; text-align: center;'>".$lang["Information_on_the_date"].": <b>$fixed_date</b></td></tr>";
        		    }
        		    // determine, if there is a call recoed
        		    $possible_file = $ast->GetRecordFile($value);
        		    $add_tr_style = "";
        		    if ($possible_file!=false) {
              			$add_tr_style = " style='cursor: pointer'; title='".$lang["Play_call_record"]."' onclick='location.href=\"".$_LSET["common_index"]."?mode=".$_LSET["ptmp_admin"]."&submode=".$_LSET["ptmp_play_call_record"]."&id=".$value["uniqueid"]."\";'";
        		    }
        		    //
        		    $tmas1["calls_list"] .= "<tr class='my_tr'$add_tr_style>
        		    <td align='center'>".($key+1).".</td>
        		    <td align='center'>".GetTime($value["calldate"],true,"H:i:s")."</td>
        		    <td align='center'>".$ast->PhoneDescription($value["src"])."</td>
        		    <td align='center'>".$ast->PhoneDescription($value["dst"])."</td>
        		    <td align='center'>".$value["billsec"]." ".$lang["sec"]."</td>
        		    <td align='center'>".$disposition."</td>
        		    </tr>";
        		}
        		$tmas1["page_selector"] = $page_selector;
  	    } else {
        		$tmpl1->DisableBlocks(array("call_logs"),true,true);
        		$tmas1["call_logs_title"] = "";
        		$tmas1["calls_list"] = $msg->ShowMessage(mb_ucfirst($lang["call_logs_information_not_found"])."!",false);
  	    }
  	    //
  	    $buffer = $tmpl1->PrintTemplate($tmas1,false);
  	} elseif (isset($_GET["submode"]) && ($_GET["submode"]==$_LSET["ptmp_play_call_record"]) && (isset($_GET["id"]))) {
  	    $tmpl1 = new Template($_LSET["tmpl_call_info"],$lang);
  	    $call = $ast->GetCallInfo(addslashes($_GET["id"]));
  	    // print information about call, if call with such id exists
  	    if (count($call)!=0) {
        		//
        		$possible_incomming_call = $ast->GetIncomingCallLogs("WHERE uniqueid='".$call[0]["uniqueid"]."'");
        		$tried_count = 0;
        		if (isset($possible_incomming_call[0])) {
        		    $tried_count = $possible_incomming_call[0]["tried_count"];
        		    $tmas1["tried_count_title"] = $lang["Tried_count_to_process_call"];
        		    $tmas1["tried_count"] = $tried_count;
        		    $tmas1["when_last_tried"] = $lang["Last_tried_to_process_call"];
        		    $tmas1["when_tried"] = GetTime($possible_incomming_call[0]["when_tried"],true);
        		    $tmas1["calling_count_title"] = $lang["Calling_count"];
        		    $tmas1["calling_count"] = $possible_incomming_call[0]["calling_count"];
        		}
        		if ($tried_count == 0) {
        		    $tmpl1->DisableBlocks(array("tried_info"),true);
        		}
        		$tmas1["return_button"] = "<a href='javascript:history.back();'>".mb_ucfirst($lang["return"])."...</a>";
        		$tmas1["calldate"] = GetTime($call[0]["calldate"],true,"d.m.Y H:i:s");
        		$tmas1["clid"] = $call[0]["clid"];
        		$tmas1["src"] = $ast->PhoneDescription($call[0]["src"]);
        		$tmas1["dst"] = $ast->PhoneDescription($call[0]["dst"]);
        		$tmas1["duration"] = $call[0]["duration"]." ".$lang["sec"];
        		$tmas1["billsec"] = $call[0]["billsec"]." ".$lang["sec"];
        		$tmas1["calldate_title"] = $lang["Calling_start"];
        		$tmas1["clid_title"] = $lang["Clid"];
        		$tmas1["src_title"] = $lang["Src"]."?";
        		$tmas1["dst_title"] = $lang["Dst"]."?";
        		$tmas1["duration_title"] = $lang["Common"]." ".$lang["duration"];
        		$tmas1["billsec_title"] = $lang["Real"]." ".$lang["duration"];
        		$tmas1["disposition_title"] = $lang["Disposition"];
        		$tmas1["call_info_title"] = $lang["Call_info"];
        		// check if record exists
        		$possible_file = $ast->GetRecordFile($call[0]);
        		if ($possible_file!==false) {
        		    // file exists and it can be played
        		    $tmas1["listen_a_call_record"] = $lang["Listen_a_call_record"];
        		    $tmas1["download_call_record"] = $lang["Download_call_record"];
        		    $tmas1["download_img"] = $_LSET["www_img_save"];
        		    $tmas1["call_record_url"] = $_LSET["common_index"]."?mode=".$_LSET["ptmp_get_call_record"]."&id=".$call[0]["uniqueid"];
        		    $tmas1["filesize"] = $lang["size_is"]." ".SizeToStr(filesize($possible_file));
        		} else {
        		    // do not print block for playing call record
        		    $tmpl1->DisableBlocks(array("listen_call_record"),true);
        		}
        		//
        		$disposition = "";
    		    switch ($call[0]["disposition"]) {
          			case "ANSWERED" : $disposition = mb_ucfirst($lang["answered"]); break;
          			case "NO ANSWER" : $disposition = mb_ucfirst($lang["no_answer"]); break;
          			case "BUSY" : $disposition = mb_ucfirst($lang["busy"]); break;
    		    }
        		$tmas1["disposition"] = $disposition;
        		$buffer = $tmpl1->PrintTemplate($tmas1,false);
      	    // else print message about error
  	    } else {
        		$buffer = $msg->ShowMessage($lang["Error"],true,$_LSET["admin_index"]);
  	    }
  	} elseif (isset($_GET["submode"]) && ($_GET["submode"] == $_LSET["ptmp_show_calls_report"])) {
	    // pricessing defined period
	    $repbegin = "";
	    $correct_period = false;
	    if (isset($_GET["repbegin"]) && (isset($_GET["repend"]))) {
      		$repbegin = addslashes($_GET["repbegin"]);
      		$repend = addslashes($_GET["repend"]);
      		$start = strtotime($repbegin);
      		$end = strtotime($repend);
      		$start_time = GetTime($start,false);
      		$end_time = GetTime($end,false);
      		if (($start_time == $repbegin) && ($end_time == $repend)) {
      		    $correct_period = true;
      		    $calls = $ast->GetIncomingCallLogs("WHERE when_created>='".GetDBTime($start)."' AND when_created<='".GetDBTime($end)."'","","ORDER BY when_created DESC");
      		}
	    }
	    //
	    if (($correct_period) && ($calls !==false)) {
       		$tmpl1 = new Template($_LSET["tmpl_calls_list"],$lang);
      		$simple_user_interface = true;
      		$tmpl1->DisableBlocks(array("template-script"),true);
      		$operator = (int)$_GET["operator"];
      		//
      		$tmas1["calls_list"] = "";
      		if (count($calls)>0) {
      		    $i=0;
      		    foreach ($calls as $call) {
            			$duration = $lang["Call_is_not_processed"];
            			if ($call["uniqueid"]!="") {
            			    $ast_call = $ast->GetCallInfo($call["uniqueid"]);
            			    if (count($ast_call)>0) {
            				$duration = $ast_call[0]["billsec"];
            				if (($operator!=_NULL)  && ((strstr($ast_call[0]["channel"],"SIP/$operator")===false) && (strstr($ast_call[0]["dstchannel"],"SIP/$operator")===false))) {
            				    continue;
            				}
            			    } else { continue; }
            			} elseif ($operator!=_NULL) {
            			    continue;
            			}
            			$i++;
            			$abonent_name = ""; $id_list = true;
            			if (($ast->PhoneNumberExist($call["remote_number"],$id_list)) && (is_array($id_list))) {
            			    $pb_item = $ast->GetItemsFromPB($id_list[0]);
            			    $abonent_name = (isset($pb_item[0]["name"])?$pb_item[0]["name"]."<br>":"");
            			}
            			$disposition = "<img src='".$_LSET["www_img_disable_24"]."'>";
            			if (($call["when_processed"]!=CONST_NULL_DATE) && ((strtotime($call["when_processed"])-strtotime($call["when_created"]))<=_DIAL_WAIT_TIME )) {
            			    $disposition = "<img src='".$_LSET["www_img_answered"]."'>";
            			}
            			$waiting_time = "";
            			if (($call["when_processed"]!=CONST_NULL_DATE)) {
            			    $waiting_time = strtotime($call["when_processed"])-strtotime($call["when_created"]);
            			    $waiting_time = sprintf('%02d:%02d:%02d', $waiting_time/3600, ($waiting_time % 3600)/60, ($waiting_time % 3600) % 60);
            			}
            			$tmas1["calls_list"] .= "<tr><td align='center'>$i.</td><td align='center'>".$abonent_name.FormatNumber($call["remote_number"])."</td>
            			<td align='center'>$disposition</td>
            			<td align='center'>".GetTime(strtotime($call["when_created"]))."</td>
            			<td align='center'>".(($call["when_processed"]!=CONST_NULL_DATE)?GetTime(strtotime($call["when_processed"])):"")."</td>
            			<td align='center'>".(($call["when_processed"]!=CONST_NULL_DATE)?$waiting_time:"")."</td>
            			<td align='center'>".$call["calling_count"]."</td>
            			<td align='center'>".$call["tried_count"]."</td>
            			<td align='center'>".$duration."</td>
            			</tr>\n";
      		    }
      		}
	        $tmas1["calls_list_title"] = $lang["Report_on_period"]." ".$lang["from"]." ".$start_time." ".$lang["till"]." ".$end_time.(($operator!=_NULL)?"<br>".((isset($_LSET["phone_desc"][$operator]))?$_LSET["phone_desc"][$operator]:$operator):"");
	        $tmas1["page_selector"] = "";
	        $tmas1["call_date"] = $lang["Calling_number"]."</td><td>".$lang["Disposition"];
	        $tmas1["calling_number"] = $lang["Incoming"];
	        $tmas1["calling_count_description"] = "";
	        $tmas1["calling_count"] = $lang["Processing"]."<td>".$lang["Waiting_time"]."</td><td>".$lang["Calling_count"]."</td><td>".$lang["Tried_count_to_process_call"]."</td><td>".$lang["Duration"]."</td>";
      		//
      		$buffer = ($tmas1["calls_list"]=="")?$lang["List_is_empty"]:$tmpl1->PrintTemplate($tmas1,false);
      		$buffer = str_replace("0px","1px",$buffer);
	    } else {
      		if (!$correct_period) {
      		    $buffer = $msg->ShowMessage($lang["Incorrect_period"],true,$_LSET["admin_index"]);
      		} elseif (!$calls) {
      		    $buffer = $msg->ShowMessage($lang["This_period_is_empty"],true,$_LSET["admin_index"]);
      		}
	    }
	}
}
?>

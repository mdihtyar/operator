<?php
// Common functions
//
function GetCurrentDBTime($timestamp=-1) {
	if ($timestamp==-1) {
	    return date("Y-m-d H:i:s");
	} else { return date("Y-m-d H:i:s",$timestamp); }
}

function GetDBTime($timestamp) {
	return GetCurrentDBTime($timestamp);
}

function GetTime($timestamp=-1, $dbtime=false, $time_format="d.m.Y H:i:s") {
    if ($timestamp == -1) {
	return date($time_format);
    } else {
	if ($dbtime) {
	    return date($time_format,strtotime($timestamp));
	} else {
	    return date($time_format,$timestamp);
	}
    }
}
//
function FixEvent($event_message) {
	global $_LSET;
	$db = new MySQLClient($_LSET["mysql_server"],$_LSET["mysql_db"],$_LSET["mysql_user"],$_LSET["mysql_password"]);
	$query = "INSERT INTO ".$_LSET["tbl_logs"]." (when_occurred, event_description) VALUES ('".GetCurrentDBTime()."','$event_message')";
	$db->Query($query);
	$db->Close();
}
//
// Make bytes more readable
function SizeToStr($size) {
	$kb = 1024;
	$mb = 1024 * $kb;
	$gb = 1024 * $mb;
	$tb = 1024 * $gb;
	if ($size<$kb) {
	    return $size." B";
	} elseif ($size<$mb) {
	    return round($size/$kb,2)."KB";
	} elseif ($size<$gb) {
	    return round($size/$mb,2)."MB";
	} elseif ($size<$tb) {
	    return round($size/$gb,2)."GB";
	} else {
	    return round($size/$tb,2)."TB";
	}
}
//
// This function splits array and returns parameters by hidden type for further using in forms
function GetHiddenValues($mas) {
    $buffer = "";
    foreach ($mas as $key=>$value) {
	$buffer .= "<input type='hidden' name='$key' value='$value'>";
    }
    return $buffer;
}
//
function RedirectHome($url = "") {
    global $_LSET;
    $url = ($url=="")?(($_LSET["www_root_dir"]=="")?"/":$_LSET["www_root_dir"]):$url;
    header("Location:".$url);
}
//
mb_internal_encoding("UTF-8");
// Make the first char input string with upper case
function mb_ucfirst($text) {
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}
// Form intercity phone number
function FormatNumber($phone_number, $simple_number=true) {
    // to input we receive phone number
    if (($simple_number) && (strlen($phone_number) == 10)) {
				//(066)123-45-67
				$phone_number = "(".substr($phone_number,0,3).") ".substr($phone_number,3,3)."-".substr($phone_number,6,2)."-".substr($phone_number,8,2);
		} elseif (!$simple_number && strlen($phone_number)==CONST_POSSIBLE_NUMBER_LENGTH) {
				$phone_number = substr($phone_number,0,3)."(".substr($phone_number,3,3).") ".substr($phone_number,6,3)."-".substr($phone_number,9,2)."-".substr($phone_number,11,2);
		}
    return $phone_number;
}
?>

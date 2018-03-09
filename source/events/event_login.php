<?php
// Processing event, login into system
// Check, does not been launch this script directly, providing of secure using of scripts
if(!defined( 'CONSTANT' )) { die; }
    //
    if ((isset($_POST["mode"]) && ($_POST["mode"]=="login") && (isset($_POST["procced"])))) {
        $user_login = (isset($_POST["login"])?$_POST["login"]:"");
        $user_password = (isset($_POST["password"])?$_POST["password"]:"");
        if ($user->CheckUserData($user_login,$user_password)) {
            if ($session->CreateTicket($user->GetUserID($user_login))) {
                // redirect to the main page
                if (isset($_POST["user_query_string"])) {
                    RedirectHome($_LSET["www_common_index"]."?".$_POST["user_query_string"]);
                } else {
                    RedirectHome();
                }
      	    }
        } else {
            $possible_message = $msg->ShowMessage($lang["authentication_failed"],false);
        }
    }
?>

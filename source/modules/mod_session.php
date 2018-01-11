<?php
// Class to work with sessions
class MySession {
    var $set;
    var $lang;
    var $id;
    var $db;
    var $id_user;
    var $user;
    function MySession($settings,$language) {
      	$this->id_user = -1; // declare, that we do not known session owner yet
      	$this->set = $settings;
      	$this->lang = $language;
      	$this->user = new Users($this->set);
      	$this->db = new MySQLClient($this->set["mysql_server"],$this->set["mysql_db"],$this->set["mysql_user"],$this->set["mysql_password"]);
      	if (!$this->SessionInit()) {
      	    exit($this->lang["Error_to_use_session"]);
      	}
      	$this->id = htmlspecialchars(session_id());
      	// check, maybe it is required to clean sessions pool
      	$this->CheckPool(); // check pool and if it is required delete old sessions
      	$this->id_user = $this->CheckTicket($this->id); // determine sessions's owner. if such exists
    }
    function ReInitSession() {
      	return session_regenerate_id();
    }
    function SessionInit() {
      	if (isset($this->set["session_name"])) {
    	    session_name($this->set["session_name"]);
      	}
      	if (session_start()) {
    	    return true;
      	}
      	return false;
    }
    function StopSession() {
      	if ($this->SessionExist($this->id)) {
    	    $query = "DELETE FROM ".$this->set["tbl_sessions"]." WHERE id='".$this->id."'";
    	    session_destroy();
    	    return $this->db->Query($query);
      	}
      	return false;
    }
    function GetClientInfo() {
	    //
    }
    function CheckTicket($session_id) {
      	if ($this->SessionExist($session_id)) {
    	    $this->db->Query("SELECT id_owner FROM ".$this->set["tbl_sessions"]." WHERE id='$session_id'");
    	    $res = $this->db->GetLastresult();
      		return (isset($res[0]["id_owner"])?$res[0]["id_owner"]:-1);
      	}
      	return -1;
    }
    function CreateTicket($user_id) {
      	$user = new Users($this->set);
      	if ($user->UserExistByID($user_id)) {
    	    $user_data = $user->GetUserInfo($user_id);
    	    // Data for creating the ticket
    	    $mas["session_id"] = $this->id; // session's ID
    	    $mas["user_agent"] = htmlspecialchars($_SERVER["HTTP_USER_AGENT"]); // user's agent
    	    $mas["created_time"] = date("Y.m.d H:i:s"); // time of creating the ticket
    	    $mas["login"] = $user_data["login"]; // user's login
    	    $mas["remote_addr"] = htmlspecialchars($_SERVER["REMOTE_ADDR"]); // remote address of user
    	    $mas["pass_hash"] = crypt($user_data["pass"],time()); // user's password hash
    	    // check, is not exist ticket for current session
    	    if (!$this->SessionExist($mas["session_id"])) {
        		$query = "INSERT INTO ".$this->set["tbl_sessions"]." (id,id_owner,when_created,user_agent,remote_addr) VALUES (
      		    '".$mas["session_id"]."',
      		    ".$user_id.",
      		    '".$mas["created_time"]."',
      		    '".$mas["user_agent"]."',
      		    '".$mas["remote_addr"]."')";
        		$this->db->Query($query);
        		return true;
    	    }
      	} else {
    	    return false;
      	}
    }
    // Check
    function SessionExist($session_id) {
      	if ($this->db->RecordsCount($this->set["tbl_sessions"],"id='$session_id'")>0) {
    	    return true;
      	} else {
    	    return false;
      	}
    }
    //
    function CheckPool() {
      	$count = $this->db->RecordsCount($this->set["tbl_sessions"]);
      	if ($count > $this->set["session_max"]) {
    	    // sorting of the session by adding order
    	    $query = "SELECT id FROM ".$this->set["tbl_sessions"]." ORDER BY when_created ASC LIMIT 0,".($count-$this->set["session_max"]);
    	    $this->db->Query($query,true);
    	    $res = $this->db->GetLastResult();
    	    if (count($res)>0) {
        		$query = "DELETE FROM ".$this->set["tbl_sessions"]." WHERE ";
        		foreach ($res as $key=>$value) {
      		    $query .= "id='".$value["id"]."'".((($key+1)!=count($res))?" OR ":"");
        		}
        		$this->db->Query($query);
    	    }
    	    //
      	}
    }
    //
}
?>

<?php
// Class for working with MySQL functions
class MySQLClient {
	var $dblink;
	var $res;
	var $database;
	function MySQLClient($host,$database,$user_name,$user_password) {
	    $this->database = $database;
	    $this->Connect($host,$database,$user_name,$user_password);
	    $this->Query("SET NAMES UTF8");
	}
	// Connecting to the database
	function Connect($host,$database,$user_name,$user_password) {
	    $this->dblink = mysqli_connect($host, $user_name, $user_password, $database);
	    return true;
	}
	// Closing database connection
	function Close() {
	    return mysqli_close($this->dblink);
	}
	// Execute SQL query
	function Query($query_string,$print_query=false) {
	    if ($print_query) {
      	print $query_string."<br>\n";
	    }
	    $this->res = mysqli_query($this->dblink, $query_string);
	    if ($this->res == false) { return false; }
	    return true;
	}
	function GetLastResult() {
	    if ($this->res!=false) {
  	    if (mysqli_num_rows($this->res)>0) {
      		$result = array(); $i=0;
      		while ($row = mysqli_fetch_assoc($this->res)) {
      		  $result[$i++] = $row;
      		}
      		return $result;
  	    }
	    }
	    return false;
	}
	function GetTablesList() {
	    $res = $this->Query("SHOW TABLES;");
	    $result = array();
	    $i=0;
	    while ($row = mysqli_fetch_row($this->res)) {
          $result[$i++]=$row[0];
	    }
	    return $result;
	}
	function TableExist($table_name) {
	    $mas = $this->GetTablesList();
	    if (in_array($table_name,$mas)) {
    		return true;
	    }
	    return false;
	}
	// Count the number of records, that are corresponding to the condition of select
	function RecordsCount($tbl_name,$query="") {
	    $tbl_name = mysqli_real_escape_string($this->dblink, $tbl_name);
	    if ($this->TableExist($tbl_name)) {
    		if ($this->Query("SELECT count(*) as rec_count FROM $tbl_name".((trim($query)!="")?" WHERE $query":""))) {
    		    $res = $this->GetLastResult();
  		    if (isset($res[0]["rec_count"])) {
      			return $res[0]["rec_count"];
  		    }
    		}
	    }
	    return 0;
	}
	// Return content from the table by user's request
	function GetRecords($tbl_name,$fields="*",$query="") {
	    $tbl_name = mysqli_real_escape_string($tbl_name);
	    $fields = mysqli_real_escape_string($fields);
	    if ($this->TableExist($tbl_name)) {
    		if ($this->Query("SELECT $fields FROM $tbl_name".((trim($query)!="")?" WHERE $query":""))) {
    		    $res = $this->GetLastResult();
    		    if (isset($res[0])) {
        			return $res;
    		    }
    		}
	    }
	    return false;
	}
}
?>

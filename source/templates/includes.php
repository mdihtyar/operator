<?php
$main_dir = __DIR__."/..";

// connecting main configuration
include_once "$main_dir/conf/main_conf.php";
include_once "common.php";

// configure sessions parameters
// sessions lifetime and their parameters
//ini_set('session.cookie_lifetime',CONST_DEFAULT_SESSION_LIFETIME);
//ini_set('session.gc_maxlifetime',CONST_DEFAULT_SESSION_LIFETIME);
ini_set('session.save_path',$_LSET["tmp_folder"]);

// connectiong language package
unset($dir); unset($file); unset($ext); unset($file_ext);
if ($dir = @opendir($_LSET["lang_dir"])) {
    $ext = "php";
    while (false !== ($file = readdir($dir))) {
        if (is_file($_LSET["lang_dir"]."/".$file)) {
            $file_ext = substr($file,strlen($file)-strlen($ext));
            if ($file_ext == $ext) {
                include_once $_LSET["lang_dir"]."/".$file;
            }
        }
    }
} else { exit("Error"); }

$lang = $lang[$_LSET["default_lang"]];
// creating of the additional array element - dublicates, that begin from upper char
foreach ($lang as $lang_key=>$lang_value) {
    $new_lang_key = mb_ucfirst($lang_key);
    $new_lang_value = mb_ucfirst($lang_value);
    if (!isset($lang[$new_lang_key])) {
        $lang[$new_lang_key] = $new_lang_value;
    }
}

//
// Including of all modules, that locate by path: $_LSET["modules_dir"]
unset($dir); unset($file); unset($ext); unset($file_ext);
if ($dir = @opendir($_LSET["modules_dir"])) {
    $ext = "php";
    while (false !== ($file = readdir($dir))) {
        if (is_file($_LSET["modules_dir"]."/".$file)) {
            $file_ext = substr($file,strlen($file)-strlen($ext));
            if ($file_ext == $ext) {
                include_once $_LSET["modules_dir"]."/".$file;
            }
        }
    }
} else { exit($lang["Error_loading_modules"]); }
//

?>

<?php
    //
    unset($_LSET); // змінна для збереження локальних параметрів
    //
    $_LSET["default_lang"] = "uk"; // локалізація
    // Зарезервовані слова підсистеми налаштувань
    $_LSET["w_users"] = "users"; // користувач

    // БД
    $_LSET["mysql_db"] = "operator"; // назва БД
    $_LSET["mysql_asterisk_db"] = "asterisk";
    // Параметри підключення до MySQL-серверу
    $_LSET["mysql_user"] = "operator";
    $_LSET["mysql_password"] = "jelaVeiqueevie2w";
    $_LSET["mysql_server"] = "localhost";
    //
    $_LSET["asterisk_server_address"] = "127.0.0.1";

    // Інші другорядні параметри
    $_LSET["records_on_page"] = 50;
    $_LSET["processed_call_records_on_page"] = 10;
    // Таблиці
    $_LSET["tbl_users"] = "tbl_users";
    $_LSET["tbl_sessions"] = "tbl_sessions";
    $_LSET["tbl_trunk_line"] = "tbl_trunk_line";
    $_LSET["tbl_call_logs"] = "tbl_call_logs";
    $_LSET["tbl_phone_book"] = "tbl_phone_book";
    //
    $_LSET["tbl_asterisk_cdr"] = "bit_cdr";

    // Директорії
    $_LSET["root_dir"] = $main_dir; // вказано кореневу директорію білінга
    $_LSET["modules_dir"] = $_LSET["root_dir"]."/modules";
    $_LSET["conf_dir"] = $_LSET["root_dir"]."/conf";
    $_LSET["lang_dir"] = $_LSET["conf_dir"]."/lang";
    $_LSET["templates_dir"] = $_LSET["root_dir"]."/templates";
    $_LSET["scripts_dir"] = $_LSET["conf_dir"]."/scripts";

    $_LSET["events_dir"] = $_LSET["root_dir"]."/events";
    $_LSET["tmp_folder"] = "/var/operator/tmp";
    $_LSET["admin_dir"] = $_LSET["root_dir"]."/admin";
    $_LSET["asterisk_call_records_dir"] = "/home/asterisk";
    $_LSET["save_call_records_period"] = 60*60*24*90; // період зберігання записів розмов, вказується в секундах // 90 днів за замовченням

    // WEB-директорії
    $_LSET["www_root_dir"] = ""; // вказано кореневий каталог веб для системи обліку

    $_LSET["www_images_dir"] = $_LSET["www_root_dir"]."/images"; // картинки
    $_LSET["www_js_dir"] = $_LSET["www_root_dir"]."/js"; // каталог з набором скриптів
    // картинки файли операцій
    $_LSET["www_img_active"] = $_LSET["www_images_dir"]."/active.png";
    $_LSET["www_img_disable"] = $_LSET["www_images_dir"]."/disable.png";
    $_LSET["www_img_disable_24"] = $_LSET["www_images_dir"]."/disable_24.png";
    $_LSET["www_img_delete"] = $_LSET["www_images_dir"]."/delete.png";
    $_LSET["www_img_edit"] = $_LSET["www_images_dir"]."/edit.png";
    $_LSET["www_img_info"] = $_LSET["www_images_dir"]."/info.png";
    $_LSET["www_img_add"] = $_LSET["www_images_dir"]."/add.png";
    $_LSET["www_img_apply"] = $_LSET["www_images_dir"]."/apply.png";
    $_LSET["www_img_answered"] = $_LSET["www_images_dir"]."/answered.png";
    //
    $_LSET["www_img_upload_file"] = $_LSET["www_images_dir"]."/file_upload.png";
    $_LSET["www_img_add_zone"] = $_LSET["www_images_dir"]."/add_zone.png";
    $_LSET["www_img_del_zone"] = $_LSET["www_images_dir"]."/del_zone.png";
    $_LSET["www_img_save"] = $_LSET["www_images_dir"]."/save.png";
    $_LSET["www_img_zone_done"] = $_LSET["www_images_dir"]."/zone_done.png";
    //
    $_LSET["www_img_uk_flat"] = $_LSET["www_images_dir"]."/flat_uk.png";
    $_LSET["www_img_ru_flat"] = $_LSET["www_images_dir"]."/flat_ru.png";
    $_LSET["www_img_usa_flat"] = $_LSET["www_images_dir"]."/flat_usa.png";
    //
    // Файли
    $_LSET["common_index"] = $_LSET["www_root_dir"]."/index.php"; // індексний файл загального доступу до системи
    //
    $_LSET["ptmp_admin"] = "admin";
    $_LSET["ptmp_show_calls_detailed"] = "calls_detailed";
    $_LSET["admin_index"] = $_LSET["common_index"]."?mode=".$_LSET["ptmp_admin"]."&submode=".$_LSET["ptmp_show_calls_detailed"]; // індексний файл адміністративної панелі системи

    //
    $_LSET["lang_file"] = $_LSET["conf_dir"]."/language.php";
    $_LSET["include_file"] = $_LSET["templates_dir"]."/includes.php"; // тут підключаються усі необхідні модулі та об"єкти
    $_LSET["css_file"] = $_LSET["www_root_dir"]."/main.css"; // веб-шлях до файлу з описом стилів
    $_LSET["js_line_status_file"] = $_LSET["www_js_dir"]."/line_status.js"; // скрипт обробки поточного стану ліній
    // Шаблони
    $_LSET["tmpl_main"] = $_LSET["templates_dir"]."/main.html"; // тут описано шлях до основного шаблону сайту
    $_LSET["tmlp_main_simple"] = $_LSET["templates_dir"]."/main_simple.html"; // звичайний інтерфейс, без примочок
    $_LSET["tmpl_login"] = $_LSET["templates_dir"]."/login.html";
    $_LSET["tmpl_main_window"] = $_LSET["templates_dir"]."/main_window.html";
    $_LSET["tmpl_show_calls_menu"] = $_LSET["templates_dir"]."/show_calls_menu.html";
    $_LSET["tmpl_call_info"] = $_LSET["templates_dir"]."/call_info.html";
    $_LSET["tmpl_calls_list"] = $_LSET["templates_dir"]."/calls_list.html";
    $_LSET["tmpl_phone_book"] = $_LSET["templates_dir"]."/phone_book.html";
    //
    $_LSET["session_max"] = 100; // вказуємо максимальну кількість зафіксованих сесій
    $_LSET["calling_sort_mode"] = "DESC"; // режим сортування інформації про дзвінки
    //
    //
    $_LSET["ptmp_logout"] = "logout";
    $_LSET["ptmp_show_call_logs"] = "show_call_logs";
    $_LSET["ptmp_all"] = "all";
    $_LSET["ptmp_unprocessed"] = "unprocessed";
    $_LSET["ptmp_new_call"] = "new_call";
    $_LSET["ptmp_call_processed"] = "call_processed";
    $_LSET["ptmp_get_call_record"] = "get_call_record";
    $_LSET["ptmp_play_call_record"] = "play_call_record";
    $_LSET["ptmp_get_line_status"] = "get_line_status";
    $_LSET["ptmp_set_line_status"] = "set_line_status";
    $_LSET["ptmp_call_processing"] = "call_processing";
    $_LSET["ptmp_call_tried"] = "call_tried";
    $_LSET["ptmp_delete_old_call_records"] = "delete_old_call_records";
    $_LSET["ptmp_clean_unprocessed_calls_list"] = "clean_unprocessed_calls_list";
    $_LSET["ptmp_phone_book"] = "phone_book";
    $_LSET["ptmp_add"] = "add";
    $_LSET["ptmp_del"] = "del";
    $_LSET["ptmp_show_calls_report"] = "show_calls_report";


    $_LSET["ptmp_processed"] = "processed";
    $_LSET["param_logout"] = "mode=".$_LSET["ptmp_logout"];

    // описуємо деякі номери телефонів, вказуємо, як їх позначати
    $_LSET["phone_desc"] = array("202"=>"Operator 1","203"=>"Operator 2","001"=>"MTS","002"=>"Kyivstar","003"=>"Life:)","004"=>"Kyivstar 2");
    $_LSET["operators"] = array("202","203");
    //
    define("CONST_MSG_ANSWER","msg_answer");
    define("CONST_FREE_LINE",0);
    define("CONST_INCOMING_CALL",1);
    define("CONST_BUSY_LINE",2);
    define("CONST_NEW_CALL",$_LSET["ptmp_new_call"]);
    define("CONST_CALL_PROCESSED",$_LSET["ptmp_call_processed"]);
    define("CONST_CALL_TRIED",$_LSET["ptmp_call_tried"]);
    define("CONST_NULL_DATE","0000-00-00 00:00:00"); // нульова дата
    define("CONST_SIMPLE_NUMBER_LENGTH",10); // 000 123 45 67
    define("CONST_POSSIBLE_NUMBER_LENGTH",13); // +38 000 123 45 67
    define("CONST_UA_CODE","+38");


    define("_IN",1);
    define("_OUT",2);
    define("_NULL",0);
    define("_DIAL_WAIT_TIME",9); // вказуємо скільки абонент чекатиме до привітання, або ж оператор йому
    //

?>

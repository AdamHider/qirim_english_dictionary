<?php

function init(){
    $list = getList();
    foreach ($list as &$item){
        $item = modify($item);
    }
    print_r($list);
    die;
}

function modify($item){
    $exploded_descr = explode(' ',$item['description']);
    foreach($exploded_descr as &$entry){
        if(strpos(mb_strtolower($entry), $item['word'])>-1){
            $entry = '[['.$entry.']]';
        }
    }
    $item['description'] = implode('|', $exploded_descr);
    return $item;
}

function getList(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT 
            *
        FROM
            qirim_english_dictionary.tmp_description_edit1
        LIMIT 10    
        ";
    return mysqli_fetch_all($mysqli->query($sql_2), MYSQLI_ASSOC);
}
init();
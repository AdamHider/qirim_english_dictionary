<?php

function init(){
    $list = getList();
    $final = [];
    set_time_limit(800000);
    foreach ($list as &$item){
        $item = modify($item);
        if(!isset($item['com_php'])){
            $final[] = $item;
        }
    }
    print_r($final);
    die;
}

function modify($item){
    $exploded_descr = explode(' ',$item['description']);
    foreach($exploded_descr as &$entry){
        $is_founded = strpos(mb_strtolower($entry), substr(mb_strtolower($item['word']), 0, 2));
        if( $is_founded === 0   && $is_founded !== false      
                ){
            $entry = '<b>'.$entry.'</b>';
            $item['com_php'] = 1;
        }
    }
    $item['description'] = implode(' ', $exploded_descr);
    update($item['description_id'], $item['language_id'], $item['description']);
    return $item;
}

function update($description_id, $language_id, $description){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        UPDATE
            qirim_english_dictionary.tmp_description_list_final
            SET template = '$description'
        WHERE 
            description_id = $description_id AND language_id = $language_id
        ";
    return $mysqli->query($sql_2);
}

function getList(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT 
            *
        FROM
            qirim_english_dictionary.tmp_description_edit1
        ";
    return mysqli_fetch_all($mysqli->query($sql_2), MYSQLI_ASSOC);
}
init();
<?php

function init(){
    $list = getList();
    $final = [];
    set_time_limit(800000);
    foreach ($list as &$item){
        $item = modify($item);
        update($item);
    }
    
}

function modify($item){
    $crimean = '';
    $circum = ['Крым', "река", "горы"];
    
    if(strpos($item['clarification'], 'Крым')>-1 || strpos($item['clarification'], 'посёл')>-1 ){
        $crimean = '-Crimea';
    } else 
    if(strpos($item['clarification'], 'река')>-1  ){
        $crimean = '-river';
    } else 
    if(strpos($item['clarification'], 'горы')>-1 ){
        $crimean = '-mountains';
    } 
    if($item['language_id'] == 1){
        $item['toponymy_link'] = 'https://www.google.com/maps/place/'.transliterate($item['referent_word']).$crimean.'/';
    } else {
        $item['toponymy_link'] = 'https://www.google.com/maps/place/'.transliterate($item['word']).$crimean.'/';
    }
    return $item;
}

function transliterate($word){
    $textcyr="Тествам с кирилица";
    $cyr = [
        'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п',
        'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
        'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П',
        'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
    ];
    $lat = [
        'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p',
        'r','s','t','u','f','h','ts','ch','sh','shy','a','i','y','e','yu','ya',
        'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P',
        'R','S','T','U','F','H','Ts','Ch','Sh','Shy','A','I','Y','e','Yu','Ya'
    ];
    $result = str_replace($cyr, $lat, $word);
    //$result = str_replace($lat, $cyr, $textlat);
    return $result;
}

function update($item){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        UPDATE
            diyar_db.new_word_list_edit1
            SET toponymy_link = '{$item['toponymy_link']}'
        WHERE 
            relation_id = {$item['relation_id']}
        ";
    return $mysqli->query($sql_2);
}

function getList(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT 
            *
        FROM
            diyar_db.new_word_list_edit1
        WHERE
            toponymy IS NOT NULL 
        ";
    return mysqli_fetch_all($mysqli->query($sql_2), MYSQLI_ASSOC);
}
init();
<?php

$mysqli;
 $result = [];
 $qirim_result = [];
 
function compileObject(){
    header('Content-type: text/plain');
    $html = file_get_contents('https://dictionary.cambridge.org/ru/%D1%81%D0%BB%D0%BE%D0%B2%D0%B0%D1%80%D1%8C/%D0%B0%D0%BD%D0%B3%D0%BB%D0%B8%D0%B9%D1%81%D0%BA%D0%B8%D0%B9/smoke');
    print_r($html);
    die;
    if(strpos($html, 'noresults')>-1){
        echo $word.": not_found! \n";
        return;
    } else {
        echo $word.": found and processed! \n";
    }
    $array = explode('<h3 class="definition">', $html);
    $start_array = [];
    unset($array[0]);
    foreach($array as &$key){
        $key = explode('<div class="defContent">', $key)[0];
        if(strpos($key, $part_of_speech)>-1 && count($start_array) < 15){
            $start_array[] = trim(strip_tags(explode('</a>',$key)[1]));
        }
    }
    prepareForDb($start_array, $eng_word_id);
}

function prepareForDb($array, $eng_word_id){
    if(empty($array)){
        return;
    }
    foreach($array as $value){
        if(!empty($value)){
            putDescription($eng_word_id, $value);
        }
    }
}

function putDescription($eng_word_id, $description){
    global $mysqli;
    $sql = "
        INSERT INTO
            qirim_english_dictionary.eng_definitions
        SET
            eng_word_id = '".$eng_word_id."',
            definition = '".$description."'
        ";
    $mysqli->query($sql);
}

function getList(){
    global $mysqli;
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
     $sql = "
        SELECT 
            ew.eng_word_id, ew.name, pos.eng_part_descr
        FROM
            qirim_english_dictionary.eng_words ew
                JOIN
            parts_of_speech pos USING (part_of_speech_id)
                LEFT JOIN
            eng_definitions def USING (eng_word_id)
        WHERE
            def.definition IS NULL
         LIMIT 5000
        ";
    return mysqli_fetch_all($mysqli->query($sql));
}

function init(){
    set_time_limit(800000);
    $list = getList();
    foreach($list as $item){
        $eng_word_id = $item[0];
        $word = $item[1];
        $part_of_speech = $item[2];
        compileObject($eng_word_id, $word, $part_of_speech);
    }
}
compileObject();
//init();
//getQTTranslation();
//gufoWordList();
//compareDifferences();
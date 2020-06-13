<?php
$mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
$mysqli->set_charset("utf8");


function init(){
    global $text_qt;
    global $text_rus;
    $exploded_qt = explode(' ', $text_qt);
    foreach($exploded_qt as &$word ){
        $word = prepareWord($word);
        print_r($word);
        die;
    }
}

function prepareWord($word){
    $word = str_replace('.', '', $word);
    $word = str_replace(',', '', $word);
    $word = str_replace('!', '', $word);
    $word = str_replace('?', '', $word);
    $word = mb_strtolower($word);
    return trim($word);
}

function findWord($word_string){
    global $mysqli;
     $sql = "
        SELECT  * FROM 
        (SELECT 
            wl.relation_id, 
            wl.word, 
            wl1.relation_id referent_relation_id,
            wl1.word referent_word
        FROM
            lgt_word_list wl
                JOIN
            lgt_word_list wl1 ON wl.denotation_id = wl1.denotation_id
                AND wl.language_id != wl1.language_id
        WHERE
            wl.word = '$word_string') t
        WHERE binary t.word = '$word_string'
        ";
    $result = mysqli_fetch_all($mysqli->query($sql));
    return $result;
}

$text_qt    = 'Saba erte turğan... endi şeerni dolanıp kelgen. Gazetanı közlerine pek yaqın tutıp, közlüksiz oqumaqta.';
$text_rus   = 'Встал рано и, обойдя весь город, уже вернулся. Газету читал без очков, правда, держа ее чересчур близко к глазам.';

init();
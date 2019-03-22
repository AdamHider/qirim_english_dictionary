<?php

function getList(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT rw.name as rus_name, ew.name as eng_name, eng_word_id, rus_word_id
        FROM qirim_english_dictionary.rus_words rw
        JOIN qirim_english_dictionary.`references` ref USING (rus_word_id)
        JOIN qirim_english_dictionary.eng_words ew USING (eng_word_id)
        WHERE rw.name LIKE '%,%'
        LIMIT 10
        ";
    echo json_encode(mysqli_fetch_all($mysqli->query($sql_2)));
}


if(function_exists($_GET['f'])) {
   $_GET['f']();
}
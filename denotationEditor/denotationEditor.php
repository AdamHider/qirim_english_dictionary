<?php

$mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
$mysqli->set_charset("utf8");

function getObjectByWord(){
    global $mysqli;
    $word = $_GET['word'];
    $sql = "
        SELECT 
            wl.word_id              query_word_id,
            wl.word                 query_word,
            wl.part_of_speech_id    query_part_of_speech_id,
            r1.relation_id          query_relation_id,
            r1.clarification        query_clarification,
            dl.denotation_id,
            dl.denotation_description,
            r2.relation_id          result_relation_id,
            
            
            r2.clarification        result_clarification,
            
            wl2.word_id             result_word_id,
            wl2.word                result_word,
            wl2.part_of_speech_id   result_part_of_speech_id
        FROM
            word_list wl
                JOIN
            relation_list r1 ON wl.word_id = r1.word_id 
                JOIN
            denotation_list dl ON dl.denotation_id = r1.denotation_id
                JOIN
            relation_list r2 ON dl.denotation_id = r2.denotation_id
                JOIN
            word_list wl2 ON wl2.word_id = r2.word_id
        WHERE
            wl.word = '$word'
                AND wl.language_id != wl2.language_id
            ORDER BY dl.denotation_id
    ";
    $result = json_encode(mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC));
     echo $result;
}

function fetchExistingDenotations(){
    global $mysqli;
    $denotation_description = $_GET['denotation_description'];
    $sql = "
        SELECT 
            denotation_id,
            denotation_description
        FROM
            denotation_list
        WHERE 
            denotation_description LIKE '%$denotation_description%'  
    ";
    $result = json_encode(mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC));
     echo $result;
}

function getDenotationByDescription(){
    global $mysqli;
    $denotation_description = $_GET['denotation_description'];
    $sql = "
        SELECT 
            denotation_id,
            denotation_description
        FROM
            denotation_list
        WHERE 
            denotation_description = '$denotation_description'  
    ";
    $result = json_encode(mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC));
     echo $result;
}

function checkIfRelationExists(){
    global $mysqli;
    $denotation_id = $_GET['denotation_id'];
    $word_id = $_GET['word_id'];
    $sql = "
        SELECT 
            relation_id
        FROM
            relation_list
        WHERE 
            denotation_id = '$denotation_id' 
        AND word_id = '$word_id' 
    ";
    $result = json_encode(mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC));
     echo $result;
}


function getLastIds (){
    global $mysqli;
    $sql = "
        SELECT 
            wl.word_id,
            (SELECT  relation_id FROM relation_list ORDER BY relation_id DESC LIMIT 1) AS last_relation,
            (SELECT  denotation_id FROM denotation_list ORDER BY denotation_id DESC LIMIT 1) AS last_denotation
        FROM
            word_list wl
		ORDER BY word_id DESC
        LIMIT 1 
            
    ";
    $result = json_encode(mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC));
     echo $result;
}

if(function_exists($_GET['f'])) {
   $_GET['f']();
}
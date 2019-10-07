<?php

$mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
$mysqli->set_charset("utf8");

function getObjectByWord(){
    $word = $_GET['word'];
    $result = getObjectByWordQuery($word);
    echo json_encode($result);
}

function getObjectByWordQuery($word){
    global $mysqli;
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
            word_list_tmp  wl
                JOIN
            relation_list_tmp  r1 ON wl.word_id = r1.word_id 
                JOIN
            denotation_list_tmp  dl ON dl.denotation_id = r1.denotation_id
                JOIN
            relation_list_tmp  r2 ON dl.denotation_id = r2.denotation_id
                JOIN
            word_list_tmp  wl2 ON wl2.word_id = r2.word_id
        WHERE
            wl.word = '$word'
                AND wl.language_id != wl2.language_id
            ORDER BY dl.denotation_id
    ";
    $result = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);
     return $result;
}

function apply(){
    $list = json_decode($_POST['list']);
    $word = $_POST['word'];
    $old_list = getObjectByWordQuery($word);
    if($list){
        analyzeList($list, $old_list);
    }
}

function analyzeList($new_list, $old_list){
    foreach($new_list as &$new_item){
        foreach($old_list as $old_item){
            if($new_item->denotation_description == ''){
                $new_item->denotation_description = null;
            }
            if($old_item['denotation_description'] !== $new_item->denotation_description){ 
                changeDenotation($new_item->denotation_description, $old_item);
            }
        }
    }
}

function changeDenotation($denotation_description, $old_item){
    $existing = fetchExistingDenotations($denotation_description);
    if(empty($existing)){
        
        $new_denotation_id = addDenotation($denotation_description, $old_item['query_part_of_speech_id']);
        addRelation($new_denotation_id, $old_item['query_relation_id'], $old_item);
        addRelation($new_denotation_id, $old_item['result_relation_id'], $old_item);
    }
}

function addDenotation($denotation_description, $part){
    global $mysqli;
    
    $sql = "
        INSERT INTO 
            denotation_list
        SET
            denotation_id = NULL, 
            denotation_number = NULL, 
            denotation_description = '$denotation_description', 
            part_of_speech_id = $part, 
            relevance = 1.000
    ";
    $mysqli->query($sql);
    return $mysqli->insert_id;
}

function addRelation($new_denotation_id, $old_relation_id, $old_item){
    echo $new_denotation_id;
    die;
}

function fetchExistingDenotations($denotation_description){
    global $mysqli;
    $where = " =  '$denotation_description'";
    if(isset($_GET['denotation_description'])){
        $denotation_description = $_GET['denotation_description'];
        $where = " LIKE '$denotation_description%'";
    } 
    $sql = "
        SELECT 
            denotation_id,
            denotation_description
        FROM
            denotation_list
        WHERE 
            denotation_description $where 
    ";
    if(isset($_GET['denotation_description'])){
        $result = json_encode(mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC));
        echo $result;
        return;
    }
    $result = mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);
    return $result;
    
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

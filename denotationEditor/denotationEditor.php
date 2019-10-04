<?php

$mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
$mysqli->set_charset("utf8");

function prepareWorkflow(){
    createTmpTables();
    echo 'ok';
}

function createTmpTables(){
    global $mysqli;
    $mysqli->query('CREATE TABLE word_list_tmp SELECT * from word_list');
    $mysqli->query('ALTER TABLE `diyar_db`.`word_list_tmp` 
                    CHANGE COLUMN `word_id` `word_id` INT(11) NOT NULL AUTO_INCREMENT ,
                    ADD PRIMARY KEY (`word_id`),
                    ADD UNIQUE INDEX `word_UNIQUE` (`language_id` ASC, `word` ASC, `part_of_speech_id` ASC),
                    ADD INDEX `word_id` (`word_id` ASC);');
    
    $mysqli->query('CREATE TABLE denotation_list_tmp SELECT * from denotation_list;');
    $mysqli->query('ALTER TABLE `diyar_db`.`denotation_list_tmp` 
                    CHANGE COLUMN `denotation_id` `denotation_id` INT(11) NOT NULL AUTO_INCREMENT ,
                    ADD PRIMARY KEY (`denotation_id`),
                    ADD INDEX `denotation_id` (`denotation_id` ASC);');
    
    $mysqli->query('CREATE TABLE relation_list_tmp SELECT * from relation_list;');
    $mysqli->query('ALTER TABLE `diyar_db`.`relation_list_tmp` 
                    CHANGE COLUMN `relation_id` `relation_id` INT(11) NOT NULL AUTO_INCREMENT ,
                    ADD PRIMARY KEY (`relation_id`),
                    ADD INDEX `word_id` (`word_id` ASC),
                    ADD INDEX `denotation_id` (`denotation_id` ASC);');
    
}

function addToHistory(){
    $list = $_POST['list'];
    $history_list = updateHistoryTable($list);
    
    $current_object = $history_list[0]['data'];
    if(isset($history_list[1])){
        $previous_object = $history_list[1]['data'];
    }
    updateTempTable(json_decode($current_object, true), json_decode($previous_object, true));
    
    echo json_encode($current_object);
}

function updateTempTable($list, $previous_object){
    checkUpdates($list, $previous_object);
}

function checkUpdates($list, $previous_object){
    //$word = $list[0]['query_word'];
    foreach($list as $index=>$row){
        foreach($row as $property => $value){
            if($value !== $previous_object[$index][$property]){
                if($property === 'denotation_description'){
                     echo 'denotation changed!';
                } else 
                if($property === 'query_word' || $property === 'query_part_of_speech_id'){
                    
                } else 
                if($property === 'result_word' || $property === 'result_part_of_speech_id'){
                    
                } else {
                    
                }    
               
            }
        }
    }
}

function updateHistoryTable($list){
    global $mysqli;
    $list = addslashes($list);
    $create_sql = "CREATE   TABLE diyar_db.history_tmp 
        (
            `id` INT NOT NULL AUTO_INCREMENT,
            `data` LONGTEXT NULL,
            PRIMARY KEY (`id`)

        )
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_bin;;
        ";
    $mysqli->query($create_sql);
     $sql = "
         INSERT INTO diyar_db.history_tmp
         SET id = NULL, data = '$list'
         ";
     $mysqli->query($sql);
    $result = mysqli_fetch_all($mysqli->query('SELECT data FROM diyar_db.history_tmp ORDER BY id DESC'), MYSQLI_ASSOC);
    return $result;
}

function getHistoryByIndex(){
    global $mysqli;
    $index = $_GET['index'];
    $result = json_encode(mysqli_fetch_all($mysqli->query("SELECT data FROM diyar_db.history_tmp WHERE id = $index"), MYSQLI_ASSOC)); 
    return $result;
}

function getObjectByWord(){
    $word = $_GET['word'];
    $result = getObjectByWordQuery($word);
    echo json_encode($result);
}

function getObjectByWordQuery($word){
    global $mysqli;
    $mysqli->query('TRUNCATE diyar_db.history_tmp');
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
            
            if(wl.language_id = 1, 
            r2.clarification, 
            r1.clarification)       result_clarification,
            
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



if(function_exists($_GET['f'])) {
   $_GET['f']();
}
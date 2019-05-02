<?php
$current_word_list = [];
$query = '';
$mysqli;
function getList(){
    global $query;
    global $mysqli;
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'HAVING ew.eng_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $rus_ids = getRusTotalIds();
    $sql_2 = "
        SELECT DISTINCT
            qw.name as qt_name,
            rw.name as rus_name,
            ew.name as eng_name,
            ew.eng_word_id as eng_word_id, 
            rw.rus_word_id as rus_word_id, 
            qw.qt_word_id as qt_word_id
        FROM
            qirim_english_dictionary.qt_words qw
                    JOIN
            references_rus_qt refrq ON (refrq.qt_word_id = qw.qt_word_id)
                    JOIN
            rus_words rw ON (rw.rus_word_id = refrq.rus_word_id)
                    JOIN
            references_eng_ru referu ON (referu.rus_word_id = rw.rus_word_id)
                    JOIN
            eng_words ew ON (ew.eng_word_id = referu.eng_word_id)
        WHERE 
            qw.status = 'warning' AND rw.rus_word_id = $rus_ids[0]
        ";
    echo json_encode(mysqli_fetch_all($mysqli->query($sql_2)));
}

function updateEngStatuses(){
    global $mysqli;
    $sql = "
        SELECT 
            (SELECT 
                    COUNT(rus.name)
                FROM
                    rus_words rus
                        JOIN
                    references_eng_ru refs USING (rus_word_id)
                        JOIN
                    eng_words eng USING (eng_word_id)
                WHERE
                    eng.name = ew.name) AS referents
        FROM
            qirim_english_dictionary.eng_words ew
        LIMIT 10          
        ";
    return mysqli_fetch_all($mysqli->query($sql))[0];
}

function getTotal(){
    global $query;
    global $mysqli;
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'HAVING ew.eng_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $sql_2 = "
        SELECT COUNT(*)
            FROM
                qirim_english_dictionary.qt_words qw
                    JOIN
                qirim_english_dictionary.`references` ref ON (ref.qt_word_id = qw.qt_word_id)
                    JOIN
                eng_words ew ON (ew.eng_word_id = ref.eng_word_id)
            WHERE qw.name LIKE '%$query%' OR qw.name LIKE '$query%' OR qw.name LIKE '%$query'
        ";
    echo json_encode(mysqli_fetch_row($mysqli->query($sql_2)));
}

function commit(){
    global $query;
    set_time_limit(500);
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    if(!empty($_GET['data'])){
        $data = json_decode($_GET['data']);
        for($i=0; $i<count($data); $i++){
            $object = [
                'qt_word' => $data[$i][0],
                'eng_word' => $data[$i][1],
                'eng_id' => $data[$i][2],
                'qt_id' => $data[$i][3],
            ];
            if(isset($data[$i][4])){
                putThatDone($object['eng_id']);
                continue;
            } else {
                updateRusName($object['qt_id'], $object['eng_id'], $object['qt_word']);
            }
        }
    }
}

function updateQtName($qt_id = '', $eng_id = '', $qt_word = ''){
    global $mysqli;
    if(!empty($_GET['newword'])){
        $data = explode(';',$_GET['newword']);
        $qt_word_id = $data[0];
        $new_word_name = $data[1];
        $eng_word_id = $data[2];
    } else {
        $qt_word_id = $qt_id;
        $new_word_name = editWord($qt_word);
        $eng_word_id = $eng_id;
    }
    $sql_4 = "
       UPDATE qirim_english_dictionary.qt_words 
       SET name = '".$new_word_name."'
       WHERE qt_word_id = '".$qt_word_id."'
       ";
    $mysqli->query($sql_4);
    $error = mysqli_error($mysqli);
    if(strpos($error, 'Duplicate entry')>-1){
          insertQuery($new_word_name,$eng_word_id);
          deleteQuery($qt_word_id, $eng_word_id);
    }
}

function insertQuery($qt_name, $eng_id){
    global $mysqli;
    $sql_5 = "
        SELECT qt_word_id FROM qirim_english_dictionary.qt_words 
        WHERE name = '".$qt_name."' LIMIT 1
        ";
    $qt_id = mysqli_fetch_all($mysqli->query($sql_5))[0][0];
    $sql_4 = "
        INSERT INTO  qirim_english_dictionary.`references`
        SET qt_word_id = '".$qt_id."', eng_word_id = '$eng_id'
        ";
    $mysqli->query($sql_4);
}


function deleteQtName(){
    global $mysqli;
    if(!empty($_GET['deleteid'])){
        $qt_id = $_GET['deleteid'];
        $sql_5 = "
            DELETE  FROM  qirim_english_dictionary.qt_words 
            WHERE qt_word_id = '".$qt_id."'
            ";
        $mysqli->query($sql_5);
        $sql_4 = "
            DELETE  FROM  qirim_english_dictionary.`references`
            WHERE qt_word_id = '".$qt_id."'
            ";
        $mysqli->query($sql_4);
    }
}

function explodeWord($object){
    global $mysqli;
    $exploded = explode(', ',$object['qt_word']);
    
    for($i = 0; $i<count($exploded); $i++){
        $sql_3 = "
            INSERT INTO qirim_english_dictionary.qt_words 
            SET name = '".$exploded[$i]."', part_of_speech_id = '".$object['part_of_speech_id']."'
            ";
        $mysqli->query($sql_3);
        $sql_2 = "
            SELECT 
                qt_word_id
            FROM
                qirim_english_dictionary.qt_words
            WHERE
                name = '".$exploded[$i]."'  AND part_of_speech_id = '".$object['part_of_speech_id']."'
        ";
        $new_qt_word_id = mysqli_fetch_row($mysqli->query($sql_2))[0];
        $sql_4 = "
            INSERT INTO qirim_english_dictionary.`references` 
            SET eng_word_id = '".$object['eng_id']."', qt_word_id = '".$new_qt_word_id."'
            ";
        $mysqli->query($sql_4);
    }
    deleteQuery($object['qt_id'], $object['eng_id']);
}


function deleteQuery($qt_id, $eng_id){
    global $mysqli;
    $sql_3 = "
        DELETE  FROM  qirim_english_dictionary.qt_words
        WHERE qt_word_id = '".$qt_id."'
        ";
    $mysqli->query($sql_3);
    $sql_4 = "
        DELETE  FROM  qirim_english_dictionary.`references`
        WHERE qt_word_id = '".$qt_id."' AND eng_word_id = '".$eng_id."'
        ";
    $mysqli->query($sql_4);
}

function editWord($word){
    global $query;
    $city = str_replace('г', "г.", $query);
    $new_word = trim(str_replace($query, $city, $word));
    return $new_word;
}

function putThatDone($data){
    $already_done = file_get_contents('done_commas.txt');
    if(!$already_done){
        file_put_contents('done_commas.txt', $data);
    } else {
        $already_done .= ','.$data;
        file_put_contents('done_commas.txt', $already_done);
    }
}

if(function_exists($_GET['f'])) {
   $_GET['f']();
}
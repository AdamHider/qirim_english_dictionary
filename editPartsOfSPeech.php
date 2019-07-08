<?php
$current_word_list = [];
$query = '';
function getList(){
    global $query;
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'HAVING rw.rus_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT 
            part_of_speech_id,
            name,
            '' as a,
            rus_word_id
        FROM
            qirim_english_dictionary.rus_words
        WHERE
            part_of_speech_id = '102' 
        ";
    echo json_encode(mysqli_fetch_all($mysqli->query($sql_2)));
}

function getTotal(){
    global $query;
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'HAVING rw.rus_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT COUNT(*)
            FROM
                qirim_english_dictionary.rus_words rw
                    LEFT JOIN
                qirim_english_dictionary.`references` ref ON (ref.rus_word_id = rw.rus_word_id)
                    LEFT JOIN
                eng_words ew ON (ew.eng_word_id = ref.eng_word_id)
            WHERE rw.rus_word_id > 160000
   
                        AND rw.name LIKE '%(%' $already_done
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
                'part_of_speech' => '1',
                'rus_name' => $data[$i][1],
                'eng_id' => $data[$i][2],
                'rus_id' => $data[$i][3],
            ];
            updateRusName($object['rus_id'],$object['part_of_speech'],$object['rus_name'] );
                continue;
        }
    }
}

function updateRusName($rus_id = '', $part_of_speech = '', $rus_name = ''){
    
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    if(!empty($_GET['newword'])){
        $data = explode(';',$_GET['newword']);
        $rus_word_id = $data[0];
        $rus_name = $data[1];
        $new_part_of_speech = $data[2];
    } else {
        $rus_word_id = $rus_id;
        $new_part_of_speech = $part_of_speech;
    }
    $sql_4 = "
       UPDATE qirim_english_dictionary.rus_words 
       SET 
        part_of_speech_id = '".$new_part_of_speech."'
       WHERE rus_word_id = '".$rus_word_id."'
       ";
    $mysqli->query($sql_4);
    $error = mysqli_error($mysqli);
    if(strpos($error, 'Duplicate entry')>-1){
          insertQuery($new_part_of_speech, $rus_word_id,$rus_name);
          //deleteQuery($rus_word_id);
    }
    $mysqli->close();
}

function insertQuery($new_part_of_speech, $old_rus_id,$rus_name){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    //$rus_name = mysqli_fetch_all($mysqli->query("SELECT name FROM rus_words WHERE rus_word_id = '$old_rus_id'"))[0][0];
    $sql_5 = "
        SELECT rus_word_id FROM qirim_english_dictionary.rus_words 
        WHERE name = '".$rus_name."' AND part_of_speech_id = '$new_part_of_speech'
        ";
    $real_rus_id = mysqli_fetch_all($mysqli->query($sql_5))[0][0];
    
    $mysqli->query("UPDATE  qirim_english_dictionary.rus_words 
        SET name = '".$rus_name."'
        WHERE  rus_word_id = '".$real_rus_id."'
        ");
    $sql_5 = "
            DELETE  FROM  qirim_english_dictionary.rus_words 
            WHERE rus_word_id = '".$old_rus_id."'
            ";
        $mysqli->query($sql_5);
    return;
    $mysqli->query("UPDATE  qirim_english_dictionary.rus_descriptions 
        SET rus_word_id = '$real_rus_id'
        WHERE rus_word_id = '".$old_rus_id."'
        ");
    $error = mysqli_error($mysqli);
    $mysqli->close();
}


function deleteRusName(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    if(!empty($_GET['deleteid'])){
        $rus_id = $_GET['deleteid'];
        $sql_5 = "
            DELETE  FROM  qirim_english_dictionary.rus_words 
            WHERE rus_word_id = '".$rus_id."'
            ";
        $mysqli->query($sql_5);
    }
    $mysqli->close();
}

function explodeWord($object){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $exploded = explode(', ',$object['rus_word']);
    
    for($i = 0; $i<count($exploded); $i++){
        $sql_3 = "
            INSERT INTO qirim_english_dictionary.rus_words 
            SET name = '".$exploded[$i]."', part_of_speech_id = '".$object['part_of_speech_id']."'
            ";
        $mysqli->query($sql_3);
        $sql_2 = "
            SELECT 
                rus_word_id
            FROM
                qirim_english_dictionary.rus_words
            WHERE
                name = '".$exploded[$i]."'  AND part_of_speech_id = '".$object['part_of_speech_id']."'
        ";
        $new_rus_word_id = mysqli_fetch_row($mysqli->query($sql_2))[0];
        $sql_4 = "
            INSERT INTO qirim_english_dictionary.`references` 
            SET eng_word_id = '".$object['eng_id']."', rus_word_id = '".$new_rus_word_id."'
            ";
        $mysqli->query($sql_4);
    }
    deleteQuery($object['rus_id'], $object['eng_id']);
    $mysqli->close();
}


function deleteQuery($rus_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_3 = "
        DELETE  FROM  qirim_english_dictionary.rus_words
        WHERE rus_word_id = '".$rus_id."'
        ";
    $mysqli->query($sql_3);
    $mysqli->close();
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
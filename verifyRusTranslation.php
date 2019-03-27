<?php
$current_word_list = [];
function getList(){
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'AND rw.rus_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT rw.name as rus_name, ew.name as eng_name, eng_word_id, rus_word_id, ew.part_of_speech_id
        FROM qirim_english_dictionary.rus_words rw
        JOIN qirim_english_dictionary.`references` ref USING (rus_word_id)
        JOIN qirim_english_dictionary.eng_words ew USING (eng_word_id)
        WHERE rw.name LIKE '%,%' $already_done
        LIMIT 10 OFFSET ".$_GET['offset']."
        ";
    echo json_encode(mysqli_fetch_all($mysqli->query($sql_2)));
}

function commit(){
    if(!empty($_GET['data'])){
        $data = json_decode($_GET['data']);
        for($i=0; $i<count($data); $i++){
            $object = [
                'rus_word' => $data[$i][0],
                'eng_word' => $data[$i][1],
                'eng_id' => $data[$i][2],
                'rus_id' => $data[$i][3],
                'part_of_speech_id' => $data[$i][4],
            ];
            if(isset($data[$i][5])){
                putThatDone($object['rus_id']);
            } else {
                explodeWord($object);
            }
        }
    }
}


function updateRusName(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    if(!empty($_GET['newword'])){
        $data = explode(';',$_GET['newword']);
        $word_id = $data[0];
        $word_name = $data[1];
         $sql_4 = "
            UPDATE qirim_english_dictionary.rus_words 
            SET name = '".$word_name."'
            WHERE rus_word_id = '".$word_id."'
            ";
        $mysqli->query($sql_4);
    }
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
        $sql_4 = "
            DELETE  FROM  qirim_english_dictionary.`references`
            WHERE rus_word_id = '".$rus_id."'
            ";
        $mysqli->query($sql_4);
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


function deleteQuery($rus_id, $eng_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_5 = "
        DELETE  FROM  qirim_english_dictionary.rus_words 
        WHERE rus_word_id = '".$rus_id."'
        ";
    $mysqli->query($sql_5);
    $sql_55 = "
        DELETE  FROM  qirim_english_dictionary.eng_words 
        WHERE rus_word_id = '".$eng_id."'
        ";
    $mysqli->query($sql_55);
    $sql_4 = "
        DELETE  FROM  qirim_english_dictionary.`references`
        WHERE rus_word_id = '".$rus_id."' AND eng_word_id = '".$eng_id."'
        ";
    $mysqli->query($sql_4);
    $mysqli->close();
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
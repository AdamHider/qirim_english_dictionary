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
        SELECT rw.name as rus_name, ew.name as eng_name, eng_word_id, rus_word_id, rw.part_of_speech_id
        FROM qirim_english_dictionary.rus_words rw
        JOIN qirim_english_dictionary.references_eng_ru ref USING (rus_word_id)
        JOIN qirim_english_dictionary.eng_words ew USING (eng_word_id)
        WHERE 
        rw.part_of_speech_id = '101' $already_done
        LIMIT 30  
        ";
    
    echo json_encode(mysqli_fetch_all($mysqli->query($sql_2)));
}

function getTotal(){
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'AND rw.rus_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT COUNT(*)
        FROM qirim_english_dictionary.rus_words rw
        JOIN qirim_english_dictionary.`references` ref USING (rus_word_id)
        JOIN qirim_english_dictionary.eng_words ew USING (eng_word_id)
        WHERE 
        rw.name LIKE '% чему-л.'
        AND rw.name not LIKE '% за %'
    AND rw.name not LIKE '% обо %'
    AND rw.name not LIKE '% на %'
    AND rw.name not LIKE '% под %' $already_done
        ";
    echo json_encode(mysqli_fetch_row($mysqli->query($sql_2)));
}

function commit(){
    if(!empty($_GET['data'])){
        $data = json_decode($_GET['data'],JSON_UNESCAPED_UNICODE);
        for($i=0; $i<count($data); $i++){
            $object = [
                'rus_word' => $data[$i][0],
                'eng_word' => $data[$i][1],
                'eng_id' => $data[$i][2],
                'rus_id' => $data[$i][3],
                'part_of_speech_id' => "2",
            ];
               if(isset($data[$i][5])){
                putThatDone($object['rus_id']);
            } else {
                explodeWord($object);
            }
        }
    }
}

function explodeWord($object){
    header('Content:text-plain');
    
    $rus_word = $object['rus_word'];
    
    updateRusName($object['rus_id'],$object['eng_id'],$rus_word,$object['part_of_speech_id']);
}
    
    

function updateRusName($rus_id = '', $eng_id = '', $rus_word = '',$part_of_speech_id = ''){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    if(!empty($_GET['newword'])){
        $data = explode(';',$_GET['newword']);
        $rus_word_id = $data[0];
        $new_word_name = trim($data[1]);
        $eng_word_id = $data[2];
        $part_of_speech_id = '2';
    } else {
        $rus_word_id = $rus_id;
        $new_word_name = trim($rus_word);
        $eng_word_id = $eng_id;
        $part_of_speech_id = "2";
        
    }
    
    $sql_4 = "
       UPDATE qirim_english_dictionary.rus_words 
       SET part_of_speech_id = '".$part_of_speech_id."'
       WHERE rus_word_id = '".$rus_word_id."' 
       ";
    $mysqli->query($sql_4);
    $error = mysqli_error($mysqli);
    if(strpos($error, 'Duplicate entry')>-1){
          insertQuery($new_word_name,$eng_word_id,$part_of_speech_id);
          deleteQuery($rus_word_id, $eng_word_id,$part_of_speech_id);
    }
    $mysqli->close();
}

function insertQuery($rus_name, $eng_id,$part_of_speech_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_5 = "
        SELECT rus_word_id FROM qirim_english_dictionary.rus_words 
        WHERE name = '".$rus_name."' AND part_of_speech_id ='$part_of_speech_id' 
        ";
    $rus_id = mysqli_fetch_all($mysqli->query($sql_5))[0][0];
    $sql_4 = "
        INSERT INTO  qirim_english_dictionary.references_eng_ru
        SET rus_word_id = '".$rus_id."', eng_word_id = '$eng_id'
        ";
    $mysqli->query($sql_4);
    
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
        $sql_4 = "
            DELETE  FROM  qirim_english_dictionary.references_eng_ru
            WHERE rus_word_id = '".$rus_id."'
            ";
        $mysqli->query($sql_4);
    }
    $mysqli->close();
}




function deleteQuery($rus_id, $eng_id,$part_of_speech_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_5 = "
        SELECT ew.eng_word_id FROM qirim_english_dictionary.rus_words rw
        JOIN references_eng_ru ref ON(ref.rus_word_id = rw.rus_word_id) 
        JOIN eng_words ew ON(ew.eng_word_id = ref.eng_word_id) 
        WHERE rw.rus_word_id = '".$rus_id."' AND rw.part_of_speech_id ='101' 
        ";
    $array = mysqli_fetch_all($mysqli->query($sql_5));
    $eng_id = $array[0][0];

    $sql_3 = "
        DELETE  FROM  qirim_english_dictionary.rus_words
        WHERE rus_word_id = '".$rus_id."' AND part_of_speech_id ='101' 
        ";
    $mysqli->query($sql_3);
    $sql_4 = "
        DELETE  FROM  qirim_english_dictionary.references_eng_ru
        WHERE rus_word_id = '".$rus_id."' AND eng_word_id = '".$eng_id."'
        ";
    $mysqli->query($sql_4);
    $mysqli->close();
}

function editWord($word){
    print_r($word);
    die;
    $new_word = trim(preg_replace('/\(.*\)/', '', $word));
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

<?php
$current_word_list = [];
$query = '';
$current_query = '';
function getList(){
    global $query;
    global $current_query;
    set_time_limit(30000);
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
    
    $current_query = '( —';
    
    $sql_2 = "
        SELECT 
            rw.rus_word_id,  ew.eng_word_id,  rw.name as rus_name
        FROM
            qirim_english_dictionary.rus_words rw
        JOIN
            references_eng_ru ref ON (ref.rus_word_id = rw.rus_word_id)
        JOIN
            eng_words ew ON (ew.eng_word_id = ref.eng_word_id)
        WHERE
             
    rw.name LIKE '%$current_query%' 
        ";
    $result =  mysqli_fetch_all($mysqli->query($sql_2));
    foreach($result as $item){
        updateRusName($item[0], $item[1],$item[2]);
    };
    print_r('success!');
    
}

function updateRusName($rus_id = '', $eng_id = '', $rus_word = ''){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    if(!empty($_GET['newword'])){
        $data = explode(';',$_GET['newword']);
        $rus_word_id = $data[0];
        $new_word_name = $data[1];
        $eng_word_id = $data[2];
    } else {
        $rus_word_id = $rus_id;
        $new_word_name = editWord($rus_word);
        $eng_word_id = $eng_id;
        
    }
    $sql_4 = "
       UPDATE qirim_english_dictionary.rus_words 
       SET name = '".$new_word_name."'
       WHERE rus_word_id = '".$rus_word_id."'
       ";
    $mysqli->query($sql_4);
    $error = mysqli_error($mysqli);
    if(strpos($error, 'Duplicate entry')>-1){
          insertQuery($new_word_name,$eng_word_id);
          deleteQuery($rus_word_id, $eng_word_id);
    }
    $mysqli->close();
}

function insertQuery($rus_name, $eng_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_5 = "
        SELECT rus_word_id FROM qirim_english_dictionary.rus_words 
        WHERE name = '".$rus_name."' LIMIT 1
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




function deleteQuery($rus_id, $eng_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_5 = "
        SELECT ew.eng_word_id FROM qirim_english_dictionary.rus_words rw
        JOIN references_eng_ru ref ON(ref.rus_word_id = rw.rus_word_id) 
        JOIN eng_words ew ON(ew.eng_word_id = ref.eng_word_id) 
        WHERE rw.rus_word_id = '".$rus_id."' LIMIT 1
        ";
    $array = mysqli_fetch_all($mysqli->query($sql_5));
    $eng_id = $array[0][0];

    $sql_3 = "
        DELETE  FROM  qirim_english_dictionary.rus_words
        WHERE rus_word_id = '".$rus_id."'
        ";
    $mysqli->query($sql_3);
    $sql_4 = "
        DELETE  FROM  qirim_english_dictionary.`references`
        WHERE rus_word_id = '".$rus_id."' AND eng_word_id = '".$eng_id."'
        ";
    $mysqli->query($sql_4);
    $mysqli->close();
}

function editWord($word){
    global $current_query;
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
 	
getList();



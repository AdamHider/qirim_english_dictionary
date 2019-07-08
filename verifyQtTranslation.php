<?php
$current_word_list = [];
$mysqli;

$count_empty = 0;
$count_comma = 0;
$count_dash = 0;
function getList(){
    global $mysqli;
    global $count_empty;
    global $count_comma;
    global $count_dash;
    set_time_limit(5000);
    
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'HAVING qw.qt_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $db_word_list = mysqli_fetch_all($mysqli->query("SELECT DISTINCT qw.name, qw.qt_word_id FROM qirim_english_dictionary.qt_words qw WHERE qw.name LIKE '% - %' "));
    $rus_word_list = [];
    /*
    foreach ($db_word_list as $word){
        $word_object = [];
        $word_object['word_id'] = $word[1];
        $word_object['word_name'] = $word[0];
        $word_object['word_name'] = checkWord($word_object);
        updateQtName($word_object['word_id'], $word_object['word_name']);
    }    
        /*
        $common_refs = mysqli_fetch_all($mysqli->query("SELECT ref.reference_id, rw.name FROM references_rus_qt ref JOIN rus_words rw USING (rus_word_id)WHERE ref.qt_word_id = '{$word[1]}'"));
        foreach ($common_refs as $ref){
            $reference = [];
            $reference['reference_id'] = $ref[0];
            $reference['referent_word_name'] = $ref[1];
            $word_object['references'][] = $reference;
        }
        checkWord($word_object);
    }*/
    echo json_encode($db_word_list);
}


function checkWord($word_object){
    global $mysqli;
    global $count_empty;
    global $count_comma;
    global $count_dash;
    $description = '';
    preg_match_all("/\([^\(\)]*\)+/", $word_object['word_name'], $matches);
    if(isset($matches[0])){
        $description = rtrim(ltrim(trim($matches[0][0]),'('),')');
        $word = trim(str_replace($matches[0][0], '', $word_object['word_name']));
    }
    $sql_4 = "
        INSERT INTO  qirim_english_dictionary.qt_descriptions
        SET qt_word_id = '".$word_object['word_id']."', 
            qt_description = '$description'
        ";
    $mysqli->query($sql_4);
    return $word;
    
    /*
    foreach($word_object['references'] as $reference){
        $ref_word = $reference['referent_word_name'];
        if(strpos($article[0],$ref_word) == -1 ){
            $count_empty +=1;
            setWarning($word_object, $reference, 'empty');
            continue;
        }
        preg_match_all("/(, ".$ref_word.")+|(".$ref_word.",)+/", $article[0], $matches);
        if(isset($matches[0])){
            $count_comma +=1;
            setWarning($word_object, $reference, 'comma');
            continue;
        }
        preg_match_all("/(-[ а-яА-Я]+ ".$ref_word.")+|(- ".$ref_word." [ а-яА-Я]+)+|([ а-яА-Я]+ ".$ref_word." -)+|(".$ref_word." [ а-яА-Я]+ -)+/", $article[0], $matches1);
        if(isset($matches1[0])){
            $count_dash +=1;
            setWarning($word_object, $reference, 'dash');
            continue;
        }
    }*/
}

function commit(){
    global $mysqli;
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    global $query;
    set_time_limit(500);
    if(!empty($_GET['data'])){
        $data = json_decode($_GET['data'], JSON_UNESCAPED_UNICODE);
        for($i=0; $i<count($data); $i++){
            $object = [
                'qt_word' => $data[$i][0],
                'qt_id' => $data[$i][1]
            ];
            $object['qt_word'] = checkWord($object);
            if(isset($data[$i][2])){
                putThatDone($object['qt_id']);
                continue;
            } else {
                updateQtName($object['qt_id'], $object['qt_word']);
            }
        }
    }
}

function updateQtName($qt_id = '', $qt_word = ''){
    global $mysqli;
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    if(!empty($_GET['newword'])){
        $data = explode(';',$_GET['newword']);
        $qt_word_id = $data[0];
        $new_word_name = $data[1];
        $eng_word_id = $data[2];
    } else {
        $qt_word_id = $qt_id;
        $new_word_name = editWord($qt_word);
    }
    $sql_4 = "
       UPDATE qirim_english_dictionary.qt_words 
       SET name = '".$new_word_name."'
       WHERE qt_word_id = '".$qt_word_id."'
       ";
    $mysqli->query($sql_4);
    $error = mysqli_error($mysqli);
    if(strpos($error, 'Duplicate entry')>-1){
          insertQuery($qt_word_id,$new_word_name);
          deleteQuery($qt_word_id);
    }
}

function insertQuery($qt_word_id,$qt_name){
    global $mysqli;
    $sql_5 = "
        SELECT qt_word_id FROM qirim_english_dictionary.qt_words 
        WHERE name = '".$qt_name."' LIMIT 1
        ";
    $real_qt_id = mysqli_fetch_all($mysqli->query($sql_5))[0][0];
    $sql_4 = "
        UPDATE IGNORE  qirim_english_dictionary.references_rus_qt
        SET qt_word_id = '".$real_qt_id."' WHERE qt_word_id = '$qt_word_id'
        ";
    $mysqli->query($sql_4);
}



function deleteQuery($qt_id){
    global $mysqli;
    $sql_3 = "
        DELETE  FROM  qirim_english_dictionary.qt_words
        WHERE qt_word_id = '".$qt_id."'
        ";
    $mysqli->query($sql_3);
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


function editWord($word){
    global $query;
    //$city = str_replace('г', "г.", $query);
    $new_word = trim($word);
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
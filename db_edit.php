<?php

function getList(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT
            rw.rus_word_id, rw.name, ew.eng_word_id, ew.name
        FROM
            qirim_english_dictionary.rus_words rw
                JOIN
            `references` ref ON (ref.rus_word_id = rw.rus_word_id)
                JOIN
            eng_words ew ON (ew.eng_word_id = ref.eng_word_id)
        WHERE rw.name LIKE 'а'
        ";
    $query = mysqli_fetch_all($mysqli->query($sql_2));
    $result = [];
    $obj = [];
    $errors = [];
    $delete_errors = [];
    foreach ($query as $row){
        $obj = [
            'rus_id' => $row[0],
            'rus_name' => editRow($row[1]),
            'eng_id' =>  $row[2],
            'eng_iname' =>  $row[3]
        ];
        if($obj['eng_id'] != '14309' || $obj['eng_id'] != '54532' || $obj['eng_id'] != '109136'){
            deleteQuery($obj['rus_id'], $obj['eng_id']);
            array_push($result, $obj);
        }
        /*
        if($obj['rus_name'] != ''){
          $sql_3 = "
                UPDATE  qirim_english_dictionary.rus_words 
                SET name = '".$obj['rus_name']."'
                WHERE rus_word_id = '".$obj['rus_id']."';
                ";
            $query1 = $mysqli->query($sql_3);
            $error = mysqli_error($mysqli);
            if(strpos($error, 'Duplicate entry')>-1){
               insertQuery($obj['rus_name'],$obj['eng_id']);
            }
            if($query1===false){
                array_push($errors, $query1.$obj['rus_id'].$error);
            }
        } else {
          deleteQuery($obj['rus_id']);
        }*/
    }
     $mysqli->close();
    print_r($result);
}

function editRow($row_name){
    //$row_name = preg_replace('/{.*}/', '', $row_name);
    //$row_name = preg_replace('/и пр/', '', $row_name);
    if(strpos($row_name, ')')>-1){
        if(!strpos($row_name, '(')){
            $row_name = '';
        }
    }
    $row_name = str_replace('≅', '', $row_name);
    if(strlen(trim($row_name))<3){
        $row_name = '';
    }
    $row_name = trim($row_name);
    return trim($row_name);
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
        INSERT INTO  qirim_english_dictionary.`references`
        SET rus_word_id = '".$rus_id."', eng_word_id = '$eng_id'
        ";
    $query3 = $mysqli->query($sql_4);
    $error = mysqli_error($mysqli);
    $mysqli->close();
    print_r($error);
}

function deleteQuery($rus_id, $eng_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    /*$sql_5 = "
        DELETE  FROM  qirim_english_dictionary.rus_words 
        WHERE rus_word_id = '".$id."'
        ";
    $query2 = $mysqli->query($sql_5);*/
    $sql_4 = "
        DELETE  FROM  qirim_english_dictionary.`references`
        WHERE rus_word_id = '".$rus_id."' AND eng_word_id = '$eng_id'
        ";
    $query3 = $mysqli->query($sql_4);
    $error = mysqli_error($mysqli);
    $mysqli->close();
    print_r($error);
}

function compileObject(){
    set_time_limit(800000);
    $xml = file_get_contents('abbr.xdxf');
    
    $xml_object = explode("<ar>", $xml);
    unset($xml_object[0]);
    
    $result_object = [];
    $word_data = [];
    $count = 0;
    $entry_object = [];
    $origins = [];
    foreach ($xml_object as $object){
        $count = $count + 1;
        $origin = explode('</k>',$object);
        if(strpos($origin[1], '</tr>')){
            $transcription_and_translation  = explode('</tr>',$origin[1]);
            $word_data['transcription']= str_replace('<tr>', '', $transcription_and_translation[0]);
            $word_data['translation']= str_replace('</ar>', '', $transcription_and_translation[1]);
        } else {
            $word_data['transcription'] = '';
            $word_data['translation']= str_replace('</ar>', '', $origin[1]);
        }
        
        $word_data['origin']= str_replace('<k>', '', $origin[0]);
        $word_data['origin']= str_replace('_', '', $origin[0]);
        $entry_object[$word_data['origin']] = $word_data['translation'];
        $word_data['translation'] = preg_replace('/[A-Za-z]*/', '', $word_data['translation']);
        $word_data['translation'] = rtrim(preg_replace('/^\s+/', '', $word_data['translation']));
        echo "'".$word_data['origin']."' => '".$count."',</br>";
        
        }
   //print_r($entry_object);
}
getList();

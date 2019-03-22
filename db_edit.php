<?php

function getList(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT * 
        FROM qirim_english_dictionary.rus_words 
        WHERE name LIKE '%:';
        ";
    $query = mysqli_fetch_all($mysqli->query($sql_2));
    $result = [];
    $obj = [];
    $errors = [];
    $delete_errors = [];
    foreach ($query as $row){
        $obj = [
            'id' => $row[0],
            'name' => editRow($row[1])
        ];
        if($obj['name'] != ''){
            array_push($result, $obj);
        }
        deleteQuery($obj['id']);
        /*
           if($obj['name'] != ''){
          $sql_3 = "
                UPDATE  qirim_english_dictionary.rus_words 
                SET name = '".$obj['name']."'
                WHERE rus_word_id = '".$obj['id']."';
                ";
            $query = $mysqli->query($sql_3);
            $error = mysqli_error($mysqli);
            if(strpos($error, 'Duplicate entry')>-1){
               deleteQuery($obj['id']);
            }
            if($query===false){
                array_push($errors, $query.$obj['id'].$error);
            }
        } else {
          deleteQuery($obj['id']);
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
    if(strlen(trim($row_name))<3){
        $row_name = '';
    }
    $row_name = trim($row_name);
    return trim($row_name);
}


function deleteQuery($id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_5 = "
        DELETE  FROM  qirim_english_dictionary.rus_words 
        WHERE rus_word_id = '".$id."'
        ";
    $query2 = $mysqli->query($sql_5);
    $sql_4 = "
        DELETE  FROM  qirim_english_dictionary.`references`
        WHERE rus_word_id = '".$id."'
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

<?php

function getList(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT * 
        FROM qirim_english_dictionary.rus_words 
        WHERE name LIKE ' %';
        ";
    $query = mysqli_fetch_all($mysqli->query($sql_2));
    $result = [];
    $obj = [];
    foreach ($query as $row){
        $obj = [
            'id' => $row[0],
            'name' => editRow($row[1])
        ];
        array_push($result, $obj);
        
    }
     $mysqli->close();
    print_r($result);
}

function editRow($row_name){
    $row_name = preg_replace('/_[\S]*\./', '', $row_name);
    $row_name = preg_replace('/[0-9a-zA-Z]+/', '', $row_name);
    return trim($row_name);
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

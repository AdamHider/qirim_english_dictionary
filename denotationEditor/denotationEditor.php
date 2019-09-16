<?php

$mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
$mysqli->set_charset("utf8");
function init(){
    $object = composeObject(getObjectByWord('открыть'));
    
}   

function composeObject($relations){
    $final_object = [
        'word_id' => '',
        'word' => '',
        'part_of_speech_id' => ''
    ];
    $final_relation_object = [
        'relation_id' => '',
        'word_id' => '',
        'word' => '',
        'word_part_of_speech_id' => '',
        'clarification' => '',
        'dialectality' => '',
        'scope_of_use' => '',
        'expressivity' => '',
        'stylistic_status' => '',
        'etymology_lang' => '',
        'etymology_word' => '',
        'modernity' => '',
    ];
    $result_object = [
        'result_word_id' => '',
        'result_word' => '',
        'result_part_of_speech_id' => '',
        'denotations' => []
    ];
    $current_part_of_speech_id = '';
    foreach($relations as $relation){
        $final_object['word_id'] = $relation['word_query_word_id'];
        $final_object['word'] = $relation['word_query_word'];
             
        $final_relation_object['word_id'] = $relation['word_result_word_id'];
        $final_relation_object['word'] = $relation['word_result_word'];
        $final_relation_object['word_part_of_speech'] = $relation['word_result_part_of_speech'];
        $final_relation_object['relation_id'] = $relation['relation_result_relation_id'];
        $final_relation_object['clarification'] = $relation['relation_result_clarification'];
        $final_relation_object['dialectality'] = $relation['relation_result_dialectality'];
        $final_relation_object['scope_of_use'] = $relation['relation_result_scope_of_use'];
        $final_relation_object['expressivity'] = $relation['relation_result_expressivity'];
        $final_relation_object['stylistic_status'] = $relation['relation_result_stylistic_status'];
        $final_relation_object['etymology_lang'] = $relation['relation_result_etymology_lang'];
        $final_relation_object['etymology_word'] = $relation['relation_result_etymology_word'];
        
        
        if($relation['word_result_part_of_speech'] != $current_part_of_speech_id){
            $current_part_of_speech_id = $relation['word_result_part_of_speech'];
        } 
        $final_object['translations'][$current_part_of_speech_id][] = $final_relation_object;
       
    }  
        print_r($final_object);
        die;
}

function getObjectByWord($word){
    global $mysqli;
    $sql = "
        SELECT DISTINCT
            wl.word_id              word_query_word_id,
            wl.word                 word_query_word,
            wl.part_of_speech_id    word_query_part_of_speech_id,
            r1.relation_id          relation_query_relation_id,
            r1.clarification        relation_query_clarification,
            r1.dialectality         relation_query_dialectality,
            r1.scope_of_use         relation_query_scope_of_use,
            r1.expressivity         relation_query_expressivity,
            r1.stylistic_status     relation_query_stylistic_status,
            r1.etymology_lang       relation_query_etymology_lang,
            r1.etymology_word       relation_query_etymology_word,
            r1.modernity            relation_query_modernity,
            dl.denotation_id,
            dl.denotation_description,
            dl.part_of_speech_id    denotation_part_of_speech_id,
            r2.relation_id          relation_result_relation_id,
            r2.clarification        relation_result_clarification,
            r2.dialectality         relation_result_dialectality,
            r2.scope_of_use         relation_result_scope_of_use,
            r2.expressivity         relation_result_expressivity,
            r2.stylistic_status     relation_result_stylistic_status,
            r2.etymology_lang       relation_result_etymology_lang,
            r2.etymology_word       relation_result_etymology_word,
            r2.modernity            relation_result_modernity,
            wl2.word_id             word_result_word_id,
            wl2.word                word_result_word,
            wl2.part_of_speech_id   word_result_part_of_speech_id,
            pts.eng_part_descr      word_result_part_of_speech
        FROM
            qirim_english_dictionary.word_list wl
                JOIN
            relations_list r1 ON wl.word_id = r1.word_id 
                JOIN
            denotations_list dl ON dl.denotation_id = r1.denotation_id
                JOIN
            relations_list r2 ON dl.denotation_id = r2.denotation_id
                JOIN
            word_list wl2 ON wl2.word_id = r2.word_id
                JOIN
            parts_of_speech pts ON wl2.part_of_speech_id = pts.part_of_speech_id
        WHERE
            wl.word = '$word'
                AND wl.language_id != wl2.language_id
    ";
    return mysqli_fetch_all($mysqli->query($sql), MYSQLI_ASSOC);
}



init();
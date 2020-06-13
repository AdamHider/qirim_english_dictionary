<?php

function init(){
    set_time_limit(20000);
    header('Content-Type: text/html; charset=UTF-8');
    $list = getList();
    foreach($list as $item){
        /*
        $explode_one = explode('-', $item[1]);
        $transcription = '';
        
        $explode_one[count($explode_one)-1] = str_replace('|', '', $explode_one[count($explode_one)-1]);
        $explode_one[count($explode_one)-4] = '|'.$explode_one[count($explode_one)-4];
        if(count($explode_one)<2){
            continue;
        }
        /*
        foreach($explode_one as $exploded){
            $transcription .= $exploded;
        }*/
        //$transcription = transcription_transcribe($item[1]);
        //$transcription = implode('-',$explode_one);
        
        //putToDb1($item[0], $item[1]);
        getObject($item[0], $item[1]);
    }
    die;
}


function getList(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT 
            wl.word_id, wl.referent_word_id
        FROM
            lgt_word_list wl
        WHERE
             wl.language_id = 1 
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));
}


function getList1(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT 
            wl.new_word_id, wl.word_id
        FROM
            word_ids_recomposed wl
        WHERE
            new_word_id < 25461       
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));
}
function getList2(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
     $sql_2 = "
        SELECT 
            w.new_word_id, m.word_id
        FROM
            diyar_db.lgt_morpheme_list5 m
                JOIN
            word_ids_recomposed w ON m.word_id = w.word_id       
        LIMIT 10
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));
}

function getObject($word_id, $referent_word_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        INSERT INTO lgt_new_relations
        SELECT
               NULL as relation_id, 
               m.morpheme_id, 
               m.word_id, 
               m.morph_word, 
               m.language_id, 
               m.morph_transcription, 
               wl.relevance_experimental, 
               m.part_of_speech, 
               wl.clarification, 
               wl.toponymy, 
               wl.toponymy_link, 
               
               m1.morpheme_id as referent_morpheme_id, 
               m1.word_id as referent_word_id, 
               m1.morph_word as referent_morph_word, 
               m1.language_id as referent_language_id, 
               m1.morph_transcription as referent_morph_transcription, 
               wl1.relevance_experimental as referent_relevance_experimental, 
               m1.part_of_speech as referent_part_of_speech, 
               wl1.clarification as referent_clarification, 
               wl1.toponymy as referent_toponymy, 
               wl1.toponymy_link as referent_toponymy_link
            FROM
                lgt_morpheme_list5 m
            JOIN
                lgt_morpheme_list5 m1
                ON (m.activeness <=> m1.activeness OR m.activeness = 'all')
                AND (m.superlativeness <=> m1.superlativeness OR m.superlativeness = 'all')
                AND (m.casuality <=> m1.casuality OR m.casuality = 'all')
                AND (m.plurality <=> m1.plurality OR m.plurality = 'all')
                AND (m.person <=> m1.person OR m.person <=> 'all')
                AND (m.mood <=> m1.mood OR m.mood = 'all')
                AND (m.tense <=> m1.tense OR m.tense = 'all')
                AND (m.perfectness <=> m1.perfectness OR m.perfectness = 'all')
                AND (m.possession_person <=> m1.possession_person)
                AND (m.possession_plurality <=> m1.possession_plurality)
                AND m.part_of_speech = m1.part_of_speech
                AND m.is_negative <=> m1.is_negative
                AND m1.word_id = $referent_word_id
            
            JOIN
				lgt_word_list wl ON wl.word_id = m.word_id
            JOIN
				lgt_word_list wl1 ON wl1.word_id = m1.word_id
            WHERE
                m.word_id = $word_id
                GROUP BY m.morpheme_id, m1.morpheme_id
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));
}

function putToDb($old_word_id, $new_word_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
    $sql = "
        UPDATE
            diyar_db.lgt_new_relations m
            
            SET m.word_id = $new_word_id
        WHERE        
            word_id = $old_word_id
        ";
    $mysqli->query($sql);
}
function putToDb1($new_word_id, $word_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
    $sql = "
        UPDATE
            diyar_db.lgt_morpheme_list5 m
            
            SET m.new_word_id = $new_word_id
        WHERE        
            word_id = $word_id
        ";
    $mysqli->query($sql);
}
init();
        
       

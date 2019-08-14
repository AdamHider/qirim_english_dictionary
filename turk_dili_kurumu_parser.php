<?php

$mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
$mysqli->set_charset("utf8");

    


function init(){
    set_time_limit(800);
    $list = getList();
    foreach($list as $word){
        getWord($word[1]);
    }
}



function getList(){
    global $mysqli;
    $sql = "
        SELECT 
            * 
        FROM 
            qirim_english_dictionary.tmp_final 
        WHERE 
            word LIKE 'a%' AND etymology IS NULL
            LIMIT 500
        
        ";
    return mysqli_fetch_all($mysqli->query($sql));
}

function getWord($word){
    $query = [];
    if(strpos($word, 'q') > -1){
        $query[] = str_replace('q', 'k', $word);
    }
    $query[] = $word;
    
    if(strpos($word, 'a') == 0)  $query[] = 'h'.$word;
    if(strpos($word, 'u') == 0)  $query[] = 'h'.$word;
    if(strpos($word, 'aa') > -1) $query[] = str_replace('aa', 'aha', $word);
    if(strpos($word, 'ee') > -1) $query[] = str_replace('ehe', 'ehe', $word);
              
    foreach($query as $query_item){
        usleep(500000);
        $json = file_get_contents('http://sozluk.gov.tr/gts?ara='.$query_item);
        $suggests = json_decode($json);
        if(isset($suggests->error)){
            continue;
        }
        $etymology = [];
        foreach($suggests as $suggest){
            if($suggest->madde === $query_item){
                if(empty($etymology)){
                    if($suggest->lisan != ''){
                       $etymology[] = $suggest->lisan;
                    } else {
                        $etymology[] = 'original';
                    }
                    updateEtymology($word, $etymology[0]);
                } else {
                    updateEtymology($word, 'multiple');
                }
                return;
            }
        }
    }
    updateEtymology($word, 'unset');
}

function updateEtymology($word, $value){
    global $mysqli;
    $sql = "
        UPDATE
            qirim_english_dictionary.tmp_final
        SET
            etymology = '$value'
        WHERE word = '$word'     
        ";
    
    $mysqli->query($sql);       
}


init();
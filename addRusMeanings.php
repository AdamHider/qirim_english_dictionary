<?php

$mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
$mysqli->set_charset("utf8");

$previous_word = '';

function index(){
    set_time_limit(480000);
    $list = getList();
    foreach($list as $item){
        usleep(100000);
        $word = $item['word'];
        $data = getData($word);
        if($data){
            insert($data, $item['word_id']);
        } else {
            insert(false, $item['word_id']);
        }
    }
    
    die;
}

function getData($word, $denotation_number_in = null, $current_transcription_in = null){ 
    global $previous_word;
    if( mb_strtolower($word) === mb_strtolower($previous_word) ){
        return false;
    }
    $word = mb_strtolower($word);
    $firstChar = mb_strtoupper(mb_substr($word, 0, 1, "UTF-8"));
    $html = @file_get_contents("https://gufo.me/dict/kuznetsov/$word");
    if(!$html){
        return false;
    }
    $start = strpos($html, '<br class="visible-print-block">');
    $finish_length = strlen(substr($html, strpos($html, '<div class="fb-quote"></div>')));
    $length = strlen($html) - $finish_length - $start;
    $needed_str = substr($html, $start, $length);
    $needed_array = explode('<p>', str_replace('<em style="color:green">//</em>', '<p>', $needed_str));
    unset($needed_array[0]);
    $current_transcription = '';
        
    $result = [];
    foreach($needed_array as $k => &$item){
        $exceptions = ['что.','что-л.','кого.','кого-л.','что.' ];
        foreach($exceptions as $ex){
            $item = str_replace($ex, str_replace('.','', $ex), $item);
        }
        $item_exploded = explode('. <em>',$item);
        $item = $item_exploded[0];
        unset($item_exploded[0]);
        
        
        $examples = explode('.</em>', str_replace(')</em>', ').</em>', (implode('</em>', $item_exploded))));
        $pattern = '/ [А-Я]{1}/u';
        preg_match_all($pattern, $item, $matches);
        preg_match_all('/<strong>([А-Я< \/ u >])+<\/strong>/', $item, $transcription);
        preg_match_all('/ см. /', $item, $referent); 
        
            
        if(isset($transcription[0][0])){
            if(!$current_transcription_in){
                $current_transcription = strip_tags(str_replace('<u>', "'", $transcription[0][0]));
            }else {
                $current_transcription = $current_transcription_in;
            }
        }
        if(isset($referent[0][0])){
            $pattern = '/ см. /u';
            $tmp = preg_split($pattern, $item);
           
            $exploded = explode('. ', trim(trim(strip_tags($tmp[1])),'.')); 
            if(!isset($exploded[1])){
                $new_word = $exploded[0];
                $den_number = null;
            } else {
                $new_word = $exploded[1];
                $den_number = $exploded[0];
            }
            
            if( mb_strtolower($word) === mb_strtolower($previous_word) || mb_strtolower($previous_word) === mb_strtolower($new_word) ){
                return false;
            }
            
            $previous_word = $word;
            $getData = getData($new_word, $den_number, $current_transcription);
            
            if(!$getData){
                $result;
            } else {
                $result = mergeArrays($result, $getData); 
            }
            continue;
        }
        
        
           
        if(isset($matches[0][0])){
            $tmp = preg_split($pattern, $item, 2);
          
            $tmp[1] = $matches[0][0].$tmp[1];
            $denotation_number = strip_tags(explode('.', $tmp[0])[0]);
            if(isset(explode('</strong>',$tmp[0])[1])){
                $transition = trim(strip_tags(explode('</strong>',$tmp[0])[1]));
            }
            $denotation = trim(strip_tags(trim($tmp[1])),'.');
            preg_match_all('/[0-9]\.'.$firstChar.'\./', $denotation, $referent);
            if(isset($referent[0][0])){
                preg_match_all('/[0-9]/', $denotation, $denotation_numbers );
                $exploded = explode(' (', trim(trim(strip_tags($denotation)),'.'));
                $result = mergeArrays($result, getData($exploded[0], $denotation_numbers[0][0], $current_transcription)); 
                continue;
            }
            if( $denotation_number ){
                if($denotation_number_in && $denotation_number_in != $denotation_number){
                    continue;
                }
                if( !is_numeric($denotation_number) ){
                        $denotation_number = $k ;
                }
                if(strpos($denotation,', -')>-1){
                    continue;
                }
                if(1){
                    $result[] = [
                        'current_transcription' => $current_transcription,
                        'denotation_number' => $k,
                        'transition' => $transition,
                        'denotation' => $denotation,
                        'examples' => editExamples($examples, $firstChar, $word)
                    ];
                }
            } 
        }
        
        
    }
    
               
    return $result;
}

function editExamples($example_array, $first_letter, $word){
    $lower = mb_strtolower($first_letter);
    $upper = mb_strtoupper($first_letter);
    $first_letter_for_pattern = $lower.$upper;
    
      
    $pattern = "/ [$first_letter_for_pattern]{1} | [$first_letter_for_pattern]{1}$|^[$first_letter_for_pattern]{1} |[$first_letter_for_pattern]\.{1}/u";
    foreach($example_array as $k => &$example){
        preg_match_all($pattern, $example, $matches );
        if(isset($matches[0][0])){
            $example= str_replace($matches[0][0], ' '.$word.' ', $example);
        }
        $example = str_replace('  ', ' ', $example);
        $example = trim(strip_tags($example));
        if(mb_substr($example, 0, 1) === '(' && $k != 0){
            $example_array[$k-1] .= $example;
            $example = '';
        }
        if(empty($example) || $example == ' '){
            unset($example_array[$k]);
        }
    }
    return $example_array;
}

function mergeArrays($array1, $array2){
    foreach ($array2 as $item2){
        $item2['denotation_number'] = count($array1);
        $array1[] = $item2;
    }
    return $array1;
}

function getList(){
    global $mysqli;
    $sql_2 = "
        SELECT
            word_id, word
        FROM
            qirim_english_dictionary.word_list
        WHERE lang_id = 2
        GROUP BY word
        LIMIT 443 OFFSET 3557
        ";
    return mysqli_fetch_all($mysqli->query($sql_2), MYSQLI_ASSOC);
}

function insert($data, $word_id){
    if(!$data){
        insertReference('denotation_word_reference', 'word_id', $word_id, 'denotation_id', null);
        return;
    }
    foreach ($data as $item){  
        $denotation_id = insertDenotation($item);
        foreach($item['examples'] as $example){
            $example_id = insertExample($example);
            insertReference('denotation_example_reference', 'example_id', $example_id, 'denotation_id',$denotation_id);
        }
        insertReference('denotation_word_reference', 'word_id', $word_id, 'denotation_id', $denotation_id);
    }
}


function insertDenotation($data){
    global $mysqli;
    $sql = "
        INSERT INTO  qirim_english_dictionary.denotation_list_tmp
        SET 
        denotation_number = '".addslashes($data['denotation_number'])."', 
        denotation_word_transcription = '".addslashes($data['current_transcription'])."', 
        denotation_transition = '".addslashes($data['transition'])."', 
        denotation_description = '".addslashes($data['denotation'])."'
        ";
    $mysqli->query($sql);
    return $mysqli->insert_id;
}

function insertExample($example){
    global $mysqli;
    $sql = "
        INSERT INTO  qirim_english_dictionary.denotation_examples
        SET 
        example_description = '".addslashes($example)."'
        ";
    $mysqli->query($sql);
    return $mysqli->insert_id;
}

function insertReference($tableName, $column1, $id1, $column2, $id2){
    global $mysqli;
    $sql = "
        INSERT INTO  qirim_english_dictionary.$tableName
        SET 
        $column1 = '".$id1."', 
        $column2 = '".$id2."'
        ";
    return $mysqli->query($sql);
}




index();
<?php

$transcription_qt_alphabet = [
    'a' => ['type' => 'vowel', 'normal' => 'a', 'voice'=> 'hard'],
    'b' => ['type' => 'consonant', 'normal' => 'b'],
    'c' => ['type' => 'consonant', 'normal' => 'dʒ'],
    'ç' => ['type' => 'consonant', 'normal' => 'ʧ'],
    'd' => ['type' => 'consonant', 'normal' => 'd'],
    'e' => ['type' => 'vowel', 'normal' => 'ɛ', 'voice'=> 'soft'],
    'f' => ['type' => 'consonant', 'normal' => 'f'],
    'g' => ['type' => 'consonant', 'normal' => 'g'],
    'ğ' => ['type' => 'consonant', 'normal' => 'ʁ'],
    'h' => ['type' => 'consonant', 'normal' => 'x'],
    'ı' => ['type' => 'vowel', 'normal' => 'ɯ', 'voice'=> 'hard'],
    'i' => ['type' => 'vowel', 'normal' => 'ɪ', 'voice'=> 'soft'],
    'j' => ['type' => 'consonant', 'normal' => 'ʐ'],
    'k' => ['type' => 'consonant', 'normal' => 'k'],
    'l' => ['type' => 'consonant', 'normal' => 'l', 'special' => 'lʲ'],
    'm' => ['type' => 'consonant', 'normal' => 'm'],
    'n' => ['type' => 'consonant', 'normal' => 'n'],
    'ñ' => ['type' => 'consonant', 'normal' => 'ŋ'],
    'o' => ['type' => 'vowel', 'normal' => 'o', 'voice'=> 'hard'],
    'ö' => ['type' => 'vowel', 'normal' => 'ø', 'voice'=> 'soft'],
    'p' => ['type' => 'consonant', 'normal' => 'p'],
    'q' => ['type' => 'consonant', 'normal' => 'q'],
    'r' => ['type' => 'consonant', 'normal' => 'r'],
    's' => ['type' => 'consonant', 'normal' => 's'],
    'ş' => ['type' => 'consonant', 'normal' => 'ʂ', 'special' => 'ʃ'],
    't' => ['type' => 'consonant', 'normal' => 't'],
    'u' => ['type' => 'vowel', 'normal' => 'u', 'voice'=> 'hard'],
    'ü' => ['type' => 'vowel', 'normal' => 'y', 'voice'=> 'soft'],
    'v' => ['type' => 'consonant', 'normal' => 'v'],
    'y' => ['type' => 'consonant', 'normal' => 'j'],
    'z' => ['type' => 'consonant', 'normal' => 'z'],
    'â' => ['type' => 'vowel', 'normal' => 'ʲa', 'voice'=> 'soft'],
    '-' => ['type' => 'consonant', 'normal' => '-'],
    '|' => ['type' => 'consonant', 'normal' => '|']
];
$patterns = [
    'consonant-vowel',
    'consonant-vowel-consonant',
    'vowel-consonant',
];
function transcription_init(){
    set_time_limit(20000);
    return;
    header('Content-Type: text/html; charset=UTF-8');
    $list = transcription_getList();
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
        if($transcription){
            transcription_putToDb($transcription, $item[0]);
            print_r('The word with id '.$item[0].' now has a transcription: '.$transcription);
            echo '</br>';
        }
    }
    die;
}

function transcription_transcribe($word_string){
    global $transcription_qt_alphabet;
    $output = '';
    if(mb_strpos($word_string, ' ')>-1){
        $words = explode(' ', $word_string);
    } else {
        $words = [$word_string];
    }
    foreach($words as $word){
        if(strpos($word, 'В°')>-1){
            $word=str_replace('В°', '', $word);
        }
        $word_array = transcription_str_split_unicode($word, 1);
        array_unshift($word_array, '[');
        $word_array[] = ']';
        
        $chunks = implode('-',transcription_getChunks($word_array));
        $chunks_array = transcription_str_split_unicode($chunks, 1);
        foreach($chunks_array as $letter){
            if( $letter == '!'){
                continue;
            }
            if($letter == '-' || $letter == '|' ){
                $output .= $letter;
                continue;
            }
            $output .= $letter;
            //$output .= $transcription_qt_alphabet[mb_strtolower($letter)]['normal'];
        }
        $output .= ' ';
    }
    return trim($output);
}

function transcription_getList(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT 
            wl.word_id, LCASE(wl.transcription) AS name
        FROM
            diyar_db.new_word_list_edit wl
                JOIN
            parts_of_speech USING (part_of_speech_id)
                        LEFT JOIN 
                new_attribute_to_relation ar USING (relation_id)
        WHERE
        wl.language_id = 1
            AND wl.transcription IS NOT NULL
          AND wl.word LIKE '%cesine' AND wl.part_of_speech_id != 1
        GROUP BY word, part_of_speech_id
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));
}

function transcription_putToDb($transcription, $word_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
    $sql = "
        UPDATE 
            diyar_db.new_word_list_edit
        SET
            transcription = '".addslashes($transcription)."'
        WHERE        
            word_id = '$word_id'  
        ";
    
    $mysqli->query($sql);
}

function transcription_getChunks($word_array){
    global $transcription_qt_alphabet;
    $chunk_array = [];
    $new_chunk = '';
    $transcription_qt_alphabet = [
    'a' => ['type' => 'vowel', 'normal' => 'a', 'voice'=> 'hard'],
    'b' => ['type' => 'consonant', 'normal' => 'b'],
    'c' => ['type' => 'consonant', 'normal' => 'dʒ'],
    'ç' => ['type' => 'consonant', 'normal' => 'ʧ'],
    'd' => ['type' => 'consonant', 'normal' => 'd'],
    'e' => ['type' => 'vowel', 'normal' => 'ɛ', 'voice'=> 'soft'],
    'f' => ['type' => 'consonant', 'normal' => 'f'],
    'g' => ['type' => 'consonant', 'normal' => 'g'],
    'ğ' => ['type' => 'consonant', 'normal' => 'ʁ'],
    'h' => ['type' => 'consonant', 'normal' => 'x'],
    'ı' => ['type' => 'vowel', 'normal' => 'ɯ', 'voice'=> 'hard'],
    'i' => ['type' => 'vowel', 'normal' => 'ɪ', 'voice'=> 'soft'],
    'j' => ['type' => 'consonant', 'normal' => 'ʐ'],
    'k' => ['type' => 'consonant', 'normal' => 'k'],
    'l' => ['type' => 'consonant', 'normal' => 'l', 'special' => 'lʲ'],
    'm' => ['type' => 'consonant', 'normal' => 'm'],
    'n' => ['type' => 'consonant', 'normal' => 'n'],
    'ñ' => ['type' => 'consonant', 'normal' => 'ŋ'],
    'o' => ['type' => 'vowel', 'normal' => 'o', 'voice'=> 'hard'],
    'ö' => ['type' => 'vowel', 'normal' => 'ø', 'voice'=> 'soft'],
    'p' => ['type' => 'consonant', 'normal' => 'p'],
    'q' => ['type' => 'consonant', 'normal' => 'q'],
    'r' => ['type' => 'consonant', 'normal' => 'r'],
    's' => ['type' => 'consonant', 'normal' => 's'],
    'ş' => ['type' => 'consonant', 'normal' => 'ʂ', 'special' => 'ʃ'],
    't' => ['type' => 'consonant', 'normal' => 't'],
    'u' => ['type' => 'vowel', 'normal' => 'u', 'voice'=> 'hard'],
    'ü' => ['type' => 'vowel', 'normal' => 'y', 'voice'=> 'soft'],
    'v' => ['type' => 'consonant', 'normal' => 'v'],
    'y' => ['type' => 'consonant', 'normal' => 'j'],
    'z' => ['type' => 'consonant', 'normal' => 'z'],
    'â' => ['type' => 'vowel', 'normal' => 'ʲa', 'voice'=> 'soft'],
    '-' => ['type' => 'consonant', 'normal' => '-'],
    '|' => ['type' => 'vowel', 'normal' => '|']
];
$patterns = [
    'consonant-vowel',
    'consonant-vowel-consonant',
    'vowel-consonant',
];
$stress_index = array_search('|', $word_array);
    for($i = 1; $i < count($word_array); $i++){
        if($word_array[$i] == ']'){
            continue;
        } 
        if($word_array[$i] == '|'){
            $new_chunk = '|'.$new_chunk;
            continue;
        }
        $prev_letter = '';
        $prev_type = '';
        if($word_array[$i-1] != '['){
            $prev_letter = $transcription_qt_alphabet[mb_strtolower($word_array[$i-1])]['normal'];
            $prev_type = $transcription_qt_alphabet[mb_strtolower($word_array[$i-1])]['type'];
        }
        $curr_letter = $transcription_qt_alphabet[mb_strtolower($word_array[$i])]['normal'];
        $curr_type = $transcription_qt_alphabet[mb_strtolower($word_array[$i])]['type'];
        
        $next_letter = '';
        $next_type = '';
        if($word_array[$i+1] != ']'){
            $next_letter = $transcription_qt_alphabet[mb_strtolower($word_array[$i+1])]['normal'];
            $next_type = $transcription_qt_alphabet[mb_strtolower($word_array[$i+1])]['type'];
        } 
        $ultra_next_letter = '';
        $ultra_next_type = '';
        if(isset($word_array[$i+2]) && $word_array[$i+2] != ']'){
            $ultra_next_letter = $transcription_qt_alphabet[mb_strtolower($word_array[$i+2])]['normal'];
            $ultra_next_type = $transcription_qt_alphabet[mb_strtolower($word_array[$i+2])]['type'];
        }
        
        if( $curr_type == 'consonant' && !empty($transcription_qt_alphabet[mb_strtolower($word_array[$i])]['special'])){
            
            if($next_type == 'vowel' || $prev_type == 'vowel' ){
                if( isset($transcription_qt_alphabet[mb_strtolower($word_array[$i-1])]['voice']) 
                        && $transcription_qt_alphabet[mb_strtolower($word_array[$i-1])]['voice'] == 'soft' 
                        || 
                    isset($transcription_qt_alphabet[mb_strtolower($word_array[$i+1])]['voice'])  
                        &&  $transcription_qt_alphabet[mb_strtolower($word_array[$i+1])]['voice'] == 'soft'
                    ){
                    if($next_letter === 'КІa'){
                        $new_chunk .= $transcription_qt_alphabet[mb_strtolower($word_array[$i])]['normal'];
                
                    }else{
                        $new_chunk .= $transcription_qt_alphabet[mb_strtolower($word_array[$i])]['special'];
                    }
                } else {
                    $new_chunk .= $transcription_qt_alphabet[mb_strtolower($word_array[$i])]['normal'];
                }
            } else {
                $new_chunk .= $transcription_qt_alphabet[mb_strtolower($word_array[$i])]['special'];
            }
        }
        else {
            $new_chunk .= $transcription_qt_alphabet[mb_strtolower($word_array[$i])]['normal'];
           
        }
        
        if($curr_type == 'vowel'){
            if($next_type == 'consonant' && $ultra_next_type == 'vowel'){
                $chunk_array[] = $new_chunk;
                $new_chunk='';
                continue;
            } else 
            if($next_type == 'vowel' && $ultra_next_type == 'consonant'){
                $chunk_array[] = $new_chunk;
                $new_chunk='';
                continue;
            } else 
            if($prev_type == ''){
                if($next_type == 'consonant' && $ultra_next_type == 'vowel'){
                    $chunk_array[] = $new_chunk;
                    $new_chunk='';
                continue;
                } else 
                if($next_type == 'vowel' && $ultra_next_type == 'consonant'){
                    $chunk_array[] = $new_chunk;
                    $new_chunk='';
                    continue;
                }
            }
        } else 
        if($curr_type == 'consonant'){
            if($next_type == 'consonant' && $ultra_next_type == 'vowel' && $prev_type != ''){
                $chunk_array[] = $new_chunk;
                $new_chunk='';
                continue;
            } 
        }
        if($next_letter == '' ){
            //print_r($chunk_array[count($chunk_array)-2]);
            //$chunk_array[count($chunk_array)-1] = '|'.$chunk_array[count($chunk_array)-1];
            if(mb_strpos(implode('',$word_array), '|') === false){
                $new_chunk = '|'.$new_chunk;
            }
            //$new_chunk = '|'.$new_chunk;
            $chunk_array[] = $new_chunk;
            break;
        }
    }
    return $chunk_array;
    
}
function transcription_str_split_unicode($str, $l = 0) {
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}

        
       

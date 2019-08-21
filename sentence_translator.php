<?php


$mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
$mysqli->set_charset("utf8");
$qt_alphabet = [
    'a' => ['type' => 'vowel', 'normal' => 'a'],
    'b' => ['type' => 'consonant', 'normal' => 'b'],
    'c' => ['type' => 'consonant', 'normal' => 'c'],
    'ç' => ['type' => 'consonant', 'normal' => 'ç'],
    'd' => ['type' => 'consonant', 'normal' => 'd'],
    'e' => ['type' => 'vowel', 'normal' => 'e'],
    'f' => ['type' => 'consonant', 'normal' => 'f'],
    'g' => ['type' => 'consonant', 'normal' => 'g'],
    'ğ' => ['type' => 'consonant', 'normal' => 'ğ'],
    'h' => ['type' => 'consonant', 'normal' => 'h'],
    'ı' => ['type' => 'vowel', 'normal' => 'ı'],
    'i' => ['type' => 'vowel', 'normal' => 'i' ],
    'İ' => ['type' => 'vowel', 'normal' => 'i' ],
    'j' => ['type' => 'consonant', 'normal' => 'j'],
    'k' => ['type' => 'consonant', 'normal' => 'k'],
    'l' => ['type' => 'consonant', 'normal' => 'l'],
    'm' => ['type' => 'consonant', 'normal' => 'm'],
    'n' => ['type' => 'consonant', 'normal' => 'n'],
    'ñ' => ['type' => 'consonant', 'normal' => 'ñ'],
    'o' => ['type' => 'vowel', 'normal' => 'o'],
    'ö' => ['type' => 'vowel', 'normal' => 'ö'],
    'p' => ['type' => 'consonant', 'normal' => 'p'],
    'q' => ['type' => 'consonant', 'normal' => 'q'],
    'r' => ['type' => 'consonant', 'normal' => 'r'],
    's' => ['type' => 'consonant', 'normal' => 's'],
    'ş' => ['type' => 'consonant', 'normal' => 'ş'],
    't' => ['type' => 'consonant', 'normal' => 't'],
    'u' => ['type' => 'vowel', 'normal' => 'u'],
    'ü' => ['type' => 'vowel', 'normal' => 'ü'],
    'v' => ['type' => 'consonant', 'normal' => 'v'],
    'y' => ['type' => 'consonant', 'normal' => 'y'],
    'z' => ['type' => 'consonant', 'normal' => 'z'],
    'â' => ['type' => 'vowel', 'normal' => 'â']
];

$qt_words = [
    'aldatmaq' => 'обманывать',
    'baba' => 'отец',
    'da' => 'тоже',
    'cemaat' => 'народ'
];


function init(){
    $sentence = 'Şurasını da bilmelidir ki, ükümet bu arzularına
qolaylıqle nail olmaq içün, iptida Qırım ağalarınıñ, bikeleriniñ ağızına bal qaptırdı, mırzalarımıza — "dvorânlıq" unvanını vererek, kendilerine onar-on beşer köy ve qaçar biñ de sotnâ topraq
isse köstermiştir. Binaenaleyh köylerde yaşayan köylüleri de mırzalara esir itmiştir ki, bu esiret alâ Qırımnıñ bazı yerlerinde devam itmektedir!';
    $sentence = mb_strtolower($sentence);
    $sentence = str_replace(array("\n", "\r"), '', $sentence);
    $translated = [];
    $skip = ['«', '»', '!', '?', '.', ',', ';', ':', '-'];
    foreach($skip as $item){
        if((int)$item || strpos($sentence, $item)>-1){
            $sentence = str_replace($item, ' '.$item.' ',$sentence);
            continue;
        }
    }
    $wordsOfSentence = explode(' ', $sentence);
    foreach($wordsOfSentence as &$word){
        foreach($skip as $item){
            if((int)$word || strpos($word, $item)>-1){   
                 //$translated[][$word] = $word;
                 $translated[] = $word;
                 continue (2);
            }
        }
        $chunks = getChunks(trim($word));
        $translated_word = findWord(implode('', $chunks));
        if(!$translated_word){
            $translated_word = morphologyNormalize($chunks);
            if($translated_word){
                $translated[] = $translated_word[0];
                //$translated[][$word] = '( '.implode('|',$translated_word).' )';
            } else {
                //$translated[][$word] = $word;
                $translated[] = $word;
            }
        } else {
            $translated[] = $translated_word[0];
            //$translated[][$word] = '( '.implode('|',$translated_word).' )';
        }
    }
    print_r($translated);
    
}

//morphologyNormalize(['et','ke','niñ','ni']);
//die;
function morphologyNormalize($word_chunks){
    $translated_word = findWord(implode('', $word_chunks));
    if($translated_word){
        return $translated_word;
    }
    if(is_array($word_chunks) &&  count($word_chunks)>0 && !$translated_word){
        $last_chunk = array_pop($word_chunks); 
        
        
        if(strpos(implode('',array_merge($word_chunks,[$last_chunk])), 'ilmesine')>-1 && !strpos($last_chunk, 'mek')){
            $word_chunks = [str_replace('ilmesine','mek',implode('',array_merge($word_chunks,[$last_chunk])))];
            return morphologyNormalize($word_chunks);
        }
        if(strpos(implode('',array_merge($word_chunks,[$last_chunk])), 'ılmasına')>-1 && !strpos($last_chunk, 'maq')){
            $word_chunks = [str_replace('ılmasına','maq',implode('',array_merge($word_chunks,[$last_chunk])))];
            return morphologyNormalize($word_chunks);
        }
        
        if($last_chunk == 'qan' || $last_chunk == 'ğan'){
            array_push($word_chunks, 'maq');
        } else if($last_chunk == 'ken' || $last_chunk == 'gen'){
            array_push($word_chunks, 'mek');
        }  
     
        if(strpos($last_chunk, 'gin') >-1  && !strpos($last_chunk, 'k')){
            array_push($word_chunks, str_replace('gin','k',$last_chunk));
        }
        if(strpos($last_chunk, 'gi') >-1  && !strpos($last_chunk, 'k')){
            array_push($word_chunks, str_replace('gi','k',$last_chunk));
        }
        if(strpos($last_chunk, 'ge') >-1  && !strpos($last_chunk, 'k')){
            array_push($word_chunks, str_replace('ge','k',$last_chunk));
        }
        if(strpos($last_chunk, 'ğın') >-1  && !strpos($last_chunk, 'q')){
            array_push($word_chunks, str_replace('ğın','q',$last_chunk));
        }
        if(strpos($last_chunk, 'ğı') >-1  && !strpos($last_chunk, 'q')){
            array_push($word_chunks, str_replace('ğı','q',$last_chunk));
        }
        if(strpos($last_chunk, 'ğa') >-1  && !strpos($last_chunk, 'q')){
            array_push($word_chunks, str_replace('ğa','q',$last_chunk));
        }
        
        
        $maq = ['tı','dı', 'yıp', 'ıp','sañ', 'san',  'sıñ', 'ñız', 'ma'];
        foreach($maq as $ad){
            if(strpos($last_chunk, $ad)> -1 && strpos($last_chunk, 'maq') < 0 ){
                array_push($word_chunks, str_replace($ad,'maq',$last_chunk));
                return morphologyNormalize($word_chunks);
            }
        }
        $mek = ['di','ti', 'yip', 'ip','señ', 'sen', 'siñ', 'ñiz', 'me'];
        foreach($mek as $ad){
            if(strpos($last_chunk, $ad)>-1 && strpos($last_chunk, 'mek') === false ){
                array_push($word_chunks, str_replace($ad,'mek',$last_chunk));
                return morphologyNormalize($word_chunks);
            }
        }
        $arr = ['ıñıznı', 'iñizni','ıñnı', 'iñni','lı','li','si','sı','in','ın','iñ', 'ıñ','ni','nı','i','ı', 'u','ü'];
        foreach($arr as $ad){
            if(strpos($last_chunk, $ad)>-1){
                $new_chunk = str_replace($ad,'',$last_chunk);
                if($new_chunk != ''){
                    array_push($word_chunks, $new_chunk);
                } else {
                    return morphologyNormalize($word_chunks);
                }
                $word_chunks = getChunks(implode('',$word_chunks));
                return morphologyNormalize($word_chunks);
            }
        }
        
       
        
        if(strpos($last_chunk, 'may') >-1  && strpos($last_chunk, 'maq') == -1){
            array_push($word_chunks, str_replace('may','maq',$last_chunk));
        }
        if(strpos($last_chunk, 'ay') >-1  && strpos($last_chunk, 'maq') == -1){
            array_push($word_chunks, str_replace('may','maq',$last_chunk));
        }
        if(strpos($last_chunk, 'ay') >-1  && strpos($last_chunk, 'maq') == -1){
            array_push($word_chunks, str_replace('y','maq',$last_chunk));
        }
        if(strpos($last_chunk, 'sa') >-1  && strpos($last_chunk, 'maq') == -1){
            array_push($word_chunks, str_replace('sa','maq',$last_chunk));
        }
        if(substr($last_chunk, -1) === 'a' && strpos($last_chunk, 'maq') == -1){
            array_push($word_chunks, str_replace('a','maq',$last_chunk));
        }
        
        if(substr($last_chunk, -1) === 'u' && strpos($last_chunk, 'maq')== -1){
            array_push($word_chunks, str_replace('u','maq',$last_chunk));
        }
        
       
        
        if(strpos($last_chunk, 'mey') >-1  && strpos($last_chunk, 'mek') == -1){
            array_push($word_chunks, str_replace('mey','mek',$last_chunk));
        }
        if(strpos($last_chunk, 'ey') >-1  && strpos($last_chunk, 'mek') == -1){
            array_push($word_chunks, str_replace('y','mek',$last_chunk));
        }
        if(strpos($last_chunk, 'ey') >-1  && strpos($last_chunk, 'mek') == -1){
            array_push($word_chunks, str_replace('ey','mek',$last_chunk));
        }
        if(substr($last_chunk, -1) === 'e' && strpos($last_chunk, 'mek') == -1){
            array_push($word_chunks, str_replace('e','mek',$last_chunk));
        }
        
        
        
        
        return morphologyNormalize($word_chunks);
    }else {
        return false;
    }
}
function getChunks($word_string){
    global $qt_alphabet;
    $word_string = '['.$word_string;
    $word_string .= ']';
    $word_array = str_split_unicode($word_string, 1);
    $chunk_array = [];
    $new_chunk = '';
    
    for($i = 1; $i < count($word_array); $i++){
        if($word_array[$i] == ']'){
            continue;
        }  
        $prev_letter = '';
        $prev_type = '';
        if($word_array[$i-1] != '['){
            $prev_letter = $qt_alphabet[strtolower($word_array[$i-1])]['normal'];
            $prev_type = $qt_alphabet[strtolower($word_array[$i-1])]['type'];
        }
        $curr_letter = $qt_alphabet[strtolower($word_array[$i])]['normal'];
        $curr_type = $qt_alphabet[strtolower($word_array[$i])]['type'];
        
               
        $next_letter = '';
        $next_type = '';
        if($word_array[$i+1] != ']'){
            $next_letter = $qt_alphabet[strtolower($word_array[$i+1])]['normal'];
            $next_type = $qt_alphabet[strtolower($word_array[$i+1])]['type'];
        } 
        $ultra_next_letter = '';
        $ultra_next_type = '';
        if(isset($word_array[$i+2]) && $word_array[$i+2] != ']'){
            $ultra_next_letter = $qt_alphabet[strtolower($word_array[$i+2])]['normal'];
            $ultra_next_type = $qt_alphabet[strtolower($word_array[$i+2])]['type'];
        }
        $new_chunk .= $word_array[$i];
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
            $chunk_array[] = $new_chunk;
            break;
        }
    }
    
    return $chunk_array;
}

function findWord($word_string){
    print_r($word_string);
    echo '-';
    global $mysqli;
     $sql = "
        SELECT 
            article
        FROM
            qirim_english_dictionary.tmp_final
        WHERE BINARY
            LCASE(word)  = '$word_string'
        ORDER BY relevance
        ";
    $result = mysqli_fetch_all($mysqli->query($sql));
    return array_column($result, 0);
}

function checkAffiks($chunk){
     global $mysqli;
     $sql = "
        SELECT 
            article, word
        FROM
            qirim_english_dictionary.tmp_final
        WHERE
            word = CONCAT('-','$chunk')
        ";
    $result = mysqli_fetch_all($mysqli->query($sql));
    return array_column($result, 0);
}


function str_split_unicode($str, $l = 0) {
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
init();
<?php


$mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
$mysqli->set_charset("utf8");

function init(){
    $basic_words = ['yapmaq', 'soramaq', 'etmek', 'istemek'];
    include 'fillDiyarMorphology_alphabet.php';
    include 'fillDiyarMorphology_tenses.php';
    include 'fillDiyarMorphology_tenses_negative.php';
    include 'fillDiyarMorphology_moods.php';
    include 'fillDiyarMorphology_moods_negative.php';
    
    $word = 'yapmaq';
    $tense_combined = [];
    
    foreach($moods as $key => $tense){
        $tense_unified = [];
        $tense_combined[$key] = $tense;
        $tense_combined[$key.'_negative'] = $moods_negative[$key.'_negative'];
    } 
    
    $result = [];
    foreach($tense_combined as $key => $tense){
        $result[$key] = composeInflection($word, $tense);
    }
    print_r($result);
    
}


function composeInflection($word,$tense){
    include 'fillDiyarMorphology_alphabet.php';
    include 'fillDiyarMorphology_tenses.php';
    include 'fillDiyarMorphology_tenses_negative.php';
    include 'fillDiyarMorphology_moods.php';
    include 'fillDiyarMorphology_moods_negative.php';
    $word_analysis = getWordAnalysis($word);
    $last_letter = mb_substr($word_analysis['word_base'], -1);
    $sonority_type = '';
    
    if($qt_alphabet[$last_letter]['type'] == 'vowel'){
        $sonority_type = 'vowel';
    } else {
        if($qt_alphabet[$last_letter]['sonorous']){
            $sonority_type = 'sonorous';
        } else {
            $sonority_type = 'non_sonorous';
        }
    }
    if($word_analysis['syllable_quantity'] == 1){
        $syllable_quantity = 'single_syllable';
    } else {
        $syllable_quantity = 'multi_syllable';
    }
    $result = [];
    $plurality_result = [];
    $inflection_template = $tense[$word_analysis['agglutination_mark']][$sonority_type][$syllable_quantity];
    foreach ($inflection_template as $plurality){
        foreach($plurality as &$person){
            $word_base= $word_analysis['word_base'];
            $last_syllable = array_reverse($word_analysis['syllables_list'])[0];
            if(strpos($person,'|')>-1){
                $person_variants = explode('|', $person);
                if(strpos($last_syllable, 'o')>-1 || strpos($last_syllable, 'u')>-1){
                    $person = $person_variants[1];
                } else{
                    $person = $person_variants[0];
                }
            }
            $stress = '́';
            if(mb_strpos($person, '*') === false && mb_strpos($word_base, '\'') === false){
                $word_base = str_replace($last_syllable, setStressOnSyllable($last_syllable), $word_base);
            }
            $person = str_replace('*', '\'', $person);
            $plurality_result[] = $word_base.$person;
        }
        
    }
    return $plurality_result;
    
    
}

function getWordAnalysis($word){
    $word_analysis = [
        'word_base' => '',
        'agglutination_mark' => '',
        'syllables_list' => '',
        'syllable_quantity' => ''
    ];
    if(strpos($word, 'maq')>-1){
        $word_analysis['word_base'] = str_replace('maq', '', $word);
        $word_analysis['agglutination_mark'] = 'hard';
    } else if (strpos($word,'mek')>-1){
        $word_analysis['word_base'] = str_replace('mek', '', $word);
        $word_analysis['agglutination_mark'] = 'soft';
    }
    $word_syllables = getSyllables($word_analysis['word_base']);
    $word_analysis['syllables_list'] = $word_syllables;
    $word_analysis['syllable_quantity'] = count($word_syllables);
    return $word_analysis;
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



function getSyllables($word_string){
    include 'fillDiyarMorphology_alphabet.php';
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
            $prev_letter = key($qt_alphabet[mb_strtolower($word_array[$i-1])]);
            $prev_type = $qt_alphabet[mb_strtolower($word_array[$i-1])]['type'];
        }
        $curr_letter = key($qt_alphabet[mb_strtolower($word_array[$i])]);
        $curr_type = $qt_alphabet[mb_strtolower($word_array[$i])]['type'];
        
               
        $next_letter = '';
        $next_type = '';
        if($word_array[$i+1] != ']'){
            $next_letter = key($qt_alphabet[mb_strtolower($word_array[$i+1])]);
            $next_type = $qt_alphabet[mb_strtolower($word_array[$i+1])]['type'];
        } 
        $ultra_next_letter = '';
        $ultra_next_type = '';
        if(isset($word_array[$i+2]) && $word_array[$i+2] != ']'){
            $ultra_next_letter = key($qt_alphabet[mb_strtolower($word_array[$i+2])]);
            $ultra_next_type = $qt_alphabet[mb_strtolower($word_array[$i+2])]['type'];
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

function setStressOnSyllable($syllable){
    include 'fillDiyarMorphology_alphabet.php';
    $syllable_array = str_split_unicode($syllable, 1);
    foreach($syllable_array as &$letter){
        if($qt_alphabet[$letter]['type'] == 'vowel'){
            $letter = '\''.$letter;
        }
    }
    return implode('', $syllable_array);
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
            
}
init();
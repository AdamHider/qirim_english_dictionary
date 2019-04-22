<?php
$word = 'свет';
function getWord(){
    $result = [];
    global $word;
    $articles = json_decode(file_get_contents('https://lugat.xyz/get_json?word='.$word))->articles;
    foreach($articles as $article){
        $word1 = compileTranslation($article->article);
        array_push($result, $word1);
    }
    //print_r($result);
}

function compileTranslation($translation_string){
    $result_object = [
        'word' => $translation_string
    ];
    if(strpos($result_object['word'],'1.')>-1){
        $result_object['word'] = getFirstMeaning($result_object['word']);
    } else if (strpos($result_object['word'],';')>-1){
        $result_object['word'] = getSecondMeaning($result_object['word']);
    } else if(strpos($result_object['word'],',')>-1){
        $result_object['word'] = getThirdMeaning($result_object['word']);
    } else {
        return $result_object;
    }
    return $result_object;
}

function getFirstMeaning($translation_string){
    $result_object = [];
    $translation_lvl1 = preg_split("/[0-9]\./", $translation_string);
    array_shift($translation_lvl1);
    foreach ($translation_lvl1 as $second_meaning){
        if(strpos($second_meaning,';')>-1){
            array_push($result_object, getSecondMeaning($second_meaning));
        } else {
            if(strpos($second_meaning,',')>-1){
                $third_meaning = getThirdMeaning($third_meaning);
                array_push($result_object,$third_meaning );
            } else {
                array_push($result_object,$second_meaning);
            }
        }
    }
    
    return $result_object;    
}

function getSecondMeaning($translation_string){
    $result_object = [
        'word' => []
        
    ];
    $new_word = explode(";", $translation_string);
    $second_meaning = $new_word[0];
    array_shift($new_word);
    $descriptions = $new_word;
    $result_object['descriptions'] = $descriptions;
        if(strpos($second_meaning,',')>-1){
            $result_object['word'] = getThirdMeaning($second_meaning);
        } else {
            array_push($result_object['word'],$second_meaning);
        }
        
    finalTranslation($result_object);
    return $result_object;
}

function getThirdMeaning($translation_string){
    
    $third_meaning = explode(",", $translation_string);
    
    return $third_meaning; 
}

function finalTranslation($word_object){
    $result = [];
    $description = descriptionsToWords($word_object['descriptions']);
    $words = checkWord($word_object['word']);
    $result['words'] = $words;
    $result['descriptions']  = $description;
    print_r($result);
}

function checkWord($word_array){
    foreach($word_array as &$word){
        $word = trim(preg_replace('/\(<i>.*<\/i>\)/', '', $word));
    }
    return $word_array;
}

function descriptionsToWords($descriptions_array){
    
    global $word;
    $result = [];
    $description_object = [];
    foreach($descriptions_array as $description){
        $first_divide = explode('<b>',$description);
        array_shift($first_divide);
        foreach($first_divide as $division){
            if(strpos($division,'~')>-1){
               $division = str_replace('~', $word, $division);
            }
            $division = explode('</b>', $division);
            foreach($division as &$key){
                preg_match('/\(.*\)/', $key, $matches);
                if(isset($matches[0])){
                    $key = preg_replace('/\(.*\)/', '', $key);
                }
                $key = trim($key);
            }
            $description_object['rus_word'] = $division[0];
            $description_object['qt_word'] = $division[1];
            array_push($result, $description_object);
        }
    }
    return $result;
}

function getFirstLevel($word_string){
    return preg_split('/[0-9]\./', $word_string);
}
function getSecondLevel($word_string){
    return explode(';', $word_string);
}

getWord();

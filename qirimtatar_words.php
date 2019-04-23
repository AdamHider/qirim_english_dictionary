<?php
$adjectives_endings = ["ый", "ий", "ой", "ая", "оя", "ое", "ее", "ои", "ые", "ие"];
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
                $result_object['word'] = $third_meaning['words'];
                $result_object['sub_meaning'] = $third_meaning['sub_meanings'];
                
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
            $third_meaning = getThirdMeaning($second_meaning);
            $result_object['word'] = $third_meaning['words'];
            $result_object['sub_meaning'] = $third_meaning['sub_meanings'];
        } else {
            array_push($result_object['word'],$second_meaning);
        }
        
    finalTranslation($result_object);
    return $result_object;
}

function getThirdMeaning($translation_string){
    preg_match('/\(<i>.*<\/i>\)/', $translation_string, $matches);
    if(isset($matches[0])){
        $rus_submeaning = $matches[0];
        $translation_string = str_replace($matches[0], '', $translation_string);
    }
    
    $third_meaning = explode(",", $translation_string);
    $rus_submeaning = strip_tags($rus_submeaning);
    $rus_submeaning = rtrim(ltrim($rus_submeaning, '('), ')');
    $rus_sub_array = explode(',',$rus_submeaning);
    
    return ['words' => $third_meaning, 'sub_meanings' => $rus_sub_array]; 
}

function finalTranslation($word_object){
    $result = [];
    $description = descriptionsToWords($word_object['descriptions']);
    $words = checkWord($word_object['word']);
    $result['words'] = $words;
    $result['descriptions']  = $description;
    $result['sub_meaning']  = $word_object['sub_meaning'];
    print_r($result);
}

function checkWord($word_array){
  /*  foreach($word_array as &$word){
        $word = trim(preg_replace('/\(<i>.*<\/i>\)/', '', $word));
    }*/
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
               $division = fillTilda($division);
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

function fillTilda($description_rus){
    global $adjectives_endings;
    global $word;
    foreach($adjectives_endings as $ending){
        preg_match("/$ending$/", trim($word), $matches);
        
        if (isset($matches[0])){
            return editAdjectiveEnding($description_rus, $matches[0]);
        }
    }
    $description_rus = str_replace('~', $word, $description_rus);
    return $description_rus; 
}

function editAdjectiveEnding($description_rus, $word_ending){
    global $adjectives_endings;
    global $word;
    $temp_word = $word;
    preg_match("/~[а-я]+/", $description_rus, $matches);
    if (isset($matches[0])){
        print_r($matches[0]);
        if(strlen($matches[0])<2){
            $temp_word = substr($temp_word, 0, -2);
            $description_rus = str_replace('~', $temp_word, $description_rus);
            return $description_rus;
        } else {
            $temp_word = str_replace($word_ending, str_replace('~', '', $matches[0]),$temp_word);
            $description_rus = str_replace($matches[0], $temp_word, $description_rus);
            return $description_rus;
        }    
    } else {
       $description_rus = str_replace('~', $word, $description_rus);
       return $description_rus; 
    }
    //$division = str_replace('~', $word, $division);
}

function getFirstLevel($word_string){
    return preg_split('/[0-9]\./', $word_string);
}
function getSecondLevel($word_string){
    return explode(';', $word_string);
}

getWord();

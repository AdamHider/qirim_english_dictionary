 <?php
$adjectives_endings = ["ый", "ий", "ой", "ая", "оя", "ое", "ее", "ои", "ые", "ие", "ти", "ть", "ить"];
$word = '';

function init(){
    set_time_limit(500);
    global $word;
    $list = getList();
    foreach($list as $rus_word){
        $word = $rus_word[0];
        $translation_found = getWord();
        if(!$translation_found){
            echo 'translation of word: '.$word.' was NOT found!<br/>';
            continue;
        }
        echo 'translation of word: '.$word.' WAS DEFINETELY found!<br/>';
    }
}

function getWord(){
    $result = [];
    global $word;
    error_reporting(0);
    $articles = json_decode(file_get_contents('https://lugat.xyz/get_json?word='.$word))->articles;
    
       
    if (!$articles && strpos($word, 'е')> -1){
        echo "Слово не найдено".'</br>';
        $articles = replaceELetter($word);
        if(!$articles){
            return false;
        }
    }
    error_reporting(1);
    foreach($articles as $article){
        preg_match('/<i>см\..*<\/i>/', $article->article, $matches);
        if(isset($matches[0])){
            $article->article = str_replace($matches[0], '', $article->article);
            $word = strip_tags($article->article);
            getWord();
        }
        compileTranslation($article->article);
    }
    return 'true';
}

function replaceELetter($word){
    preg_match_all('/е+/', $word, $matches, PREG_OFFSET_CAPTURE);
    foreach($matches[0] as $letter){
        $new_word = substr_replace($word,'ё',$letter[1]). substr($word, $letter[1]+2);
        $articles = json_decode(file_get_contents('https://lugat.xyz/get_json?word='.$new_word))->articles;
        if($articles){
           return $articles;
        }
        return false;
    }
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
        $result_object['word'] = getThirdMeaning($result_object);
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
                finalTranslation($result_object); 
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
    $result_object['word'][] = $second_meaning;
    $result_object['descriptions'] = $descriptions;
    if(strpos($second_meaning,',')>-1){
        $third_meaning = getThirdMeaning($result_object);
        $result_object['word'] = $third_meaning['words'];
        $result_object['sub_meaning'] = $third_meaning['sub_meanings'];
    } else {
        $sub_array = findSubMeaning($second_meaning);
        $result_object['sub_meaning'] = $sub_array['sub_meanings']; 
        array_push($result_object['word'], $sub_array['words']);
        finalTranslation($result_object);    
    }
    //finalTranslation($result_object);
    return $result_object;
}

function getThirdMeaning($translation_object){
    $sub_array = findSubMeaning($translation_object['word']);

    $third_meaning = explode(",", $sub_array['words']);

    $rus_sub_array = $sub_array['sub_meanings']; 

    //return ['words' => $third_meaning, 'sub_meanings' => $rus_sub_array];
    
    $result_object['word'] = $third_meaning;
    if(isset($translation_object['descriptions'])){
        $result_object['descriptions'] = $translation_object['descriptions'];
    }
    $result_object['sub_meaning'] = $rus_sub_array;
    finalTranslation($result_object);    
}

function findSubMeaning($input){
    if(is_array($input)){
        $translation_string = $input[0];
    }else {
        $translation_string = $input;
    }
     preg_match('/<i>.*<\/i>/', $translation_string, $matches);
     if(isset($matches[0])){
        $rus_submeaning = $matches[0];
        $translation_string = str_replace($matches[0], '', $translation_string);
        $translation_string = trim(str_replace('()', '', $translation_string));
        $rus_submeaning = strip_tags($rus_submeaning);
        $rus_submeaning = rtrim(ltrim($rus_submeaning, '('), ')');
        $rus_sub_array = explode(',',$rus_submeaning);
    } else {
        $rus_sub_array = [];
    }
    return  ['words' => $translation_string, 'sub_meanings' => $rus_sub_array]; 
}

function finalTranslation($word_object){
    if(!isset($word_object['descriptions'])){
        $word_object['descriptions'] = [];
    }
    $result = [];
    $description = descriptionsToWords($word_object['descriptions']);
    $words = checkWord($word_object['word']);
    $result['words'] = $words;
    $result['descriptions']  = $description;
    $result['sub_meaning']  = $word_object['sub_meaning'];
    foreach($result['words'] as $word){
        $result['words'] = $word;
        if(isset($result['sub_meaning'][0])){
            preg_match('/диал/u', $result['sub_meaning'][0], $matches);
            if(!empty($result['sub_meaning']) && !isset($matches[0])){
                $result['status'] = 'WARNING!';
            } else {
                $result['status'] = 'OKAY!';
            }
        } else {
            $result['status'] = 'OKAY!';
        }
        composLastObject($result);
    }
}

function composLastObject($word){
    print_r($word);
    return;
}

function checkWord($word_array){
    foreach($word_array as &$word){
        $word = trim(preg_replace('/\ {2,10}/', ' ',strip_tags($word)));
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
            $description_object['rus_word'] = preg_replace('/\ {2,10}/', ' ',$division[0]);
            $description_object['qt_word'] = preg_replace('/\ {2,10}/', ' ',strip_tags($division[1]));
            $description_object['qt_word'] = str_replace('◊', '',$division[1]);
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
            $new_word = editAdjectiveEnding($description_rus, $matches[0]);
            if($new_word === 'NOT_FOUND!'){
                continue;
            }
            return $new_word;
        }
    }
    $description_rus = str_replace('~', $word, $description_rus);
    return $description_rus; 
}

function editAdjectiveEnding($description_rus, $word_ending){
    mb_internal_encoding('UTF-8');
    global $adjectives_endings;
    global $word;
    $temp_word = $word;
    preg_match("/~[а-я]+/u", $description_rus, $matches);
    if (isset($matches[0])){
        if(strlen($matches[0])<2){
            $temp_word = substr($temp_word, 0, -2);
            $description_rus = str_replace('~', $temp_word, $description_rus);
            return $description_rus;
        } else {
            $temp_word = str_replace($word_ending, str_replace('~', '', $matches[0]),$temp_word);
            preg_match("/и{2}ть$/u", $temp_word, $matches1);
            if(isset($matches1[0])){
                return 'NOT_FOUND!';
            }
            $description_rus = str_replace($matches[0], $temp_word, $description_rus);
            return $description_rus;
        }    
    } else {
       $description_rus = str_replace('~', $word, $description_rus);
       return $description_rus; 
    }
    //$division = str_replace('~', $word, $division);
}

function getList(){
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'WHERE rus_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT
               name
            FROM
                qirim_english_dictionary.rus_words 
               $already_done
            LIMIT 200, 20
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));
}


init();
//getWord();

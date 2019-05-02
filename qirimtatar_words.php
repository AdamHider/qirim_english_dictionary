 <?php
$adjectives_endings = ["ый", "ий", "ой", "ая", "оя", "ое", "ее", "ои", "ые", "ие", "ти", "ть", "ить"];
$word = '';
$part_of_speech_id = '';
$mysqli; 
function init(){
    set_time_limit(500000);
    global $iteration_count;
    global $word;
    global $part_of_speech_id;
    global $current_rus_id;
    global $total_words_founded;
    $list = getList();
    foreach($list as $rus_word){
        $iteration_count = 0;
        $word = $rus_word[0];
        $part_of_speech_id = $rus_word[1];
        $current_rus_id = $rus_word[2];
        $translation_found = getWord();
        if(!$translation_found){
            continue;
        }
    }
    echo 'Всего слов переведено: '.$total_words_founded;
}

$iteration_count = 0;
function getWord(){
    $result = [];
    global $word;
    global $total_words_founded;
    global $iteration_count;
    $iteration_count += 1;
    if($iteration_count > 10){
        return;
    }
    error_reporting(0);
    $articles = json_decode(file_get_contents('https://lugat.xyz/get_json?word='.$word))->articles;
    if (!$articles && strpos($word, 'е')> -1){
        echo "К сожалению, я не смог найти слово: ".$word.'!</br>';
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
        $total_words_founded = $total_words_founded+1;
        echo "Я нашел слово: ".$word.'!</br>';
    }
    return;
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
        finalTranslation($result_object); 
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
    
    if(!is_array($result['words'])){
        $result['words'] = array ('words'=>$result['words']);
    }    
    
    foreach($result['words'] as $word){
        
        $result['words'] = $word;
        if(isset($result['sub_meaning'][0])){
            preg_match('/диал/u', $result['sub_meaning'][0], $matches);
            if(!empty($result['sub_meaning']) && !isset($matches[0])){
                $result['status'] = 'warning';
            } else {
                $result['status'] = 'normal';
            }
        } else {
            $result['status'] = 'normal';
        }
        
        composLastObject($result);
    }
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
    global $mysqli;
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
               name, part_of_speech_id, rus_word_id
            FROM
                qirim_english_dictionary.rus_words 
               $already_done
         LIMIT 70000, 5000
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));
}


function composLastObject($word){
    return writeDownToDB($word);
}

function writeDownToDB($word){
    global $current_rus_id;
    $qt_word_id = getQirimWordId($word)[0][0];
    addNewToReferences($qt_word_id, $current_rus_id);
    
    if(!empty($word['descriptions'])){
        writeDescriptionToDB($word['descriptions'], $qt_word_id, $current_rus_id);
    }
    
    
}

function getQirimWordId($word_object){
    global $mysqli;
    global $part_of_speech_id;
    $sql = "
        INSERT INTO 
            qirim_english_dictionary.qt_words
        SET
            name = '{$word_object['words']}',
            part_of_speech_id  = '$part_of_speech_id',    
            status = '{$word_object['status']}'    
        ";
    $mysqli->query($sql);
    $sql_2 = "
        SELECT 
            qt_word_id
        FROM 
            qirim_english_dictionary.qt_words
        WHERE
            name = '{$word_object['words']}'
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));        
}

function getRusWordId($rus_word){
    global $mysqli;
    $sql_2 = "
        SELECT 
            rus_word_id
        FROM 
            qirim_english_dictionary.rus_words
        WHERE
            name = '$rus_word' 
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));    
    
}

function addRusWord($word_object){
    global $mysqli;
    $sql = "
        INSERT INTO 
            qirim_english_dictionary.rus_words
        SET
            name = '{$word_object['words']}',
            part_of_speech_id  = '100',    
            rus_status = '{$word_object['rus_status']}'    
        ";
    $mysqli->query($sql);
    $sql_2 = "
        SELECT 
            rus_word_id
        FROM 
            qirim_english_dictionary.rus_words
        WHERE
            name = '{$word_object['words']}'
            AND part_of_speech_id  = '100'    
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));        
}


function addNewToReferences($qt_word_id, $rus_word_id){
    global $mysqli;
    $sql = "
        INSERT INTO 
            qirim_english_dictionary.references_rus_qt
        SET
            rus_word_id = '$rus_word_id',  
            qt_word_id = '$qt_word_id'  
        ";
    $mysqli->query($sql);
    
}
function writeDescriptionToDB($descriptions, $qt_word_id, $rus_word_id){
    $stressed_symbols = [
        '<stress>a</stress>' => 'á',
        '<stress>e</stress>' => 'é',
        '<stress>o</stress>' => 'ó',
        '<stress>i</stress>' => 'i',
        '<stress>ı</stress>' => 'ı',
        '<stress>ö</stress>' => 'ö',
        '<stress>ü</stress>' => 'ü',
        '<stress>u</stress>' => 'ú'
    ];
    foreach($descriptions as &$description){
        $description['qt_word'] = preg_replace('/<i>.*<\/i>/', '', $description['qt_word']);
        if(strpos($description['qt_word'], '<stress>')>-1){
            foreach($stressed_symbols as $symbol=>$value){
                if(strpos($description['qt_word'], $symbol)>-1){
                    $description['qt_word'] = str_replace($symbol, $value, $description['qt_word']);
                }
            }
        }
        $need_to_be_added = checkRusWord($description['rus_word'])[0][0];
        if(!empty($need_to_be_added)){
                $word = [
                'words' => $description['qt_word'],
                'status' => 'description'
            ];
                
            $qt_word_id = getQirimWordId($word)[0][0];
            addNewToReferences($qt_word_id, $need_to_be_added);
            continue;
        }    
        addRusExample($description['rus_word'], $rus_word_id);
        addQtExample($description['qt_word'], $qt_word_id);
    }
    
}

function checkRusWord($rus_word){
    global $mysqli;
    $sql = "
        SELECT 
            rus_word_id
        FROM 
            qirim_english_dictionary.rus_words
        WHERE name = '$rus_word'
        ";
    return mysqli_fetch_all($mysqli->query($sql));     
}

function addRusExample($rus_example, $rus_word_id){
    global $mysqli;
    $sql = "
        INSERT INTO 
            qirim_english_dictionary.rus_word_examples
        SET
            rus_word_id = '$rus_word_id',  
            description = '$rus_example'  
        ";
    $mysqli->query($sql);
}


function addQtExample($qt_example, $qt_word_id){
    global $mysqli;
    $sql = "
        INSERT INTO 
            qirim_english_dictionary.qt_word_examples
        SET
            qt_word_id = '$qt_word_id',  
            description = '$qt_example'  
        ";
    $mysqli->query($sql);
}

/*
function writeDescriptionToDB2($description){
    global $mysqli;
    $rus_word_id = getRusWordId($description['rus_word'])[0][0];
    if($rus_word_id){
        
        $word = [
            'words' => $description['qt_word'],
            'status' => 'description'
        ];
        $qt_word_id = getQirimWordId($word)[0][0];
        addNewToReferences($qt_word_id, $rus_word_id);
    } else {
        $word = [
            'words' => $description['rus_word'],
            'rus_status' => 'description'
        ]; 
        $new_rus_word_id = addRusWord($word)[0][0];
        $qt_word = [
            'words' => $description['qt_word'],
            'status' => 'description'
        ];
        $new_qt_word_id = getQirimWordId($qt_word)[0][0];
        addNewToReferences($new_qt_word_id, $new_rus_word_id);
    }
    
}*/
init();
//getWord();

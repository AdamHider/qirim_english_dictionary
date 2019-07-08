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
        $part_of_speech_id = $rus_word[3];
        $current_rus_id = $rus_word[2];
        $word = $rus_word[0];
        $article = compileTranslation($rus_word[1]);
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
    if(strpos($result_object['word'],'1)')>-1){
        $result_object['word'] = getFirstMeaning($result_object['word']);
    
    } else {
        finalTranslation($result_object); 
    }
    
    return $result_object;
}

function getFirstMeaning($translation_string){
    $result_object = [];
    $result = [];
    
    $translation_lvl1 = preg_split("/[0-9]\)/", $translation_string);
    array_shift($translation_lvl1);
    $result_object['word'] = $translation_lvl1;
    foreach ($result_object['word'] as $word){
        $result['word'] = $word;
        finalTranslation($result); 
              
    }
    return;          
                
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
    $result_object['word'] = $new_word;
    finalTranslation($result_object);
    return;
    $second_meaning = $new_word[0];
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
    //$sub_array = findSubMeaning($translation_object['word']);
    
    preg_match_all('/\([\W]*, [\W]*\)/', $translation_object, $rus_matches);
    preg_match_all('/\([\w ,şçüñğıö]*\)/', $translation_object, $qir_matches);
    if(!empty($rus_matches[0])){
        $temp = str_replace(', ', '|', $rus_matches[0]);
        $new_text = str_replace($rus_matches[0], $temp, $translation_object);
        $third_meaning = explode(",", $new_text);
    } else if(!empty($qir_matches[0])){
        $temp = str_replace(', ', '|', $qir_matches[0]);
        $new_text = str_replace($qir_matches[0], $temp, $translation_object);
        $third_meaning = explode(",", $new_text);
    } else {
        $new_text = $translation_object;
        $third_meaning = explode(",", $new_text);
    }
    return $third_meaning;
    //$rus_sub_array = $sub_array['sub_meanings']; 

    //return ['words' => $third_meaning, 'sub_meanings' => $rus_sub_array];
    
    $result_object['word'] = $translation_object['word'];
    if(isset($translation_object['descriptions'])){
        $result_object['descriptions'] = $translation_object['descriptions'];
    }
    //$result_object['sub_meaning'] = $rus_sub_array;
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

$new_qt_word = '';
function finalTranslation($word_object){
    global $new_qt_word;
    global $current_rus_id;
    global $part_of_speech_id;
    if(!isset($part_of_speech_id)){
        
    }
    print_r($word_object);
    print_r($part_of_speech_id);
    print_r($current_rus_id);
    die;
    $new_qt_word = '';
   /* preg_match('/^\([a-z ,;şçüñğıâö°]*\)/',$word_object['word'], $matches);
    if(isset($matches[0])){
        $word_object['word'] = preg_replace('/\('.$matches[0].'\)/','',$word_object['word'], 1);
        $new_qt_word = preg_replace('/[\(\)]/','',$matches[0]);
    }*/
   /* if(!isset($word_object['descriptions'])){
        $word_object['descriptions'] = [];
    }*/
    
    /*
    if(strpos($word_object['word'], 'см. ')>-1){
        $tmp_array = explode('см. ', $word_object['word']);
        
        $word= $tmp_array[1];
        if(!empty($tmp_array[0])){
            $rus_word = $tmp_array[0];
        } else {
            $rus_word = null;
        }
        $array_ids = getRusWordIds($word);
            if(!empty($array_ids)){
                foreach($array_ids as $qt_id){
                    /*
                      print_r($qt_id[0]);
                    echo ' - ';
                    print_r($current_rus_id);
                    echo '</br>_____';*//*
                    addNewToReferences($qt_id[0],$current_rus_id );
                }
            }   
    }*/
    /*
    if(strpos($word_object['word'], 'ср. ')>-1){
        $word= str_replace('ср. ', '', trim($word_object['word']));
        $sub = preg_split('/\n/',trim($word));
        if(strpos($sub[1], ',')>-1){
            $examples = explode(',', $sub[1]);
           
        } else {
            $examples[] = $sub[1];
        }
        foreach($examples as $example){
            $array_ids = getRusWordIds(trim($example));
            print_r($example);
            die;
        }
        if(!empty($array_ids)){
            foreach($array_ids as $qt_id){
                echo 'added to reference \n';
                /*  print_r($qt_id[0]);
                echo ' - ';
                print_r($current_rus_id);
                echo '</br>_____';
                addNewToReferences($qt_id[0],$current_rus_id );
            }
        }
        return;    
    } else {
        $sub = preg_split('/\n/',trim($word_object['word']));
        $result['words'] = trim($sub[0]);
        unset($sub[0]);
    }*/
    $sub = preg_split('/\n/',trim($word_object['word']));
    $word_object['word'] = $sub[0];
    unset($sub[0]);
    $result = [];
    /*preg_match('/(\([\W ]*\)){0,1} [\w ,;şçüñğıâö°]+(\([\W ]*\)){0,1}/', $word_object['word'], $matches);
    
    $sub = preg_replace('/'.$matches[0].'/','',$word_object['word'], 1);*/
    
   
    $result['sub_meaning']  = $sub; 
    foreach($result['sub_meaning'] as &$meaning){
        $meaning = explode(' - ',$meaning);
        setDescription($meaning);
    }
    
    $result['words'] = trim($word_object['word']);
    $result['status'] = 'normal';
    /*
    $result = [];
    $description = descriptionsToWords($word_object['descriptions']);
    $words = checkWord($word_object['word']);
    $result['words'] = $words;
    $result['descriptions']  = $description;
    $result['sub_meaning']  = '';
    */
    /*
    if(!is_array($result['words'])){
        $result['words'] = array ('words'=>$result['words']);
    }   */ 
    //$result['words'] = $word;
   /* if($rus_word != null){
        $result['words'] = trim($rus_word);
    } else {
        return;
    }*/
    return;
    if(strpos($result['words'], ';')){
        $tmp = explode(';',$result['words']);
        foreach ($tmp as $comma){   
            $temp = getThirdMeaning($comma);
            foreach($temp as $word){
                $result['words'] = trim($word);
                $result['status'] = 'normal';
                composLastObject($result);
            }
        }
    } else if(strpos($result['words'], ',')>-1) {
      
        $temp = getThirdMeaning($result['words']);
        foreach($temp as $word){
            $result['words'] = trim($word);
            $result['status'] = 'normal';
            composLastObject($result);
        } 
    } else {
      composLastObject($result);
    }
    //composLastObject($result);
        
}

function composLastObject($word){
    
                
    global $current_rus_id;
    //$rus_id = checkRusWord($word['words']);
    //die;
    writeDownToDB($word);
    //getQirimWordId($word);
    return;
    
    
    if(!empty($rus_id[0][0])){
         print_r($word);
        print_r($rus_id);
        echo '______';
        addNewToReferences($current_rus_id, $rus_id[0][0]);
    } else {
        if(strpos($word['words'], '-')>-1){
            return;
        }
        $word['rus_status'] = 'normal';
        /*
         print_r($word);
        echo 'NOT FOUND!';
        echo '______';*/
        $new_rus_id = addRusWord($word)[0][0];
        addNewToReferences($current_rus_id, $new_rus_id);
    }
    return;
    //return writeDownToDB($word);
}

function setDescription($array){
    if(!isset($array[1])){
        return;
    }
    $word_object = [
        'words' => $array[1],
        'status' => 'normal'
    ];
    $is_there = checkRusWord($array[0]);
    if(!empty($is_there[0][0])){
        if(strpos($word_object['words'], ';')){
            $tmp = explode(';',$word_object['words']);
            foreach ($tmp as $comma){   
                $temp = getThirdMeaning($comma);
                foreach($temp as $word){
                    $word_object['words'] = trim($word);
                    $word_object['status'] = 'normal';
                    tmp($is_there, $word_object);
                }
            }
        } else if(strpos($word_object['words'], ',')>-1) {

            $temp = getThirdMeaning($word_object['words']);
            foreach($temp as $word){
                $word_object['words'] = trim($word);
                $word_object['status'] = 'normal';
                tmp($is_there, $word_object);
            } 
        } else {
          tmp($is_there, $word_object);
        }
        
    }
}
function tmp($is_there, $word_object){
    $part = '100';
    if(!empty($is_there[0][1])){
        $part = $is_there[0][1];
    }
    print_r($word_object);
    print_r($is_there[0][0]);
    return;
    $qt_word_id = getQirimWordId($word_object, $part);
    addNewToReferences($qt_word_id, $is_there[0][0]);
}

function checkWord($word_array){
    if(!is_array($word_array)){
        return $word_array;
    }
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
    $division = str_replace('~', $word, $division);
}


function getList(){
    global $mysqli;
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'WHERE rus_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "medeniye_db");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT 
            l.word, l.article, qw.qt_word_id, qw.part_of_speech_id
        FROM
            medeniye_db.lugat l
            LEFT JOIN qirim_english_dictionary.qt_words qw ON (l.stem = qw.name)
        WHERE
            l.lang_to = 'ru'  
                AND article NOT LIKE '%/%'
                AND article NOT LIKE '%см. %'
                AND article NOT LIKE '%ср. %'
                AND article NOT LIKE '%1)%'
                AND article NOT LIKE '%,%'
                AND article NOT LIKE '%;%'
                AND article NOT LIKE '% - %'
                AND article NOT LIKE '%(%'
                AND article NOT LIKE '%.%'
            
        ";
    return mysqli_fetch_all($mysqli->query($sql_2));
}



function writeDownToDB($word){
    global $current_rus_id;
    $qt_word_id = getQirimWordId($word);
    addNewToReferences($qt_word_id, $current_rus_id);
    
    if(!empty($word['descriptions'])){
        writeDescriptionToDB($word['descriptions'], $qt_word_id, $current_rus_id);
    }
    
    
}

function getQirimWordId($word_object, $part = 0){
    global $mysqli;
    global $part_of_speech_id;
    if($part != 0){
        $this_part = $part;
    } else {
        $this_part = $part_of_speech_id;
    }
    //print_r('part_of_speech_id: '.$part_of_speech_id.'\n');
    $sql = "
        INSERT INTO 
            qirim_english_dictionary.qt_words
        SET
            name = '".trim($word_object['words'])."',
            part_of_speech_id  = '$this_part',    
            status = '{$word_object['status']}'    
        ";
    $mysqli->query($sql);
    
    $sql_2 = "
        SELECT 
            qt_word_id
        FROM 
            qirim_english_dictionary.qt_words
        WHERE
            name = '".trim($word_object['words'])."' AND part_of_speech_id  = '$this_part'    
        ";
    return mysqli_fetch_all($mysqli->query($sql_2))[0][0];      
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

function getRusWordIds($rus_word){
    global $mysqli;
    $sql_2 = "
        SELECT 
            rw.rus_word_id
        FROM 
            qirim_english_dictionary.qt_words qw
            JOIN
            qirim_english_dictionary.references_rus_qt ref USING (qt_word_id)
            JOIN
            qirim_english_dictionary.rus_words rw USING (rus_word_id)
        WHERE
            qw.name = '".trim($rus_word)."' 
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
            part_of_speech_id  = '102',    
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
            AND part_of_speech_id  = '102'    
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
            rus_word_id, part_of_speech_id
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

function getQTWord($qt_name){
    global $mysqli;
    $sql = "
        SELECT qt_word_id
        FROM
            qirim_english_dictionary.qt_words
        WHERE
            name = '$qt_name' 
        ";
    return mysqli_fetch_all($mysqli->query($sql));      
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

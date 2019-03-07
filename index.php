<?php

/*
$mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");


if (mysqli_connect_errno()) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
}
$sql = "
    INSERT INTO
        `english_b`
    SET
        description = '2'
    ";

$mysqli->query($sql);
*/



$abbreviation_eng = [
    'n' => 'noun',
    'a' => 'adjective' ,
    'adv' => 'adverb',
    'attr.' => 'attributive',
    'conj' => 'conjunction',
    'etc.' => 'etc',
    'imp.' => 'imperative',
    'inf.' => 'infinitive',
    'int' => 'interjenction',
    'num' => 'numeral',
    'o.s.' => 'oneself',
    'p. p.' => 'past_participle',
    'pass.' => 'passive',
    'past' => 'past_tense',
    'pl' => 'plural',
    'pref' => 'prefix',
    'pres.p.' => 'present_participle',
    'pron' => 'pronoun',
    'prep' => 'preposition',
    'refl.' => 'reflexive',
    'syn.' => 'synonym',
    'smb.' => 'somebody',
    'smth.' => 'something',
    's.o.' => 'someone',
    'v' => 'verb',
    'vi' => 'verb_intransitive',
    'vt' => 'verb_transitive'
];
$abbreviation_rus = [
    'ав.' => 'авиация',
    'австрал.' => 'австалийская терминология',
    'авт.' => 'автотранспорт',
    'ам.' => 'американская терминология',
    'анат.' => 'анатомия',
    'англ.' => 'английская терминология',
    'англоинд.' => 'англоиндийская терминология',
    'араб.' => 'арабская терминология',
    'арт.' => 'артиллерия',
    'арх.' => 'архитектура',
    'археол.' => 'археология',
    'астр.' => 'астрономия',
    'астрол.' => 'астрология',
    'бакт.' => 'бактериология',
    'библ.' => 'библейская терминология',
    'биол.' => 'биология',
    'бирж.' => 'биржевая терминология',
    'бот.' => 'ботаника',
    'букв.' => 'буквально',
    'бухг.' => 'бухгалтерская терминология',
    'вет.' => 'ветеринария',
    'вм.' => 'вместо',
    'воен.' => 'военное дело',
    'вопрос.' => 'вопросительный',
    'в разн. знач.'  => 'в разных значениях',
    'вульг.' => 'вульгаризм',
    'г.' => 'город',
    'генет.' => 'генетика',
    'геогр.' => 'география',
    'геод.' => 'геодезия',
    'геол.' => 'геология',
    'геом.' => 'геометрия',
    'гер.' => 'геральдика',
    'гидр.' => 'гидротехника',
    'гл.' => 'главным',
    'глаг.' => 'глагол',
    'голл.' => 'голландская терминология',
    'горн.' => 'горное дело',
    'грам.' => 'грамматика',
    'греч.' => 'греческая терминология',
    'груб.' => 'грубое слово или выражение',
    'дет.' => 'детская терминология',
    'диал.' => 'диалектизм',
    'дип.' => 'дипломатический термин',
    'др.-греч.' => 'древнегреческая история',
    'др.-рим.' => 'древнеримская история',
    'ед.ч.' => 'единственное число',
    'жарг.' => 'жаргонный термин',
    'жив.' => 'живопись',
    'ж.р.' => 'женский род',
    'ж.-д.' => 'железнодорожный термин',
    'зоол.' => 'зоология',
    'и др.' => 'и другое',
    'и пр.' => 'и прочее',
    'инд.' => 'индийская терминология',
    'ирл.' => 'ирландская терминология',
    'ирон.' => 'иронично',
    'иск.' => 'искусство',
    'исп.' => 'испанская терминология',
    'ист.' => 'исторический термин',
    'и т.п.' => 'и тому подобное',
    'итал.' => 'итальянская терминология',
    'карт.' => 'термин карточной игры',
    'кино' => 'кинематография',
    'кит' => 'китайская терминология',
    'ком.' => 'коммерческий термин',
    'комп.' => 'информатика и компьютерные технологии',
    'кул.' => 'кулинария',
    'л.' => 'лицо',
    'ласк.' => 'ласкательное слово или выражение',
    'лат.' => 'латинский термин',
    'лес.' => 'лесное дело',
    'лингв.' => 'лингвистика',
    'лит.' => 'литература',
    'лог.' => 'логика',
    'мат.' => 'математика',
    'мед.' => 'медицина',
    'мест.' => 'местоимение',
    'метал.' => 'металлургия',
    'метеор.' => 'метеорология',
    'метр.' => 'метрология',
    'мех.' => 'механика',
    'мин.' => 'минералогия',
    'миф.' => 'мифология',
    'мн.ч.' => 'множественное число',
    'мор.' => 'морское дело',
    'муз.' => 'музыка',
    'накл.' => 'наклонение',
    'напр.' => 'например',
    'нареч.' => 'наречие',
    'науч.' => 'научная терминология',
    'нач.' => 'начало',
    'нем.' => 'немецкая терминология',
    'неол.' => 'неологизм',
    'обыкн.' => 'обыкновенно',
    'опт.' => 'оптика',
    'особ.' => 'особенно',
    'охот.' => 'охотничий термин',
    'офиц.' => 'официальное выражение',
    'парл.' => 'парламентский термин',
    'перен.' => 'в переносном значении',
    'погов.' => 'поговорка',
    'пол.' => 'политический термин',
    'полигр.' => 'полиграфия',
    'португ.' => 'португальская терминология',
    'посл.' => 'пословица',
    'поэт.' => 'поэтический термин',
    'превосх.' => 'превосходная',
    'предл.' => 'предложение',
    'предлог.' => 'предлогами',
    'презр.' => 'презрительно',
    'преим.' => 'преимущественно',
    'пренебр.' => 'пренебрежительно',
    'прибл.' => 'приблизительно',
    'прилаг.' => 'имя пралагательное',
    'притяж.' => 'притяжательное местоимение',
    'прям.' => 'в прямом значении',
    'психол.' => 'психология',
    'р.' => 'род',
    'рад.' => 'радиотехника',
    'разг.' => 'разговорное выражение',
    'распр.' => 'распространительно',
    'редк.' => 'редко употребляемое слово',
    'рел.' => 'религия',
    'рит.' => 'риторика',
    'род.' => 'родительный (падеж)',
    'св.' => 'святой',
    'сказ.' => 'сказочная терминология',
    'см.' => 'смотри',
    'собир.' => 'собирательное выражение',
    'сокр.' => 'сокращение',
    'сослаг.' => 'сослагательный',
    'спорт.' => 'физкультура и спорт',
    'ср.' => 'сравни',
    'сравнит.' => 'сравнительная',
    'ср.-век.' => 'средневековый',
    'степ.' => 'степень',
    'стр.' => 'строительный термин',
    'сущ.' => 'имя существительное',
    'с.-х.' => 'сельское хозяйство',
    'твор.' => 'творительный (падеж)',
    'т.е.' => 'то есть',
    'театр.' => 'театральный термин',
    'текст.' => 'текстильное дело',
    'тел.' => 'телефония',
    'телев.' => 'телевидение',
    'тех.' => 'техника',
    'тж.' => 'также',
    'топогр.' => 'топография',
    'тур.' => 'турецкая терминология',
    'уменьш.' => 'уменьшительная форма',
    'унив.' => 'универскитетский термин',
    'употр.' => 'употребляется',
    'уст.' => 'устаревшее слово или выражение',
    'утверд.' => 'утвердительный',
    'фарм.' => 'фармакология',
    'физ.' => 'физика',
    'физиол.' => 'физиология',
    'филол.' => 'филология',
    'филос.' => 'философия',
    'фин.' => 'финансковый термин',
    'фон.' => 'фонетика',
    'фот.' => 'фотография',
    'фр.' => 'французская терминология',
    'футб.' => 'футбол',
    'хим.' => 'химия',
    'хир.' => 'хирургия',
    'церк.' => 'церковный термин',
    'что-н.' => 'что-нибудь',
    'шахм.' => 'шахматный термин',
    'шк.' => 'школьное выражение',
    'шотл.' => 'шотландская терминология',
    'шутл.' => 'шутливое выражение',
    'эвф.' => 'эвфемизм',
    'эк.' => 'экономика',
    'эл.' => 'электроника',
    'южноафр.' => 'юэноафриканская терминология',
    'юр.' => 'юридический термин',
    'яп.' => 'японская терминология'
];

function scanPage(){
    $links = [];
    for($i=3; $i<100; $i++){
        
        $html=file_get_contents('https://gufo.me/dict/enru_muller?page='.$i.'&letter=b');
        if (!$html){
            break;
        }
        $start = strpos($html, '<ul class="list-unstyled app-word-list">');
        $end = strpos($html, '</article>');

        $length = $end-$start;
        $html = substr($html, $start, $length);
        preg_match_all("/<a[^>]+href=\"([^\"]+)\"[^>]*>/",$html,$matches_links);
        
        for($i = 0; $i < 10; $i++){
            print_r(getTranslation($matches_links[1][$i]));
           
            echo '<br>';
            echo '<br>';  
        }
    }
} 

function getTranslation($url){
    set_time_limit(8000);
    $origin = explode('/', $url);
    $word_object = [
        'origin' => $origin[3],
        'translation' => []
    ];
    $html = file_get_contents('https://gufo.me'.$url);
    $start = strpos($html, 'lightslategray');
    $end = strpos($html, '<div class="fb-quote"></div>');

    $length = $end-$start;
    $html = substr($html, $start, $length);
    $html = strip_tags($html);
    $new_start = strpos($html, ']');
    $new_length = strlen($html)-$new_start;
    $word_string = trim(substr($html, $new_start+1, $new_length));
    
    for($i = 1; $i<15; $i++){
        if(strpos($word_string, ($i.'.')) === false){
            if(empty($word_object['translation'])){
                $word_object['translation'][0]['word'] = $word_string;
                $abbreviation_eng = findAbbreviationEng($word_object['translation'][0]['word']);
                if ($abbreviation_eng){
                    $word_object['translation'][0]['word'] = validateTextWord($abbreviation_eng['word']);
                    $word_object['translation'][0]['abbreviation_eng'] = $abbreviation_eng['abbreviation_eng'];
                }
                $abbreviation_rus = findAbbreviationRus($word_object['translation'][0]['word']);
                if ($abbreviation_rus){
                    $word_object['translation'][0]['word'] = validateTextWord($abbreviation_rus['word']);
                    $word_object['translation'][0]['abbreviation_rus'] = $abbreviation_rus['abbreviation_rus'];
                }
                
                return $word_object;
            }else{
                $word_object['translation'][0]['word'] = validateTextWord($word_object['translation'][0]['word']);
                return $word_object;
            }
        } else {
            $meaning_start = strpos($word_string, ($i.'.'));
            $meaning_end = strpos($word_string, ($i+1).'.');
            if($meaning_end == 0){
                $meaning_length  = strlen($word_string)-$meaning_start;
            }else{
                $meaning_length = $meaning_end-$meaning_start;  
            }
            $new_word_string = substr($word_string, $meaning_start+2, $meaning_length-2);
            $word_object['translation'][$i]['word'] = $new_word_string;
            
            if (strpos($word_object['translation'][$i]['word'],'1)')){
                return getSubMeaning($word_object['translation'][$i]['word'],$word_object['origin']);
            }
            
            $abbreviation_eng = findAbbreviationEng($new_word_string);
            if ($abbreviation_eng){
                $word_object['translation'][$i]['word'] = validateTextWord($abbreviation_eng['word']);
                $word_object['translation'][$i]['abbreviation_eng'] = $abbreviation_eng['abbreviation_eng'];
            }
            $abbreviation_rus = findAbbreviationRus($word_object['translation'][$i]['word']);
            if ($abbreviation_rus){
                $word_object['translation'][$i]['word'] = validateTextWord($abbreviation_rus['word']);
                $word_object['translation'][$i]['abbreviation_rus'] = $abbreviation_rus['abbreviation_rus'];
            }
        }
    }
   
    return $word_object;
    //getQirimTranslation(trim($newhtml));
}

function getSubMeaning($word, $origin){
    $sub_meaning_array = [
        'origin' => $origin
    ];
    $main_abbreviation_eng = findAbbreviationEng($word);
    
    for($k = 1; $k < 15; $k++){
        
        $word_sub_string = $word;
        $sub_meaning_start = strpos($word_sub_string, ($k.')'));

        if(!$sub_meaning_start){
            return $sub_meaning_array;
        }
        $sub_meaning_end = strpos($word_sub_string, ($k+1).')');
        if($sub_meaning_end == 0){
            $sub_meaning_length  = strlen($word_sub_string)-$sub_meaning_start;
        }else{
            $sub_meaning_length = $sub_meaning_end-$sub_meaning_start;  
        }
        $new_word_sub_string = substr($word_sub_string, $sub_meaning_start+3, $sub_meaning_length-3);
        
        $sub_meaning_array[$k]['word'] = $new_word_sub_string;
        print_r($new_word_sub_string);
        die;
       
        if ($main_abbreviation_eng){
            $sub_meaning_array[$k]['abbreviation_eng'] = $main_abbreviation_eng['abbreviation_eng'];
        }
        $abbreviation_eng = findAbbreviationEng($new_word_sub_string);
        if ($abbreviation_eng){
            $sub_meaning_array[$k]['word'] = validateTextWord($abbreviation_eng['word']);
            $sub_meaning_array[$k]['abbreviation_eng'] = $abbreviation_eng['abbreviation_eng'];
        } else {
            $sub_meaning_array[$k]['word'] = validateTextWord($sub_meaning_array[$k]['word']);
        }
        $abbreviation_rus = findAbbreviationRus($sub_meaning_array[$k]['word']);
        if ($abbreviation_rus){
            $sub_meaning_array[$k]['word'] = validateTextWord($abbreviation_rus['word']);
            $sub_meaning_array[$k]['abbreviation_rus'] = $abbreviation_rus['abbreviation_rus'];
        } else {
            $sub_meaning_array[$k]['word'] = validateTextWord($sub_meaning_array[$k]['word']);
        }
    }
}

//$findAbbreviationEng = findAbbreviationEng();
function findAbbreviationEng($word_string){
    error_reporting(0);
    global $abbreviation_eng;
    foreach ($abbreviation_eng as $abbr_eng=>$value){
        if (preg_match("/^ *($abbr_eng)[^a-z]/",$word_string)){
            $new_word_string = preg_replace("/^ *($abbr_eng)[^a-z]/", '', $word_string);
            return $meaning_object = [
                'word' => $new_word_string,
                'abbreviation_eng' => $value
            ];
        } else {
            continue;
        }
    }
    error_reporting(1);
}

function findAbbreviationRus($word_string){
    error_reporting(0);
    global $abbreviation_rus;
    $meaning_object = [
                'word' => $word_string,
                'abbreviation_rus' => []
    ];
    foreach ($abbreviation_rus as $abbr_rus=>$value){
        if (strpos($abbr_rus,'.')){
            $abbr_for_regex = str_replace(".", "\.", $abbr_rus);
        }
        if (preg_match_all("/[\s]($abbr_for_regex)|^($abbr_for_regex)/",$meaning_object['word'])){
            $new_word_string = str_replace($abbr_rus,'', $meaning_object['word']);
            $meaning_object['word'] = $new_word_string;
            array_push($meaning_object['abbreviation_rus'],$value);
        } else {
             continue;
        }
    }
    error_reporting(1);
    return $meaning_object;
}


function validateTextWord($word_string){
    $validated_meaning = [
        'meaning_lvl1' => []
    ];
    if (strpos($word_string, 'Ⅱ')){
        $meaning_lvl1 = explode('Ⅱ', $word_string);
        $validated_meaning['meaning_lvl1'] = $meaning_lvl1[0];
    } else if (strpos($word_string, 'Ⅲ')){
        $meaning_lvl1 = explode('Ⅲ', $word_string);
        $validated_meaning['meaning_lvl1'] = $meaning_lvl1[0];
    } else {
        $validated_meaning['meaning_lvl1'] = $word_string;
    }
    
    $validated_meaning['meaning_lvl1'] = preg_replace("/◊ /", '', $validated_meaning['meaning_lvl1']);
    preg_match_all("/[^\;]*[\s][a-zA-Z].*[a-zA-Z].\W*/", $validated_meaning['meaning_lvl1'], $matches);
    
    
  
    $validated_meaning['meaning_lvl1'] = preg_replace("/[^\;]*[\s][a-zA-Z].*[a-zA-Z].\W*/", '', $validated_meaning['meaning_lvl1']);
    $validated_meaning['context_meanings'] = $matches[0];
   
    if (strpos($validated_meaning['meaning_lvl1'], ';')){
        if(strlen($validated_meaning['meaning_lvl1']) - strpos($validated_meaning['meaning_lvl1'], ';') == 3){
            $meaning_lvl2 = explode(';', $validated_meaning['meaning_lvl1']);
            $validated_meaning['meaning_lvl1'] = $meaning_lvl2[0];
            
            preg_match_all("/\([\W].*\)/", $validated_meaning['meaning_lvl1'], $matches);
            if(!empty($matches[0])){
                $validated_meaning['meaning_lvl1'] = preg_replace("/\([\W].*\)/", '', $validated_meaning['meaning_lvl1']);
                $validated_meaning['meaning_lvl1'] .= " ".$matches[0][0];
            }
            if (strpos($validated_meaning['meaning_lvl1'], ',')){
                $meaning_lvl2 = explode(',', $validated_meaning['meaning_lvl1']);
                $validated_meaning['meaning_lvl1'] = $meaning_lvl2;
            }    
        } else {
            if(substr(trim($validated_meaning['meaning_lvl1']), -1) == ';'){
                $validated_meaning['meaning_lvl1'] = rtrim(trim($validated_meaning['meaning_lvl1']),substr(trim($validated_meaning['meaning_lvl1']), -1));
            }
            $meaning_lvl2 = explode(';', $validated_meaning['meaning_lvl1']);
            $validated_meaning['meaning_lvl1'] = $meaning_lvl2;
            for($i = 0; $i<count($validated_meaning['meaning_lvl1']); $i++){
                $meaning_group = ltrim($validated_meaning['meaning_lvl1'][$i]);
                preg_match_all("/\([\W].*\)/", $meaning_group, $matches);
                if(!empty($matches[0])){
                    $validated_meaning['meaning_lvl1'][$i] = preg_replace("/\([\W].*\)/", '', $meaning_group);
                    $validated_meaning['meaning_lvl1'][$i] .= " ".$matches[0][0];
                }
                if (strpos($meaning_group, ',')){
                    $meaning_lvl3 = explode(',', $meaning_group);
                    unset($validated_meaning['meaning_lvl1'][$i]);
                    foreach ($meaning_lvl3 as $mean_lvl3){
                        array_push($validated_meaning['meaning_lvl1'],$mean_lvl3 );
                    }
                }else{
                    preg_match_all("/\([\W].*\)/", $meaning_group, $matches);
                    if(!empty($matches[0])){
                        $meaning_group = preg_replace("/\([\W].*\)/", '', $meaning_group);
                        $meaning_group .= " ".$matches[0][0];
                    }
                    continue;
                }
            }
        }
        return $validated_meaning['meaning_lvl1'];
    }else if(strpos($validated_meaning['meaning_lvl1'], ',')){
        preg_match_all("/\([\W].*\,[\W].*\)/", $validated_meaning['meaning_lvl1'],$matches);
        if(!empty($matches[0])){
            $validated_meaning['meaning_lvl1'] = preg_replace("/\([\W].*\)/", '', $validated_meaning['meaning_lvl1']);
            if(strpos($validated_meaning['meaning_lvl1'], ',')){
                $meaning_lvl3 = explode(',', $validated_meaning['meaning_lvl1']);
                foreach($meaning_lvl3 as $mean_lvl3){
                    preg_match_all("/\([\W].*\)/", $mean_lvl3, $matches);
                    if(!empty($matches[0])){
                        $mean_lvl3 = preg_replace("/\([\W].*\)/", '', $mean_lvl3);
                        $mean_lvl3 .= " ".$matches[0][0];
                    } 
                }
                $validated_meaning['meaning_lvl1'] = $meaning_lvl3;
                return $validated_meaning['meaning_lvl1']; 
            }else{
                $validated_meaning['meaning_lvl1'] .= " ".$matches[0][0];
                return $validated_meaning['meaning_lvl1']; 
            }
        }
        $meaning_lvl3 = explode(',', $validated_meaning['meaning_lvl1']);
        foreach($meaning_lvl3 as $mean_lvl3){
            preg_match_all("/\([\W].*\)/", $mean_lvl3, $matches);
            if(!empty($matches[0])){
                $mean_lvl3 = preg_replace("/\([\W].*\)/", '', $mean_lvl3);
                $mean_lvl3 .= " ".$matches[0][0];
            }
        }
        $validated_meaning['meaning_lvl1'] = $meaning_lvl3;
        return $validated_meaning['meaning_lvl1']; 
    }else{
        return $validated_meaning['meaning_lvl1']; 
    }
    
    
}

function getQirimTranslation($word){
    $html = file_get_contents('https://gufo.me/dict/rucrh/'.$word);
    $start = strpos($html, 'lightslategray');
    $end = strpos($html, '<div class="fb-quote"></div>');

    $length = $end-$start;
    $html = substr($html, $start, $length);
    $html = strip_tags($html);
    $new_start = strpos($html, ']');
    $new_length = strlen($html)-$new_start;
    $newhtml = substr($html, $new_start+3, $new_length);
    
}
scanPage();
//print_r(validateTextWord());
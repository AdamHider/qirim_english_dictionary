<?php


    $mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
    $mysqli->set_charset("utf8");
function init(){
    
    set_time_limit(8000000);
    $basic_words = getList();
    include 'fillDiyarMorphology_alphabet.php';
    include 'fillDiyarMorphology_tenses.php';
    include 'fillDiyarMorphology_tenses_negative.php';
    include 'fillDiyarMorphology_moods.php';
    include 'fillDiyarMorphology_moods_negative.php';
    include 'fillDiyarMorphology_participle_full_past.php';
    include 'fillDiyarMorphology_participle_full_present.php';
    //include 'fillDiyarMorphology_moods_negative.php';
    $parts_of_speech = [
        'VERB' => [$tenses, $tenses_negative, $moods, $moods_negative],
        'PRTF' => [$participles_active_past, $participles_active_present]
    ];
    foreach($basic_words as $word){
        foreach($parts_of_speech as $pts_name => $part_of_speech){
            foreach($part_of_speech as $current_tense){
                foreach($current_tense as $key => $tense){
                    //print_r($key);
                    $result = composeInflection($word['word'], $tense);
                    $final_object[] = finalComposing($result, $key, $pts_name, $word);
                    
                }  
            }
        }
       
    }
    
    die;
    
}

function putData($data){
    global $mysqli;
    return updateData($data);
    $error_message = '';
    foreach($data as $item){
        $sql = "
           INSERT INTO 
               rusqt_morph_items1 
           SET 
               word = '".$item['word']."', 
               word_id = '".$item['word_id']."', 
               part_of_speech_id = '".$item['part_of_speech_id']."', 
               remote_word = '".$item['word']."', 
               morph_word = '".$item['morph_word']."', 
               morph_word_stressed = '".$item['morph_word_stressed']."', 
               person = '".$item['person']."', 
               plurality = '".$item['plurality']."', 
               tense = '".$item['tense']."', 
               mood = '".$item['mood']."', 
               `case` = '".$item['case']."',
               morph_part_of_speech = '".$item['part_of_speech']."', 
               perfectness = '".$item['perfectness']."', 
               tense_mark = '".$item['tense_mark']."'
       ";
       $result = mysqli_query($mysqli, $sql);
       if ( empty($result))  {
            $error_message .= mysqli_error($mysqli) . "\n";
            echo $error_message;
        }
    }
}

function updateData($data){
    global $mysqli;
    $error_message = '';
    if(empty($data)){
        return;
    }
    foreach($data as $item){
        $sql = "
           UPDATE
               lgt_morpheme_list 
           SET 
               transcription = '".$item['morph_word_stressed']."'
            WHERE morpheme = '".$item['morph_word']."'    
       ";
       $result = mysqli_query($mysqli, $sql);
       if ( empty($result))  {
            $error_message .= mysqli_error($mysqli) . "\n";
            echo $error_message;
        }
    }
}

function finalComposing($result, $tense, $pts_name, $word){
    
    if($pts_name == 'VERB'){
        composeTensesMorphology($result, $tense, $word);
        composeTensesNegativeMorphology($result, $tense, $word);
    }
    if($pts_name == 'PRTF' || $pts_name == 'PRTS'){
        composeParticipleMorphology($result, $tense, $pts_name, $word);
    }
    
    
}

function composeTensesMorphology($result, $tense, $word){
    
    $result_list = [];
    require_once 'transcription.php';
    
    foreach($result as $key => $form){
        $word_object = [];
        $word_object['word'] = $word['word'];
        $word_object['word_id'] = $word['word_id'];
        $word_object['part_of_speech_id'] = $word['part_of_speech_id'];
        $word_object['plurality'] = '';
        $word_object['person'] = '';
        $word_object['part_of_speech'] = '';
        $word_object['perfectness'] = '';
        $word_object['tense'] = '';
        $word_object['mood'] = '';
        $word_object['case'] = '';
        $word_object['tense_mark'] = '';
        $word_object['morph_word'] = str_replace('|', '', $form);
        $word_object['morph_word_stressed'] = transcription_transcribe($form);
        
        $word_object['verb_negative'] = '';
        switch($key){
            case 0:
                $word_object['plurality'] = 'sing';
                $word_object['person'] = '1per';
                break;
            case 1:
                $word_object['plurality'] = 'sing';
                $word_object['person'] = '2per';
                break;
            case 2:
                $word_object['plurality'] = 'sing';
                $word_object['person'] = '3per';
                break;
            case 3:
                $word_object['plurality'] = 'plur';
                $word_object['person'] = '1per';
                break;
            case 4:
                $word_object['plurality'] = 'plur';
                $word_object['person'] = '2per';
                break;
            case 5:
                $word_object['plurality'] = 'plur';
                $word_object['person'] = '3per';
                break;
        }
        switch($tense){
            case 'past_simple':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'past_continuous':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'past_remote':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'past_unfinished':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'past_retelling':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'present_simple':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'pres';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'present_continuous':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'pres';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'future_simple':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'perf';
                $word_object['tense'] = 'futr';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'wide':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'pres';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $word_object1 = $word_object;
                $word_object1['perfectness'] = 'perf';
                $word_object1['tense'] = 'futr';
                $result_list[] = $word_object;
                $result_list[] = $word_object1;
                break;
            case 'future_in_the_past':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'imperative_mood':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = '';
                $word_object['mood'] = 'impr';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'obligation_mood':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = '';
                $word_object['mood'] = 'oblg';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'obligation_past_mood':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'oblg';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'desire_mood':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = '';
                $word_object['mood'] = 'desr';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'subjunctive_mood':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = '';
                $word_object['mood'] = 'subj';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'conditional_mood':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'pres';
                $word_object['mood'] = 'cond';
                $word_object['tense_mark'] = $tense;
                
                $word_object2 = $word_object;
                $word_object2['tense'] = 'futr';
                $word_object2['perfectness'] = 'all';
                
                $result_list[] = $word_object;
                $result_list[] = $word_object2;
                break;
            case 'conditional_past_mood':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'cond';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
        }
    }
    putData($result_list);
    return $result_list;
    
}

function composeParticipleMorphology($result, $tense, $pts_name, $word){
    $result_list = [];
    foreach($result as $key => $form){
        $word_object = [];
        $word_object['word'] = $word['word'];
        $word_object['word_id'] = $word['word_id'];
        $word_object['part_of_speech_id'] = $word['part_of_speech_id'];
        $word_object['plurality'] = '';
        $word_object['person'] = '';
        $word_object['part_of_speech'] = '';
        $word_object['perfectness'] = '';
        $word_object['tense'] = '';
        $word_object['mood'] = '';
        $word_object['case'] = '';
        $word_object['tense_mark'] = '';
        $word_object['morph_word'] = str_replace('|', '', $form);
        $word_object['morph_word_stressed'] = transcription_transcribe($form);
        switch($key){
            case 0:
                $word_object['plurality'] = 'sing';
                break;
            case 1:
                $word_object['plurality'] = 'plur';
                break;
        }
        switch($tense){
            case 'nominative_past':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'past';
                $word_object['case'] = 'nomn';
                $word_object['tense_mark'] = $tense;
                $word_object1 = $word_object;
                $word_object['tense'] = 'pres';
                $result_list[] = $word_object;
                $result_list[] = $word_object1;
                break;
            case 'nominative_present':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'pres';
                $word_object['case'] = 'nomn';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'genitive_past':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'past';
                $word_object['case'] = 'gent';
                $word_object['tense_mark'] = $tense;
                $word_object1 = $word_object;
                $word_object['tense'] = 'pres';
                $result_list[] = $word_object;
                $result_list[] = $word_object1;
                break;
            case 'genitive_present':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'pres';
                $word_object['case'] = 'gent';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'dative_past':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'past';
                $word_object['case'] = 'datv';
                $word_object['tense_mark'] = $tense;
                $word_object1 = $word_object;
                $word_object['tense'] = 'pres';
                $result_list[] = $word_object;
                $result_list[] = $word_object1;
                break;
            case 'dative_present':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'pres';
                $word_object['case'] = 'datv';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'accusative_past':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'past';
                $word_object['case'] = 'accs';
                $word_object['tense_mark'] = $tense;
                $word_object1 = $word_object;
                $word_object['tense'] = 'pres';
                $result_list[] = $word_object;
                $result_list[] = $word_object1;
                break;
            case 'accusative_present':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'pres';
                $word_object['case'] = 'accs';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'placive_past':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'past';
                $word_object['case'] = 'loct';
                $word_object['tense_mark'] = $tense;
                $word_object1 = $word_object;
                $word_object['tense'] = 'pres';
                $result_list[] = $word_object;
                $result_list[] = $word_object1;
                break;
            case 'placive_present':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'pres';
                $word_object['case'] = 'loct';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'exodive_past':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'past';
                $word_object['case'] = 'exod';
                $word_object['tense_mark'] = $tense;
                $word_object1 = $word_object;
                $word_object['tense'] = 'pres';
                $result_list[] = $word_object;
                $result_list[] = $word_object1;
                break;
            case 'exodive_present':
                $word_object['part_of_speech'] = $pts_name;
                $word_object['tense'] = 'pres';
                $word_object['case'] = 'exod';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'gerund':
                $word_object['part_of_speech'] = 'GRND';
                $word_object['tense'] = '';
                $word_object['case'] = '';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
        }
    }
    putData($result_list);
    return $result_list;
    
}

function composeTensesNegativeMorphology($result, $tense, $word){
    $result_list = [];
    require_once 'transcription.php';
    foreach($result as $key => $form){
        $word_object = [];
        $word_object['word'] = $word['word'];
        $word_object['word_id'] = $word['word_id'];
        $word_object['part_of_speech_id'] = $word['part_of_speech_id'];
        $word_object['plurality'] = '';
        $word_object['person'] = '';
        $word_object['part_of_speech'] = '';
        $word_object['perfectness'] = '';
        $word_object['tense'] = '';
        $word_object['mood'] = '';
        $word_object['case'] = '';
        $word_object['tense_mark'] = '';
        $word_object['morph_word'] = str_replace('|', '', $form);
        $word_object['morph_word_stressed'] = transcription_transcribe($form);
        $word_object['verb_negative'] = 'negt';
        
        
        switch($key){
            case 0:
                $word_object['plurality'] = 'sing';
                $word_object['person'] = '1per';
                break;
            case 1:
                $word_object['plurality'] = 'sing';
                $word_object['person'] = '2per';
                break;
            case 2:
                $word_object['plurality'] = 'sing';
                $word_object['person'] = '3per';
                break;
            case 3:
                $word_object['plurality'] = 'plur';
                $word_object['person'] = '1per';
                break;
            case 4:
                $word_object['plurality'] = 'plur';
                $word_object['person'] = '2per';
                break;
            case 5:
                $word_object['plurality'] = 'plur';
                $word_object['person'] = '3per';
                break;
        }
        switch($tense){
            case 'past_simple_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'past_continuous_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'past_remote_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'past_unfinished_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'past_retelling_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'present_simple_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'pres';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'present_continuous_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'pres';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'future_simple_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'perf';
                $word_object['tense'] = 'futr';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'wide_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = 'pres';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $word_object1 = $word_object;
                $word_object1['perfectness'] = 'perf';
                $word_object1['tense'] = 'futr';
                $result_list[] = $word_object;
                $result_list[] = $word_object1;
                break;
            case 'future_in_the_past_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'indc';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'imperative_mood_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'impf';
                $word_object['tense'] = '';
                $word_object['mood'] = 'impr';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'obligation_mood_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = '';
                $word_object['mood'] = 'oblg';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'obligation_past_mood_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'oblg';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'desire_mood_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = '';
                $word_object['mood'] = 'desr';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'subjunctive_mood_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = '';
                $word_object['mood'] = 'subj';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
            case 'conditional_mood_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'pres';
                $word_object['mood'] = 'cond';
                $word_object['tense_mark'] = $tense;
                
                $word_object2 = $word_object;
                $word_object2['tense'] = 'futr';
                $word_object2['perfectness'] = 'all';
                
                $result_list[] = $word_object;
                $result_list[] = $word_object2;
                break;
            case 'conditional_past_mood_negative':
                $word_object['part_of_speech'] = 'VERB';
                $word_object['perfectness'] = 'all';
                $word_object['tense'] = 'past';
                $word_object['mood'] = 'cond';
                $word_object['tense_mark'] = $tense;
                $result_list[] = $word_object;
                break;
        }
    }
    putData($result_list);
    return $result_list;
    
}

function getList(){
    $conn = mysqli_connect("127.0.0.1", "root", "root", "diyar_db");
    $conn->set_charset("utf8");
    $sql = "
        SELECT 
            *
        FROM(
        SELECT wl.word_id, wl.word, part_of_speech_id FROM
            diyar_db.lgt_morpheme_list m
            JOIN lgt_word_list wl USING(word_id)
        WHERE
            LENGTH(m.`transcription`) - LENGTH(REPLACE(m.`transcription`, '|', '')) > 1
                AND (m.morpheme  LIKE '% %'
                OR m.morpheme  LIKE '%-%')

                AND m.part_of_speech = 'VERB'
        GROUP BY wl.word_id)t
        ";
    return mysqli_fetch_all($conn->query($sql), MYSQLI_ASSOC);
}

function composeInflection($word,$tense){
    include 'fillDiyarMorphology_alphabet.php';
    include 'fillDiyarMorphology_tenses.php';
    include 'fillDiyarMorphology_tenses_negative.php';
    include 'fillDiyarMorphology_moods.php';
    include 'fillDiyarMorphology_participle_adjectives.php';
    include 'fillDiyarMorphology_moods_negative.php';
    $word_analysis = getWordAnalysis($word);
    $last_letter = mb_substr($word_analysis['word_base'], -1);
    $sonority_type = '';
    $last_letter = mb_strtolower(mb_substr($word_analysis['word_base'], -1));
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
        
        
        if(mb_strpos($word, 'aytmaq') !== false){
            $syllable_quantity = 'multi_syllable';
        }
        if(mb_strpos($word, 'almaq') !== false){
            $syllable_quantity = 'multi_syllable';
        }
        if(mb_strpos($word, 'barmaq') !== false){
            $syllable_quantity = 'multi_syllable';
        }
        if(mb_strpos($word, 'bermek') !== false){
            $syllable_quantity = 'multi_syllable';
        }
        if(mb_strpos($word, 'kelmek') !== false){
            $syllable_quantity = 'multi_syllable';
        }
        if(mb_strpos($word, 'qalmaq') !== false){
            $syllable_quantity = 'multi_syllable';
        }
        if(mb_strpos($word, 'olmaq') !== false){
            $syllable_quantity = 'multi_syllable';
        }
        
        
        $result = [];
        $plurality_result = [];
        $plurality_result_string = '';
        $inflection_template = $tense[$word_analysis['agglutination_mark']][$sonority_type][$syllable_quantity];
                
        foreach ($inflection_template as $plurality){
            foreach($plurality as &$person){
                
    
                $word_base = checkLastLetter($word_analysis['word_base'], $person,$syllable_quantity);
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
                if(mb_strpos($person, '*') === false && mb_strpos($word_base, '|') === false){
                    $word_base = str_lreplace($last_syllable, setStressOnSyllable($last_syllable), $word_base);
                }
                $person = str_replace('*', '|', $person);
                $morph = $word_base.$person;
                $plurality_result_string .=  ' '.str_replace('|', '', $morph);
                $plurality_result[] = $morph;
            }
        }
        return $plurality_result;
}

function getWordAnalysis($word){
    $word_analysis = [
        'word_base' => $word,
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
        $word_analysis['agglutination_mark'] = getAgglutinationMark(array_reverse($word_analysis['syllables_list'])[0]);
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

     function checkLastLetter($word_base, $person, $syllable_quantity){
        include 'fillDiyarMorphology_alphabet.php';
        $first_person_letter = mb_substr($person, 0, 1);
        if($first_person_letter == '*'){
            $first_person_letter = mb_substr($person, 1, 1);
        }
        if(!empty($first_person_letter) && $qt_alphabet[$first_person_letter]['type'] == 'vowel'){
            $last_letter = mb_substr($word_base, -1, 1);
          /*  if($qt_alphabet[$last_letter]['type'] == 'vowel'){
                $word_base .= 'y';
            }*/
            $exit = ['tüp', 'cep', 'yaq'];
            if(!in_array($word_base, $exit) && $syllable_quantity == 'single_syllable'){
                return $word_base;
            }
            if($last_letter == 'q' ){
                $word_base = mb_substr($word_base, 0, -1);
                $word_base .= 'ğ';
            } else if($last_letter == 'k' ){
                $word_base = mb_substr($word_base, 0, -1);
                $word_base .= 'g';
            } else if($last_letter == 'p' ){
                $word_base = mb_substr($word_base, 0, -1);
                $word_base .= 'b';
            }
        }
        return $word_base;
    }

function getSyllables($word_string){
    include 'fillDiyarMorphology_alphabet.php';
        
        if(strpos($word_string, ' ') > -1){
            $word_string = array_reverse(explode(' ', $word_string))[0];
        }
        if(strpos($word_string, '-') > -1){
            $word_string = array_reverse(explode('-', $word_string))[0];
        }
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
            $alphabet_letter = mb_strtolower($letter);
            if($qt_alphabet[$alphabet_letter]['type'] == 'vowel'){
                $letter = '|'.$letter;
            }
        }
        return implode('', $syllable_array);
    }

     function getAgglutinationMark($syllable){
        include 'fillDiyarMorphology_alphabet.php';
        $syllable_array = str_split_unicode($syllable, 1);
        foreach($syllable_array as &$letter){
            $alphabet_letter = mb_strtolower($letter);
            if($qt_alphabet[$alphabet_letter]['type'] == 'vowel'){
                if($qt_alphabet[$alphabet_letter]['soft']){
                    return 'soft';
                }
                return 'hard';
            }
        }
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
    
     function array_mix($array1, $array2){
        $result = [];
        foreach($array1 as $key => $value1){
            $result[$key] = $value1;
            $result[$key.'_negative'] = $array2[$key.'_negative'];
        }
        return $result;
    }
    function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
init();
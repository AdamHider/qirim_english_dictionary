<?php


function init(){
    set_time_limit(8000000);
    $conn = mysqli_connect("127.0.0.1", "root", "root", "diyar_db");
    $conn->set_charset("utf8");
    $affectedRow = 0;
    
    $list = getList();
    foreach($list as $list_item){
        $forms = getWordForm($list_item['word_id'], 'perf', 'indc', 'futr');
        /*
        $xml = simplexml_load_string($item['rev_text']) or die("Error: Cannot create object");
        $array = simpleXml2ArrayWithCDATASupport($xml);
       
        $parent_word = $array['l']['@attributes']['t'];
        $item['parent_attributes'] = parseAttributes($array['l']['g']);
        */
        $i = 0;
        foreach($forms as $item){
           /* $item['child_word'] = '';
            if(!empty($child['@attributes']['t'])){
                $item['child_word'] = $child['@attributes']['t'];
            }
            if(!empty($child['g'])){
                $item['child_attributes'] = parseAttributes($child['g']);
            } else {
                $item['child_attributes'] = parseAttributes([]);
            }
            */
            $result_to_write = [];
            $current_word = $item['morph_word'];
            $item['mood'] = 'cond';
            $item['tense_mark'] = 'cond';
            
            if($item['plurality'] == 'sing'){
                if($item['person'] == '1per'){
                    if($item['sex'] == 'neut'){
                        
                    } else {
                        $item['morph_word'] = 'если я '.$current_word;
                        $item['morph_word_negative'] = 'если я не '.$current_word;
                        $item1 = $item;
                        $item1['sex'] == 'all';
                        $item1['morph_word'] = 'если '.$current_word;
                        $item1['morph_word_negative'] = 'если не '.$current_word;
                        $result_to_write[] = $item;
                        $result_to_write[] = $item1;
                    } 
                } else 
                if($item['person'] == '2per'){
                    if($item['sex'] == 'neut'){
                        
                    } else {
                        $item['morph_word'] = 'если ты '.$current_word;
                        $item['morph_word_negative'] = 'если ты не '.$current_word;
                        $item1 = $item;
                        $item1['sex'] == 'all';
                        $item1['morph_word'] = 'если '.$current_word;
                        $item1['morph_word_negative'] = 'если не '.$current_word;
                        $result_to_write[] = $item;
                        $result_to_write[] = $item1;
                    } 
                } else 
                if($item['person'] == '3per'){
                    if($item['sex'] == 'masc'){
                        $item['morph_word'] = 'если он '.$current_word;
                        $item['morph_word_negative'] = 'если он не '.$current_word;
                        $result_to_write[] = $item;
                    } else 
                    if($item['sex'] == 'femn'){
                        $item['morph_word'] = 'если она '.$current_word;
                        $item['morph_word_negative'] = 'если она не '.$current_word;
                        $result_to_write[] = $item;
                    } else 
                    if($item['sex'] == 'neut'){
                        $item['morph_word'] = 'если оно '.$current_word;
                        $item['morph_word_negative'] = 'если оно не '.$current_word;
                        $result_to_write[] = $item;
                    } else {
                        $item['sex'] = 'masc';
                        $item['morph_word'] = 'если он '.$current_word;
                        $item['morph_word_negative'] = 'если он не '.$current_word;
                        $item1 = $item;
                        $item1['sex'] = 'femn';
                        $item1['morph_word'] = 'если она '.$current_word;
                        $item1['morph_word_negative'] = 'если она не '.$current_word;
                        $item2 = $item;
                        $item2['sex'] = 'neut';
                        $item2['morph_word'] = 'если оно '.$current_word;
                        $item2['morph_word_negative'] = 'если оно не '.$current_word;
                        $item3 = $item;
                        $item3['sex'] = 'all';
                        $item3['morph_word'] = 'если '.$current_word;
                        $item3['morph_word_negative'] = 'если не '.$current_word;
                        $result_to_write[] = $item;
                        $result_to_write[] = $item1;
                        $result_to_write[] = $item2;
                        $result_to_write[] = $item3;
                    } 
                } else {
                    if($item['sex'] == 'masc'){
                        $item['person'] = '1per';
                        $item['morph_word'] = 'если я '.$current_word;
                        $item['morph_word_negative'] = 'если я не '.$current_word;
                        $item1 = $item;
                        $item1['person'] = '2per';
                        $item1['morph_word'] = 'если ты '.$current_word;
                        $item1['morph_word_negative'] = 'если ты не '.$current_word;
                        $item2 = $item;
                        $item2['person'] = '3per';
                        $item2['morph_word'] = 'если он '.$current_word;
                        $item2['morph_word_negative'] = 'если он не '.$current_word;
                        $item3 = $item;
                        $item3['person'] = 'all';
                        $item3['morph_word'] = 'если '.$current_word;
                        $item3['morph_word_negative'] = 'если не '.$current_word;
                        $result_to_write[] = $item;
                        $result_to_write[] = $item1;
                        $result_to_write[] = $item2;
                        $result_to_write[] = $item3;
                        
                    } else if($item['sex'] == 'femn'){
                        $item['person'] = '1per';
                        $item['morph_word'] = 'если я '.$current_word;
                        $item['morph_word_negative'] = 'если я не '.$current_word;
                        $item1 = $item;
                        $item1['person'] = '2per';
                        $item1['morph_word'] = 'если ты '.$current_word;
                        $item1['morph_word_negative'] = 'если ты не '.$current_word;
                        $item2 = $item;
                        $item2['person'] = '3per';
                        $item2['morph_word'] = 'если она '.$current_word;
                        $item2['morph_word_negative'] = 'если она не '.$current_word;
                        $item3 = $item;
                        $item3['person'] = 'all';
                        $item3['morph_word'] = 'если '.$current_word;
                        $item3['morph_word_negative'] = 'если не '.$current_word;
                        $result_to_write[] = $item;
                        $result_to_write[] = $item1;
                        $result_to_write[] = $item2;
                        $result_to_write[] = $item3;
                        
                    } else  if($item['sex'] == 'neut'){
                        $item['person'] = '3per';
                        $item['morph_word'] = 'если оно '.$current_word;
                        $item['morph_word_negative'] = 'если оно не '.$current_word;
                        $item1 = $item;
                        $item1['morph_word'] = 'если '.$current_word;
                        $item1['morph_word_negative'] = 'если не '.$current_word;
                        $result_to_write[] = $item;
                        $result_to_write[] = $item1;
                    } 
                }
            } else {
                if($item['person'] == '1per'){
                    $item['morph_word'] = 'если мы '.$current_word;
                    $item['morph_word_negative'] = 'если мы '.$current_word;
                    $item1 = $item;
                    $item1['morph_word'] = 'если '.$current_word;
                    $item1['morph_word_negative'] = 'если не '.$current_word;
                    $result_to_write[] = $item;
                    $result_to_write[] = $item1;
                } else 
                if($item['person'] == '2per'){
                    $item['morph_word'] = 'если вы '.$current_word;
                    $item['morph_word_negative'] = 'если вы не '.$current_word;
                    $item1 = $item;
                    $item1['morph_word'] = 'если '.$current_word;
                    $item1['morph_word_negative'] = 'если не '.$current_word;
                    $result_to_write[] = $item;
                    $result_to_write[] = $item1;
                } else 
                if($item['person'] == '3per'){
                    $item['morph_word'] = 'если они '.$current_word;
                    $item['morph_word_negative'] = 'если они не '.$current_word;
                    $item1 = $item;
                    $item1['morph_word'] = 'если '.$current_word;
                    $item1['morph_word_negative'] = 'если не '.$current_word;
                    $result_to_write[] = $item;
                    $result_to_write[] = $item1;
                } else {
                    $item['person'] = '1per';
                    $item['morph_word'] = 'если мы '.$current_word;
                    $item['morph_word_negative'] = 'если мы не '.$current_word;
                    $item1 = $item;
                    $item1['person'] = '2per';
                    $item1['morph_word'] = 'если вы '.$current_word;
                    $item1['morph_word_negative'] = 'если вы не '.$current_word;
                    $item2 = $item;
                    $item2['person'] = '3per';
                    $item2['morph_word'] = 'если они '.$current_word;
                    $item2['morph_word_negative'] = 'если они не '.$current_word;
                    $item3 = $item;
                    $item3['person'] = 'all';
                    $item3['morph_word'] = 'если '.$current_word;
                    $item3['morph_word_negative'] = 'если не '.$current_word;
                    $result_to_write[] = $item;
                    $result_to_write[] = $item1;
                    $result_to_write[] = $item2;
                    $result_to_write[] = $item3;
                }
             }      
            //$item['morph_word'] = 'если '.$item['morph_word'];
            $i++;
            foreach ($result_to_write as $write){
                $sql = "
                     INSERT INTO 
                         rus_morph_items1 
                     SET 
                         word = '".$write['word']."', 
                         word_id = '".$write['word_id']."', 
                         part_of_speech_id = '".$write['part_of_speech_id']."', 
                         remote_word = '".$write['remote_word']."', 
                         revision_id = '".$write['revision_id']."', 
                         set_id = '".$write['set_id']."', 
                         lemma_id = '".$write['lemma_id']."', 
                         morph_word = '".$write['morph_word']."', 
                         morph_word_negative = '".$write['morph_word_negative']."', 
                         person = '".$write['person']."', 
                         plurality = '".$write['plurality']."', 
                         tense = '".$write['tense']."', 
                         mood = '".$write['mood']."', 
                         sex = '".$write['sex']."',  
                         tense_mark = '".$write['tense_mark']."',
                         morph_part_of_speech = '".$write['morph_part_of_speech']."', 
                         perfectness = '".$write['perfectness']."', 
                         transitivity = '".$write['transitivity']."'
                 ";
                 $result = mysqli_query($conn, $sql);
                 
             }
            
        }
       
        if (! empty($result)) {
            $affectedRow ++;
        } else {
            $error_message = mysqli_error($conn) . "\n";
            echo $error_message;
        }
    }
    
    echo $affectedRow;
}

function getList(){
    $conn = mysqli_connect("127.0.0.1", "root", "root", "diyar_db");
    $conn->set_charset("utf8");
    $sql = "
        SELECT 
            word_id, word
          FROM
            diyar_db.rus_morph_items1
            WHERE part_of_speech_id = 26 AND morph_part_of_speech = 'VERB' 
        GROUP BY word_id  
        ";
    return mysqli_fetch_all($conn->query($sql), MYSQLI_ASSOC);
}

function getWordForm($word_id, $param1='', $param2='', $param3='', $param4=''){
    $conn = mysqli_connect("127.0.0.1", "root", "root", "diyar_db");
    $conn->set_charset("utf8");
    $sql = "
        SELECT 
            *
        FROM
            diyar_db.rus_morph_items1
        
        WHERE word_id = '$word_id' AND morph_part_of_speech = 'VERB' 
              AND tense_mark IS NULL AND mood != 'cond' AND (tense = 'past' OR tense = 'pres')
              
            ";
    return mysqli_fetch_all($conn->query($sql), MYSQLI_ASSOC);
}

function parseAttributes($attributes_array){
    $result = [];
    for($i = 0; $i < 10; $i++){
        if(!empty($attributes_array[$i]['@attributes']['v'])){
            $result[$i] = $attributes_array[$i]['@attributes']['v'];
        } else {
            $result[$i] = '';
        }
    }
    return $result;
}

 function simpleXml2ArrayWithCDATASupport($xml)
{
    $array = (array)$xml;

    if (count($array) === 0) {
        return (string)$xml;
    }

    foreach ($array as $key => $value) {
        if (is_object($value) && strpos(get_class($value), 'SimpleXML') > -1) {
            $array[$key] = simpleXml2ArrayWithCDATASupport($value);
        } else if (is_array($value)) {
            $array[$key] = simpleXml2ArrayWithCDATASupport($value);
        } else {
            continue;
        }
    }

    return $array;
}

init();
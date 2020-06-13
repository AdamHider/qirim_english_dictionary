<?php


function init(){
    set_time_limit(8000000);
    $conn = mysqli_connect("127.0.0.1", "root", "root", "diyar_db");
    $conn->set_charset("utf8");
    $affectedRow = 0;
    $error_message = '';
    $list = getList();
    foreach($list as $item){
        if(empty($item['rev_text'])){
            continue;
        }
        $xml = simplexml_load_string($item['rev_text']) or die("Error: Cannot create object");
        $array = simpleXml2ArrayWithCDATASupport($xml);
       
        $parent_word = $array['l']['@attributes']['t'];
        $item['parent_attributes'] = parseAttributes($array['l']['g']);
        if(empty($array['f'])){
            continue;
        }
        foreach($array['f'] as $child){
            $item['child_word'] = '';
            if(!empty($child['@attributes']['t'])){
                $item['child_word'] = $child['@attributes']['t'];
            } else if (!empty($child['t'])){
                $item['child_word'] = $child['t'];
            }
            if(!empty($child['g'])){
                $item['child_attributes'] = parseAttributes($child['g']);
            } else {
                $item['child_attributes'] = parseAttributes([]);
            }
            if(empty($item['child_word'])){
                continue;
            }
            $sql = "
                INSERT INTO 
                    rus_morph_items1 
                SET 
                    word = '".$item['word']."', 
                    word_id = '".$item['word_id']."', 
                    part_of_speech_id = '".$item['part_of_speech_id']."', 
                    remote_word = '".$item['lemma_text']."', 
                    revision_id = '".$item['rev_id']."', 
                    set_id = '".$item['set_id']."', 
                    lemma_id = '".$item['lemma_id']."', 
                    morph_word = '".$item['child_word']."', 
                    attribute1 = '".$item['child_attributes'][0]."', 
                    attribute2 = '".$item['child_attributes'][1]."', 
                    attribute3 = '".$item['child_attributes'][2]."', 
                    attribute4 = '".$item['child_attributes'][3]."', 
                    attribute5 = '".$item['child_attributes'][4]."', 
                    attribute6 = '".$item['child_attributes'][5]."', 
                    attribute7 = '".$item['child_attributes'][6]."', 
                    attribute8 = '".$item['child_attributes'][7]."', 
                    attribute9 = '".$item['child_attributes'][8]."', 
                    attribute10 = '".$item['child_attributes'][9]."',
                    parent_attribute1 = '".$item['parent_attributes'][0]."', 
                    parent_attribute2 = '".$item['parent_attributes'][1]."', 
                    parent_attribute3 = '".$item['parent_attributes'][2]."', 
                    parent_attribute4 = '".$item['parent_attributes'][3]."', 
                    parent_attribute5 = '".$item['parent_attributes'][4]."'
                ON DUPLICATE KEY UPDATE 
                    word = '".$item['word']."', 
                    word_id = '".$item['word_id']."', 
                    part_of_speech_id = '".$item['part_of_speech_id']."', 
                    remote_word = '".$item['lemma_text']."', 
                    revision_id = '".$item['rev_id']."', 
                    set_id = '".$item['set_id']."', 
                    lemma_id = '".$item['lemma_id']."', 
                    morph_word = '".$item['child_word']."', 
                    attribute1 = '".$item['child_attributes'][0]."', 
                    attribute2 = '".$item['child_attributes'][1]."', 
                    attribute3 = '".$item['child_attributes'][2]."', 
                    attribute4 = '".$item['child_attributes'][3]."', 
                    attribute5 = '".$item['child_attributes'][4]."', 
                    attribute6 = '".$item['child_attributes'][5]."', 
                    attribute7 = '".$item['child_attributes'][6]."', 
                    attribute8 = '".$item['child_attributes'][7]."', 
                    attribute9 = '".$item['child_attributes'][8]."', 
                    attribute10 = '".$item['child_attributes'][9]."',
                    parent_attribute1 = '".$item['parent_attributes'][0]."', 
                    parent_attribute2 = '".$item['parent_attributes'][1]."', 
                    parent_attribute3 = '".$item['parent_attributes'][2]."', 
                    parent_attribute4 = '".$item['parent_attributes'][3]."', 
                    parent_attribute5 = '".$item['parent_attributes'][4]."'
            ";
            $result = mysqli_query($conn, $sql);
        }
        
       
        if (! empty($result)) {
            $affectedRow ++;
        } else {
            $error_message .= mysqli_error($conn) . "\n";
        }
    }
    echo $error_message;
    echo $affectedRow;
}

function getList(){
    $conn = mysqli_connect("127.0.0.1", "root", "root", "diyar_db");
    $conn->set_charset("utf8");
    $sql = "
        SELECT DISTINCT
            wl.*,
            lemmas.lemma_text,
            revs1.*
        FROM
            zaliznyak_dict.dict_revisions revs
                        JOIN 
            zaliznyak_dict.dict_lemmata lemmas ON revs.lemma_id = lemmas.lemma_id
                        JOIN
            zaliznyak_dict.dict_links links ON links.lemma1_id = lemmas.lemma_id
                        JOIN
            zaliznyak_dict.dict_lemmata lemmas1 ON links.lemma2_id = lemmas1.lemma_id
                        JOIN
            zaliznyak_dict.dict_links links1 ON links1.lemma1_id = lemmas1.lemma_id
                        JOIN
            zaliznyak_dict.dict_lemmata lemmas2 ON links1.lemma2_id = lemmas2.lemma_id
                        JOIN 
            zaliznyak_dict.dict_revisions revs1 ON revs1.lemma_id = lemmas2.lemma_id
                        JOIN 
            diyar_db.complete_rus_word_list wl ON wl.word = lemmas.lemma_text 
          
        ";
    return mysqli_fetch_all($conn->query($sql), MYSQLI_ASSOC);
}

function parseAttributes($attributes_array){
    $result = [];
    for($i = 0; $i < 10; $i++){
        if(count($attributes_array) == 1 && !empty($attributes_array['@attributes']['v'])){
            $result[$i] = $attributes_array['@attributes']['v'];
            $attributes_array['@attributes']['v'] = '';
        } else {
            if(!empty($attributes_array[$i]['@attributes']['v'])){
                $result[$i] = $attributes_array[$i]['@attributes']['v'];
            } else {
                $result[$i] = '';
            }
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
<?php
$mysqli = new mysqli("127.0.0.1", "root", "root", "diyar_db");
$mysqli->set_charset("utf8");

$required_attributes_config = [
    'part_of_speech',
    'tense',
    'tense_minor',
    'mood',
    'perfectness',
    'is_negative'
];

$config = [
    'VERB|past|defn|indc|all|afrm|' => 
        [
            'NPRO||||{person}|{plurality}|nomn|||||| + word?VERB|past|defn|indc|all|{plurality}||masc|||impf/perf|afrm||'
        ],
    'VERB|past|defn|indc|all|negt|' => 
        [
            'NPRO||||{person}|{plurality}|nomn|||||| + не + word?VERB|past|defn|indc|all|{plurality}||masc|||impf/perf|afrm||'
        ],
    
    'VERB|past|remt|indc|all|afrm|' => 
        [
            'NPRO||||{person}|{plurality}|nomn|||||| + word?VERB|past|defn|indc|all|{plurality}||asex|||perf|afrm||'
        ],
    'VERB|past|remt|indc|all|negt|' => 
        [
            'NPRO||||{person}|{plurality}|nomn|||||| + не + word?VERB|past|defn|indc|all|{plurality}||asex|||perf|afrm||'
        ],
    
    'VERB|pres|defn|cond|all|afrm|' => 
        [
           'если + NPRO||||{person}|{plurality}|nomn|||||| + word?VERB|pres|defn|indc|{person}|{plurality}||asex|||impf/perf|afrm||'
        ],
    'VERB|pres|defn|cond|all|negt|' => 
        [
            'если + NPRO||||{person}|{plurality}|nomn|||||| + не + word?VERB|pres|defn|indc|{person}|{plurality}||asex|||impf/perf|afrm||'
        ]
];

function init(){
    
    searchByWordform('если он кладёт');
}

function composeWordMorphology($search_word){
    
}

function searchByWordform($search_word){
    $referent_word_ids = getReferentWordIds($search_word);
    if(mb_strpos($search_word, ' ') !== false){
        
    }
    foreach($referent_word_ids as $referent_word_id){
        $template = convertWordformToTemplate($search_word);
        $result[] = replicate($search_word, $referent_word_id[0], $template);
    }
    print_r($result);
    die;
    
}


function replicate($search_word, $referent_word_id, $template){
    global $config;
    global $required_attributes_config;
    $required_attributes_template = '';
    $referent_wordforms = [];
    $result = [];
    foreach($required_attributes_config as $config_attribute){
        $required_attributes_template .= $template[$config_attribute].'|';
    }
    if(!empty($required_attributes_template) && !empty($config[$required_attributes_template])){
        foreach($config[$required_attributes_template] as $referent_set_configuration){
            $referent_wordforms[] = composeReferentWordform($referent_set_configuration, $template, $referent_word_id);
        }
    }
    $result['source_word'] = $search_word;
    $result['referent_wordforms'] = $referent_wordforms;
    return $result;
}

function composeReferentWordform($config_template, $source_template, $referent_word_id){
    $config_exploded = explode(' + ', $config_template);
    $result = [];
    foreach($config_exploded as &$param){
        $parameter_object = handleTemplate($param,$source_template, $referent_word_id);
        $wordform = getWorformBySetTemplate($parameter_object)['wordform'];
        if(strpos($wordform, '|') !== false){
            return;
        }
        $result[] = $wordform;
    }
    return implode(' ',$result);
}

function handleTemplate($template, $source_template, $referent_word_id, $output = []){
    $template_exploded = explode('|',$template);
    foreach($template_exploded as &$set_column){
        if(strpos($set_column, '{') !== false){
            $column_name = str_replace(['{','}'], '', $set_column);
            if(!empty($source_template[$column_name])){
                $set_column = $source_template[$column_name];
            }
        }
        if(strpos($set_column, 'word?') !== false){
            $output['referent_word_id'] = $referent_word_id;
            $set_column = str_replace('word?', '', $set_column);
        }
        if(strpos($set_column, '/') !== false){
            $options = explode('/', $set_column);
            foreach($options as $option){
                $set_column = $option;
                $template_string = implode('|',$template_exploded);
                $output = handleTemplate($template_string, $source_template, $referent_word_id, $output);
            }
        }
    }
    $template_string = implode('|',$template_exploded);
    if(empty($output['template'])){
        $output['template'] = [];
    }
    if( !in_array($template_string, $output['template'])){
        $output['template'][] = $template_string;
    }
    return $output;
       
}

function convertWordformToTemplate($wordform){
    //$template = getWordformSet($wordform);
    global $config;
    $exploded_phrase = explode(' ', $wordform);
    $phrasal_template = [];
    $source_templates = [];
    foreach($exploded_phrase as $phrase_item){
        $source_templates[] = getWordformSetByWordform($phrase_item);
    }
    foreach($config as $config_item){
        $exploded = explode(' + ', $config_item[0]);
        compareTemplateGroups($exploded, $source_templates);
    }
            
    return $template;
}

function compareTemplateGroups($config_item, $source_templates){
    foreach($config_item as $template){
        foreach($source_templates as $source_template){
            if($template === $source_template ){
                continue 2;
            }
            if(strpos($template,'|') !== false){
                $template_exploded = explode('|', $template);
                $source_template_exploded = explode('|', $source_template);
                $ok = compareTemplates($template_exploded, $source_template_exploded);
            }
            
        }
    }
}

function compareTemplates($template_exploded, $source_template_exploded){
    foreach($template_exploded as $template_column){
        foreach($source_template_exploded as $source_template_column){
            print_r($template_column);
            print_r($source_template_column);
            die;
        }
        
    }
    
}

function getWordformSet($wordform){
    global $mysqli;
    $sql = "
        SELECT 
            s.*
        FROM
            diyar_db.lgt_wordform_list wfl
            JOIN 
            diyar_db.lgt_wordform_sets s USING(set_configuration_id)
        WHERE 
                wfl.wordform = '$wordform'
        GROUP BY wordform_id
        ";
    $result = mysqli_fetch_assoc($mysqli->query($sql));
    return $result;
}

function findInDatabase($wordform){
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

function getWorformBySetTemplate($param){
    $where = '';
    $where .= 's.template IN (';
    foreach($param['template'] as $key => $template){
        $where .= "'".$template."'";
        if(!empty($param['template'][$key+1])){
            $where .= ',';
        } else {
            $where .= ') ';
        }
    }
    if(!empty($param['referent_word_id'])){
        $where .= ' AND wfl.word_id = '.$param['referent_word_id'];
    }
    global $mysqli;
    $sql = "
        SELECT 
            wfl.wordform
        FROM
            diyar_db.lgt_wordform_list wfl
            JOIN 
            diyar_db.lgt_wordform_sets s USING(set_configuration_id)
        WHERE 
            $where
    ";
    $result = mysqli_fetch_assoc($mysqli->query($sql));
    if(empty($result)){
        return ['wordform' => $param['template'][0]];
    }
    return $result;
}

function getWordformSetByWordform($wordform){
    global $mysqli;
    $sql = "
        SELECT 
            s.template
        FROM
            diyar_db.lgt_wordform_list wfl
            JOIN 
            diyar_db.lgt_wordform_sets s USING(set_configuration_id)
        WHERE 
                wfl.wordform = '$wordform'
        GROUP BY wordform_id
        ";
    $result = mysqli_fetch_assoc($mysqli->query($sql));
    if(empty($result)){
        return $wordform;
    }
    return $result['template'];
}

function getReferentWordIds($search_word){
    global $mysqli;
    $sql = "
        SELECT 
            wl1.word_id
        FROM
            diyar_db.lgt_wordform_list wfl
                JOIN 
            diyar_db.lgt_word_list wl ON wl.word_id = wfl.word_id
                JOIN
            diyar_db.lgt_word_list wl1 ON wl.denotation_id = wl1.denotation_id AND wl.language_id != wl1.language_id 
        WHERE 
            wfl.wordform = '$search_word'
        ";
    $result = mysqli_fetch_all($mysqli->query($sql));
    return $result;
}

init();
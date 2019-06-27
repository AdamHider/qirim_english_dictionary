<?php
$current_word_list = [];
$query = '';
$mysqli;
function getList(){
    global $query;
    global $mysqli;
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'HAVING ew.eng_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    
    print_r(file_get_contents('https://translate.google.com/?hl=ru#view=home&op=translate&sl=ru&tl=en&text=шарик'));
    die;
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $db_word_list = mysqli_fetch_all($mysqli->query("
        SELECT DISTINCT
            refer.rus_word_id,
            (SELECT DISTINCT
                    COUNT(rus_word_id)
                FROM
                    references_rus_qt ref
                WHERE
                    ref.rus_word_id = refer.rus_word_id) AS c , rw.name, lug.article
        FROM
            qirim_english_dictionary.references_rus_qt refer
                JOIN
            rus_words rw USING (rus_word_id)
		JOIN 
            medeniye_db.lugat lug ON (lug.word = rw.name)
        HAVING c > 1
        ORDER BY rus_word_id
        LIMIT 50"));
    $new_list = [];
     foreach ($db_word_list as $word){
        $word_object = [];
        $word_object['word_id'] = $word[0];
        $word_object['word_name'] = $word[2];
        $word_object['reference_quantity'] = $word[1];
        $word_object['lugat_article'] = $word[3];
        if(strpos($word_object['lugat_article'], 'см.')>-1){
            preg_match_all('/^см\.[а-я ]*/',$word_object['lugat_article'], $matches );
            
            if(isset($matches[0])){
                $search_word = trim(str_replace('см.', '', $word_object['lugat_article']));
                $word_object['lugat_article'] = mysqli_fetch_all($mysqli->query("SELECT article FROM medeniye_db.lugat WHERE word = '$search_word'"))[0][0];
                
            }
        }   
        $common_refs = mysqli_fetch_all($mysqli->query("SELECT ref.reference_id, qw.name, qw.qt_word_id FROM references_rus_qt ref JOIN qt_words qw USING (qt_word_id)WHERE ref.rus_word_id = '{$word[0]}'"));
        foreach ($common_refs as $ref){
            $reference = [];
            $reference['reference_id'] = $ref[0];
            $reference['qt_word_name'] = $ref[1];
            $reference['qt_word_id'] = $ref[2];
            $word_object['references'][] = $reference;
        }
            $new_list[]=$word_object;
    }
    
    echo json_encode($new_list);
}

function getTotal(){
    global $query;
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'HAVING ew.eng_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
        SELECT COUNT(*)
            FROM
                qirim_english_dictionary.rus_words rw
                    JOIN
                qirim_english_dictionary.`references` ref ON (ref.rus_word_id = rw.rus_word_id)
                    JOIN
                eng_words ew ON (ew.eng_word_id = ref.eng_word_id)
            WHERE rw.name LIKE '%$query%' OR rw.name LIKE '$query%' OR rw.name LIKE '%$query' 
        ";
    echo json_encode(mysqli_fetch_row($mysqli->query($sql_2)));
}

function commit(){
    global $query;
    set_time_limit(500);
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    if(!empty($_GET['data'])){
        $data = json_decode($_GET['data']);
        for($i=0; $i<count($data); $i++){
            $object = [
                'rus_word' => $data[$i][0],
                'eng_word' => $data[$i][1],
                'eng_id' => $data[$i][2],
                'rus_id' => $data[$i][3],
            ];
            if(isset($data[$i][4])){
                putThatDone($object['eng_id']);
                continue;
            } else {
                updateRusName($object['rus_id'], $object['eng_id'], $object['rus_word']);
            }
        }
    }
}

function updateRusName($rus_id = '', $eng_id = '', $rus_word = ''){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    if(!empty($_GET['newword'])){
        $data = explode(';',$_GET['newword']);
        $rus_word_id = $data[0];
        $new_word_name = $data[1];
        $eng_word_id = $data[2];
    } else {
        $rus_word_id = $rus_id;
        $new_word_name = editWord($rus_word);
        $eng_word_id = $eng_id;
    }
    $sql_4 = "
       UPDATE qirim_english_dictionary.rus_words 
       SET name = '".$new_word_name."'
       WHERE rus_word_id = '".$rus_word_id."'
       ";
    $mysqli->query($sql_4);
    $error = mysqli_error($mysqli);
    if(strpos($error, 'Duplicate entry')>-1){
          insertQuery($new_word_name,$eng_word_id);
          deleteQuery($rus_word_id, $eng_word_id);
    }
    $mysqli->close();
}

function insertQuery($rus_name, $eng_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_5 = "
        SELECT rus_word_id FROM qirim_english_dictionary.rus_words 
        WHERE name = '".$rus_name."' LIMIT 1
        ";
    $rus_id = mysqli_fetch_all($mysqli->query($sql_5))[0][0];
    $sql_4 = "
        INSERT INTO  qirim_english_dictionary.`references`
        SET rus_word_id = '".$rus_id."', eng_word_id = '$eng_id'
        ";
    $mysqli->query($sql_4);
    $error = mysqli_error($mysqli);
    $mysqli->close();
}


function deleteRusName(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    if(!empty($_GET['deleteid'])){
        $rus_id = $_GET['deleteid'];
        $sql_5 = "
            DELETE  FROM  qirim_english_dictionary.rus_words 
            WHERE rus_word_id = '".$rus_id."'
            ";
        $mysqli->query($sql_5);
        $sql_4 = "
            DELETE  FROM  qirim_english_dictionary.`references`
            WHERE rus_word_id = '".$rus_id."'
            ";
        $mysqli->query($sql_4);
    }
    $mysqli->close();
}

function explodeWord($object){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $exploded = explode(', ',$object['rus_word']);
    
    for($i = 0; $i<count($exploded); $i++){
        $sql_3 = "
            INSERT INTO qirim_english_dictionary.rus_words 
            SET name = '".$exploded[$i]."', part_of_speech_id = '".$object['part_of_speech_id']."'
            ";
        $mysqli->query($sql_3);
        $sql_2 = "
            SELECT 
                rus_word_id
            FROM
                qirim_english_dictionary.rus_words
            WHERE
                name = '".$exploded[$i]."'  AND part_of_speech_id = '".$object['part_of_speech_id']."'
        ";
        $new_rus_word_id = mysqli_fetch_row($mysqli->query($sql_2))[0];
        $sql_4 = "
            INSERT INTO qirim_english_dictionary.`references` 
            SET eng_word_id = '".$object['eng_id']."', rus_word_id = '".$new_rus_word_id."'
            ";
        $mysqli->query($sql_4);
    }
    deleteQuery($object['rus_id'], $object['eng_id']);
    $mysqli->close();
}


function deleteQuery($rus_id, $eng_id){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_3 = "
        DELETE  FROM  qirim_english_dictionary.rus_words
        WHERE rus_word_id = '".$rus_id."'
        ";
    $mysqli->query($sql_3);
    $sql_4 = "
        DELETE  FROM  qirim_english_dictionary.`references`
        WHERE rus_word_id = '".$rus_id."' AND eng_word_id = '".$eng_id."'
        ";
    $mysqli->query($sql_4);
    $mysqli->close();
}

function editWord($word){
    global $query;
    $city = str_replace('г', "г.", $query);
    $new_word = trim(str_replace($query, $city, $word));
    return $new_word;
}

function putThatDone($data){
    $already_done = file_get_contents('done_commas.txt');
    if(!$already_done){
        file_put_contents('done_commas.txt', $data);
    } else {
        $already_done .= ','.$data;
        file_put_contents('done_commas.txt', $already_done);
    }
}

if(function_exists($_GET['f'])) {
   $_GET['f']();
}
<?php
$current_word_list = [];
$query = '';
$current_word = '';
function getList(){
    global $query;
    global $current_word;
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'WHERE rw.rus_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    
    $part_of_speech = 'adverb';
    
    $sql_2 = "
        SELECT 
            rus_word_id,
            name,
            COUNT(*) c,
            (SELECT 
                    GROUP_CONCAT(pos.eng_part_descr
                            SEPARATOR ';')
                FROM
                    rus_words rw1
                JOIN 
                                parts_of_speech pos USING(part_of_speech_id)
                WHERE
                    rw.name = rw1.name) AS part
        FROM
            rus_words rw
        $already_done
        GROUP BY name
        HAVING c > 1 AND part LIKE '%$part_of_speech' AND part LIKE '%$part_of_speech%' AND part LIKE '$part_of_speech%' AND part LIKE '$part_of_speech' 
        LIMIT 1;    
        ";
    
    $result = mysqli_fetch_all($mysqli->query($sql_2));
    $word_object = [
        'id' => $result[0][0],
        'name' => $result[0][1],
        'parts' => explode(';',$result[0][3])
    ];
    $current_word = $word_object['name'];
    echo json_encode($word_object);
    //echo json_encode(mysqli_fetch_all($mysqli->query($sql_2)));
}

function getTotal(){
    global $query;
    if(!empty($_GET['query'])){
        $query = $_GET['query'];
    }
    $already_done = file_get_contents('done_commas.txt');
    if(!empty($already_done)){
        $already_done = 'WHERE rus_word_id NOT IN ('.$already_done.') ';
    } else {
        $already_done = '';
    }
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    $sql_2 = "
         SELECT  name, COUNT(*) c FROM rus_words $already_done GROUP BY name HAVING c > 1 
        ";
    echo json_encode($mysqli->query($sql_2)->num_rows);
}

function choose(){
    $mysqli = new mysqli("127.0.0.1", "root", "root", "qirim_english_dictionary");
    $mysqli->set_charset("utf8");
    global $current_word;
    if(!empty($_GET['part_of_speech'])){
        $part_of_speech = explode(',', $_GET['part_of_speech']);
        $obj = [
            'part' => $part_of_speech[0],
            'id' => $part_of_speech[1]
        ];
        if($obj['part'] == 'ALL'){
            putThatDone($obj['id']);
            return getList();
        }
        $word = mysqli_fetch_all($mysqli->query("SELECT name FROM rus_words WHERE rus_word_id = '{$obj['id']}' LIMIT 1"))[0][0];
        $ids = mysqli_fetch_all($mysqli->query("SELECT  rus_word_id FROM rus_words rw JOIN parts_of_speech pos USING (part_of_speech_id)WHERE
                    pos.eng_part_descr != '{$obj['part']}'
                    AND rw.name = '$word'"))[0];
        $ids_n =  implode(',',$ids)  ;        
        $sql_2 = "
            DELETE FROM rus_words 
            WHERE
                rus_word_id IN ('{$ids_n}')
            ";
        $mysqli->query($sql_2);
        return getList();
    }
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
                'rus_word' => $data[$i][2]
            ];
            putThatDone($object['rus_word']);
            continue;
        }
        return getList();
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

function chastrechiRUS($string){

    /*
    Группы окончаний:
    1. прилагательные
    2. причастие
    3. глагол
    4. существительное
    5. наречие
    6. числительное
    7. союз
    8. предлог
   */

    $groups = array(
    1 => array ('ее','ие','ые','ое','ими','ыми','ей','ий','ый','ой','ем','им','ым','ом',
   'его','ого','ему','ому','их','ых','ую','юю','ая','яя','ою','ею'),
    2 => array ('ивш','ывш','ующ','ем','нн','вш','ющ','ущи','ющи','ящий','щих','щие','ляя'),
    3 => array ('ила','ыла','ена','ейте','уйте','ите','или','ыли','ей','уй','ил','ыл','им','ым','ен',
   'ило','ыло','ено','ят','ует','уют','ит','ыт','ены','ить','ыть','ишь','ую','ю','ла','на','ете','йте',
   'ли','й','л','ем','н','ло','ет','ют','ны','ть','ешь','нно'),
    4 => array ('а','ев','ов','ье','иями','ями','ами','еи','ии','и','ией','ей','ой','ий','й','иям','ям','ием','ем',
   'ам','ом','о','у','ах','иях','ях','ы','ь','ию','ью','ю','ия','ья','я','ок', 'мва', 'яна', 'ровать','ег','ги','га','сть','сти'),
    5 => array ('чно', 'еко', 'соко', 'боко', 'роко', 'имо', 'мно', 'жно', 'жко','ело','тно','льно','здо','зко','шо','хо','но'),
    6 => array ('чуть','много','мало','еро','вое','рое','еро','сти','одной','двух','рех','еми','яти','ьми','ати',
   'дного','сто','ста','тысяча','тысячи','две','три','одна','умя','тью','мя','тью','мью','тью','одним'),
    7 => array ('более','менее','очень','крайне','скоре','некотор','кажд','други','котор','когд','однак',
   'если','чтоб','хот','смотря','как','также','так','зато','что','или','потом','эт','тог','тоже','словно',
   'ежели','кабы','коли','ничем','чем'),
    8 => array ('в','на','по','из')
   );

   $res=array();
   $string=mb_strtolower($string);
   $words=explode(' ',$string);
   //print_r($words);
   foreach ($words as $wk=>$w){
       $len_w=mb_strlen($w);
    foreach ($groups as $gk=>$g){
     foreach ($g as $part){
       $len_part=mb_strlen($part);
      if (
           mb_substr($w,-$len_part)==$part && $res[$wk][$gk]<$len_part //любая часть речи, окончания
           || mb_strpos($w,$part)>=(round(2*$len_w)/5) && $gk==2 //причастие, от 40% и правее от длины слова
           || mb_substr($w,0,$len_part)==$part && $res[$wk][$gk]<$len_part && $gk==7 //союз, сначала слОва
           || $w==$part //полное совпадение
         ) {
            //echo $w.':'.$part."(".$gk.")<br>";
            if ($w!=$part) $res[$wk][$gk]=mb_strlen($part); else $res[$wk][$gk]=99;
           }

     }
    }
   if (!isset($res[$wk][$gk])) $res[$wk][$gk]=0;
   //echo "<hr>";
   }


   $result=array();
   foreach($res as $r) {
    arsort($r);
    array_push($result,key($r));
   }
   return $result;
}




if(function_exists($_GET['f'])) {
   $_GET['f']();
}
<?php
header('Content-Type: text/xml'); 

$xml = file_get_contents('dict_lite.xdxf');


$movies = new SimpleXMLElement($xml);
foreach ($movies->ar as $object){
    $word = $object->asXML();
    $data = [
        'origin' => '',
        'transcription' => '',
        'translation' => ''
    ];
    $origin = explode('</k>',$word);
    
    
    print_r($origin);
    die;
}
/*$xml_object = explode("</ar>", $xml);
for($i=0;$i<count($xml_object); $i++){
    print_r($xml_object[$i]);
}*/

<?php

function index(){
    set_time_limit(8000);
    $current_letter = [
        'A', 'B', 'C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' 
    ];
    for($i=0; $i < count($current_letter); $i++){
        $xml = file_get_contents('origin/dict_'.$current_letter[$i].'.xdxf');
        $xml_object = explode("<ar><k>", $xml);
        unset($xml_object[0]);

        $intervals = [
            '.,\/-', 'a-c', 'd-f', 'g-i', 'j-l', 'm-o', 'p-r', 's-u', 'v-z'
        ];
        $result_object = '';
        for($k=0; $k < count($intervals); $k++){
            foreach ($xml_object as $object){
                if(preg_match('/^('.$current_letter[$i].'{1}['.$intervals[$k].']{1})/i', $object)){
                    $object = '<ar><k>'.$object;
                    $result_object .= $object;
                    continue;
                } 
            }
            if($k == 0){
                $intervals[$k] = 'delims';
            }
            file_put_contents('origin/dict_'.$current_letter[$i].'_'.$intervals[$k].'.xdxf', $result_object);
            $result_object = '';
        }
    }
}

index();
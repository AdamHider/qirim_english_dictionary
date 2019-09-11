<?php

function index(){
    $word = 'светить';
    $data = getData($word);
}

function getData($word){
    $html = file_get_contents("https://gufo.me/dict/kuznetsov/$word");
    $start = strpos($html, "<p><span>");
    $finish_length = strlen(substr($html, strpos($html, '<div class="fb-quote"></div>')));
    $length = strlen($html) - $finish_length - $start;
    $needed_str = substr($html, $start, $length);
    $needed_array = explode('<p><span>',$needed_str);
    unset($needed_array[0]);
    $result = [];
    foreach($needed_array as &$item){
        $item = explode('<em>',$item)[0];
        $pattern = '/ [А-Я]{1}/u';
        preg_match_all($pattern, $item, $matches);
        $tmp = preg_split($pattern, $item);
        $tmp[1] = $matches[0][0].$tmp[1];
        $result[] = [
            'denotation_number' => explode('.', $tmp[0])[0],
            'transition' => trim(strip_tags(explode('</strong>',$tmp[0])[1])),
            'denotation' => trim($tmp[1])
        ];
    }
    print_r($result);
    die;
}

index();
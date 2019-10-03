<?php

function splitCSV($keepHeaders) {
    
        $path = './csv_export_files';
        mkdir($path);
        //$outputPath = $path.'/file';
        $csv = new SplFileObject($csvPath);
        $csv->setFlags(SplFileObject::READ_CSV);
        foreach(new LimitIterator($csv, 0, 30000) as $num =>$line)
        {
                $data[$num] = $line;
        }
        $fp = fopen('file1.csv', 'w');
        foreach ($data as $fields) 
        {
                fputcsv($fp, $fields);
        }
        fclose($fp);
    
    
}
function fsplit($parts=2){
    $source_file="https://happywear.ru/exchange/xml/price-list.csv";
    $tmpfile = './happy_exchange'.rand(0,1000);//tempnam("/tmp", "tmp_");
    if(!copy($source_file, $tmpfile)){
        die("Downloading failed");
    };
    
    //open file to read
    $file_handle = fopen($tmpfile,'r');
    //get file size
    $file_size = filesize($tmpfile);
    //no of parts to split
    $buffer = $file_size / $parts;
    //store all the file names
    $file_parts = array();
    //path to write the final files
    $store_path = "./csv_tmp_files";
    
    if(!realpath($store_path)){
        mkdir($store_path);
    }
    //name of input file
    $file_name = basename($tmpfile);
    
    $store_path .= '/';
    $result = [];
    for($i=0;$i<$parts;$i++){
        //read buffer sized amount from file
        $file_part = fread($file_handle, $buffer);
        //the filename of the part
        $file_part_path = $store_path.$file_name.".part$i.csv";
        $result[] = $file_part_path;
        //open the new file [create it] to write
        $file_new = fopen($file_part_path,'w+');
        //write the part of file
        fwrite($file_new, $file_part);
        //add the name of the file to part list [optional]
        array_push($file_parts, $file_part_path);
        //close the part file handle
        fclose($file_new);
    }    
    //close the main file handle
    fclose($file_handle);
    
    unlink($tmpfile);
    return $result;
}

print_r(fsplit());
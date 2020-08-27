<?php
function require_search_files($start,$end){
    $files = [];
    for ($i=$start;$i<=$end;$i++){
        $files [] = fopen("doc{$i}.txt","r");
    }
    return $files;
}
function is_char($char){
    return (($char>="A"&& $char<="Z")||($char>="a"&& $char<="z"))?1:0;
}
function count_text_char($str,$a=null){
    $count =0;
    $str = str_split($str);
    foreach ($str as $char){
        if($a){
            if($char === $a)
                $count++;
        }
        else{
            if(is_char($char))$count++;
        }
    }
    return $count;
}
function processQuery(&$query){
    $data = [];
    $q = explode(' ',$query);
    foreach ($q as $element){
        $keyVal = explode(':',$element);
        $name = isset($keyVal[0])? $keyVal[0]:'';
        $val = isset($keyVal[1])? $keyVal[1]:'';
        $data[$name]=is_numeric($val)? $data[$name]=$val: 0;
    }
    $query = $data;
}
function get_entire_file($file){
    $str =fgets($file);
    while(!feof($file))
        $str .=" ".fgets($file);
    return $str;
}
function frequentChar($file,$query){
    $text=get_entire_file($file);
    $freqFileQuery = [];
    $totalChar = count_text_char($text);
    foreach ($query as $key => $data){
        $freqFileQuery[$key] = count_text_char($text,$key)/$totalChar;
    }
    return $freqFileQuery;
}
function allFilesFrequent($files,$query){
    $freq = [];
    foreach ($files as $file)
        $freq[] = frequentChar($file,$query);
    return $freq;
}
function rank_docs($query,$fileFreq){
    $score = array_fill(0,count($fileFreq),0);
    foreach ($query as $i_key => $i_val){
        foreach ($fileFreq as $key => $data) {
            foreach ($data as $d_key => $d_val) {
                if($i_key === $d_key) {
                    $score[$key] += $i_val * $d_val;
                }
            }
        }
    }
    arsort($score);
    return $score;
}
function print_div($ranked_docs,$files){
    foreach ($ranked_docs as $key => $val)
    {
        $current =$key+1;
        $cont =fread($files[$key],20);
        if(fgets($files[$key])) $cont .= '...';
        echo "<a href='doc{$current}.txt' style='font-size: x-large'>Document {$current}</a><br><span style='font-size: medium;font-weight: normal' >{$cont}</span><br></br>";
    }
}
function filter(&$score){
    foreach ($score as $key => $item)
        if (!$item) unset($score[$key]);
}
function check_results($set){
    echo empty($set)?'<span style="font-weight: normal">No results found.</span>':'';
}
function close_search_files($files){
    foreach ($files as $file)
        fclose($file);
}
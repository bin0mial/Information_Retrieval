<?php
function require_search_files($start,$end){
    $files = [];
    for ($i=$start;$i<=$end;$i++){
        $files [] = fopen("doc{$i}.txt","r");
    }
    return $files;
}
function files_to_text($files){
    $texts =[];
    foreach ($files as $file)
        $texts[] = get_entire_file($file);
    return $texts;
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
function processQuery($query){
    $data = explode(' ',$query);
    $new_data = [];
    foreach($data as $char)
    {
        if(array_key_exists($char,$new_data))
        {
            $new_data[$char]++;
        }
        else{
            $new_data[$char] = 1;
        }
    }
    return $new_data;
}
function get_entire_file($file){
    $str =fgets($file);
    while(!feof($file))
        $str .=" ".fgets($file);
    return $str;
}
function get_max_char($text){
    $data = processQuery($text);
    return max($data);
}
function get_whole_chars($files,$query){
    $chars = [];
    $query = str_split($query);
    foreach ($query as $value){
        if(is_char($value) && !isset($chars[$value]))
            $chars[$value]='';
    }
    foreach ($files as $file){
        $temp = str_split($file);
        foreach ($temp as $value){
            if(is_char($value) && !isset($chars[$value])){
                $chars[$value]='';
            }
        }
    }
    ksort($chars);
    return $chars;
}
function frequentChar($text,$chars){
    $freqFileQuery = [];
    $max_char_count = get_max_char($text);
    foreach ($chars as $key => $data){
        $freqFileQuery[$key] = count_text_char($text,$key)/$max_char_count;
    }
    return $freqFileQuery;
}
function allFilesFrequent($files,$chars,$query){
    $freq = [];
    $freq[] = frequentChar($query,$chars);
    foreach ($files as $file)
        $freq[] = frequentChar($file,$chars);
    return $freq;
}
function allFilesIDF($files, $chars, $query){
    $idf = [];
    $all_docs=[];
    $total_documents = count($files)+1;
    $all_docs[] =$query;
    $all_docs=array_merge($all_docs,$files);
    $count= $chars;
    foreach ($count as $key => $value){
        $count[$key]=0;
    }
    foreach ($chars as $char => $val){
        foreach ($all_docs as $key => $document){
            if(strpos($document,$char)!==false)
                $count[$char]++;
        }
    }
    foreach ($count as $item => $value){
        $idf[$item] = log($total_documents/$value,2);
    }
    return $idf;
}
function get_tf_idf($tf, $idf){
    $tf_idf = [];
    foreach ($idf as $char => $value) {
        foreach ($tf as $num => $arr){
            foreach ($arr as $char1 => $value1)
            {
                if ($char === $char1)
                    $tf_idf[$num][$char1]= $value*$value1;
            }
        }
    }
    return $tf_idf;
}
function cos_sim($tf_idfs){
    $cos_sim =[];
    $query= $tf_idfs[0];
    unset($tf_idfs[0]);
    foreach ($tf_idfs as $num => $arr){
        $top=0;
        $bot=0;
        $bot1=0;
        foreach ($query as $char => $val){
            foreach ($arr as $char1 => $value){
                if($char1 === $char){
                    $top += $value*$val;
                    $bot += pow($val,2);
                    $bot1 += pow($value,2);
                }
            }
        }
        $cos_sim[$num]= $bot&&$bot1?$top/(sqrt($bot*$bot1)):0;
    }
    return $cos_sim;
}
function rank_docs($score){
    arsort($score);
    return $score;
}
function print_div($ranked_docs,$files){
    foreach ($ranked_docs as $key => $val)
    {
        $current_file=$files[$key-1];
        $cont =strlen($current_file)>=20?substr($current_file, 0, 20)."...":$current_file;
        echo "<a href='doc{$key}.txt' style='font-size: x-large'>Document {$key}</a><br><span style='font-size: medium;font-weight: normal' >{$cont}</span><br></br>";
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
function show($num, $data){
    switch ($num){
        case 1:
            echo "<span style='color: red;font-size: x-large'>Getting tf of documents and query: </span><br>";
            echo "<span style='color: blue;'>First is query .. the rest are Files<br>[document number] => tf</span><br>";
            print_r($data);
            echo "<br><br>";
            break;
        case 2:
            echo "<span style='color: red;font-size: x-large''>Getting idf of documents and query: </span><br>";
            echo "<span style='color: blue;'>[Character] => idf</span><br>";
            print_r($data);
            echo "<br><br>";
            break;
        case 3:
            echo "<span style='color: red;font-size: x-large''>Getting tf-idf of files and query </span><br>";
            echo "<span style='color: blue;'>First is query .. the rest are Files<br>[document number] => tf-idf</span><br>";
            print_r($data);
            echo "<br><br>";
            break;
        case 4:
            echo "<span style='color: red;font-size: x-large''>Applying CosSim between documents and query: </span><br>";
            echo "<span style='color: blue;'>[document number] => CosSim(doc,query)</span><br>";
            print_r($data);
            echo "<br><br>";
            break;
        case 5:
            echo "<span style='color: red;font-size: x-large''>Ranked Docs: </span><br>";
            echo "<span style='color: blue;'>[document number] => CosSim(doc,query)</span><br>";
            print_r($data);
            echo "<br><br>";
            break;
        case 6:
            echo "<span style='color: red;font-size: x-large''>After filtering Ranked Docs: </span><br>";
            echo "<span style='color: blue;'>[document number] => CosSim(doc,query)</span><br>";
            print_r($data);
            echo "<br><br>";
            break;
        default:
            echo'';
    }
}
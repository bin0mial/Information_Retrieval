<?php
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
function allFilesIDF($files, $chars){
    $idf = [];
    $total_documents = count($files);
    $all_docs=$files;
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
function frequentChar($text,$chars){
    $freqFileQuery = [];
    $max_char_count = get_max_char($text);
    foreach ($chars as $key => $data){
        $freqFileQuery[$key] = count_text_char($text,$key)/$max_char_count;
    }
    return $freqFileQuery;
}
function allFilesFrequent($files,$chars){
    $freq = [];
    foreach ($files as $file)
        $freq[] = frequentChar($file,$chars);
    return $freq;
}
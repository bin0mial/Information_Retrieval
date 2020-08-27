<?php
function newChanges($newFiles){
    for ($i="A";$i<="E";$i++){
        $filesss= fopen("{$i}.txt","w");
        fwrite($filesss,$newFiles[$i]);
    }
    fclose($filesss);
}
function require_search_files($start,$end){
    $files = [];
    for ($i=$start;$i<=$end;$i++){
        $files [$i] = fopen("{$i}.txt","r");
    }
    return $files;
}
function files_to_text($files){
    $texts =[];
    foreach ($files as $i => $file)
        $texts[$i] = get_entire_file($file);
    return $texts;
}
function is_char($char){
    return (($char>="A"&& $char<="Z")||($char>="a"&& $char<="z"))?1:0;
}

function get_entire_file($file){
    $str =fgets($file);
    while(!feof($file))
        $str .=" ".fgets($file);
    return $str;
}
function get_whole_chars($files){
    $chars = [];
//    foreach ($files as $file){
//        $temp = str_split($file);
//        foreach ($temp as $value){
//            if(is_char($value) && !isset($chars[$value])){
//                $chars[$value]='';
//            }
//        }
//    }
    for ($i="A";$i<="E";$i++){
        $chars[$i]='';
    }
    ksort($chars);
    return $chars;
}

function text_filteration($texts){
    foreach ($texts as $key => $text)
    {
        $chars = explode(' ', $text);
        foreach ($chars as $ckey => $char){
            !isset($found[$char])?$found[$char]=1: $found[$char]++;
            if($char==$key || $found[$char]>=2)
                unset($chars[$ckey]);
        }
        $texts[$key]=join(' ',$chars);
        unset($found);
    }
    return $texts;
}
function processText($text){
    $data = explode(' ',$text);
    $new_data = [];
    foreach($data as $char)
    {
        if($char>="A" && $char<="E") {
            if (array_key_exists($char, $new_data)) {
                $new_data[$char]++;
            } else {
                $new_data[$char] = 1;
            }
        }
    }
    return $new_data;
}

function get_adjacent_matrix($texts,$chars){
    $adj_matrix = [];
    foreach ($texts as $key =>$text){
        $adj_matrix[$key]=processText($text);
    }
    foreach($adj_matrix as $key => $adjchar) {
        foreach ($chars as $char => $val) {
            if (!isset($adj_matrix[$key][$char]))
                $adj_matrix[$key][$char] = 0;
        }
        ksort($adj_matrix[$key]);
    }
    return $adj_matrix;
}

function get_t_adjacent_matrix($adj_matrix){
    $t_adj_matrix =[];
    foreach ($adj_matrix as $row => $columns){
        foreach ($columns as $column => $value){
                $t_adj_matrix[$column][$row]=$value;
        }
    }
    return $t_adj_matrix;
}

function initialize_hubs($texts){
    $hubs=[];
    foreach ($texts as $char => $text){
        $hubs[$char]=1;
    }
    return $hubs;
}

function mul_mat($mat1,$mat2){
    $result_mat=[];
    $modified_mat=[];
    foreach ($mat1 as $row1 => $columns){
        foreach ($columns as $column => $value1) {
            foreach ($mat2 as $row2 => $value2) {
                if ($column == $row2) {
                    $result_mat[$row1][$row1] = 0;
                }
            }
        }
    }
    foreach ($mat1 as $row1 => $columns){
        foreach ($columns as $column => $value1){
            foreach ($mat2 as $row2 => $value2){
                if ($column==$row2){
                    $result_mat[$row1][$row1]+=($value1*$value2);
                }
            }
        }
    }
    foreach ($result_mat as $key => $value) {
        foreach ($value as $key1 => $value1) {
            if ($key == $key1)
                $modified_mat[$key] = $value1;
        }
    }
    return $modified_mat;
}
function normalize(&$mat){
    $cmat=0;
    foreach ($mat as $value){
        $cmat+= pow($value,2);
    }
    $cmat=sqrt($cmat);
    foreach ($mat as $key => $value){
        if($cmat!=0.0)
            $mat[$key]=$value/$cmat;
        else
            $mat[$key]=0;
    }
}
function get_hubs_auth($adj_mat, $t_adj_mat,$ini_hubs ,$iterations){
    $h=$ini_hubs;
    $a=$ini_hubs;
    for ($i=0 ; $i<$iterations; $i++){
        $a=mul_mat($t_adj_mat, $h);
        $h=mul_mat($adj_mat, $a);
        normalize($h);
        normalize($a);
        if($_SESSION['show']) {
            $output['hubs'] = $h;
            $output['auth'] = $a;
            $current_it = $i + 1;
            echo "Iteration #$current_it<br>";
            show($output, "HUBS_AUTH");
        }
    }
    $h_a_mat['hubs']=$h;
    $h_a_mat['auth']=$a;
    return $h_a_mat;
}

function rank_docs($score){
    arsort($score);
    return $score;
}

function close_search_files($files){
    foreach ($files as $file)
        fclose($file);
}
function show($data,$type){
    switch ($type) {
        case "FILTER":
            echo "<span style='font-weight: bold; font-size: larger'>Filtered texts:</span><br>";
            echo "<table border='1' style='border-color: skyblue'>";
            echo "<th>File</th><th>Content</th>";
            foreach ($data as $file => $internal){
                echo "<tr><td style='font-weight: bold'>$file</td><td>$internal</td></tr>";
            }
            echo "</table><br>";
            break;
        case "ADJACENT_MATRIX":
        case "T_ADJACENT_MATRIX":
            echo "<span style='font-weight: bold; font-size: larger'>";
            echo $type==="ADJACENT_MATRIX"?"Adjacent matrix":"Transposed adjacent matrix";
            echo "</span><br>";
            echo "<table>";
            echo "<th></th>";
            foreach ($data as $header => $content){
                echo "<th>$header</th>";
            }
            foreach ($data as $file => $internal){
                echo "<tr><td style='font-weight: bold'>$file</td>";
                foreach ($internal as $key => $data){
                    echo "<td>$data</td>";
                }
                echo "</tr>";
            }
            echo "</table><br>";
            break;
        case "HUBS_AUTH":
            echo "<span style='font-weight: bold; font-size: larger'>Hubs and authorities:</span><br>";
            echo "<table>";
            echo "<th></th><th>Hubs</th><th>Authority</th>";
            foreach ($data['hubs'] as $fnamehubs => $fvalhubs){
                echo "<tr><td style='font-weight: bold'>$fnamehubs</td><td>$fvalhubs</td>";
                foreach ($data['auth'] as $fnameauth => $fvalauth){
                    if($fnameauth==$fnamehubs){
                        echo "<td>$fvalauth</td>";
                    }
                }
                echo "<tr>";
            }
            echo "</table><br>";
            break;
    }
}
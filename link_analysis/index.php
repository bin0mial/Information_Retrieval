<?php require "require_files.php";
session_start();
$_SESSION['show']=isset($_SESSION['show'])?$_SESSION['show']:0;
//Reading files from .. to
$prefiles = require_search_files("A", "E");

//Change file to text
$text_files = files_to_text($prefiles);

close_search_files($prefiles);
?>
<html>
<head>
    <title>Link Analysis</title>
</head>
<body>
<center>
    <form method="post">
        <table>
            <tr>
                <td>A:</td><td><input name="a" value="<?php echo $text_files['A'] ?>" required></td>
            </tr>
            <tr>
                <td>B:</td><td><input name="b" value="<?php echo $text_files['B'] ?>" required></td>
            </tr>
            <tr>
                <td>C:</td><td><input name="c" value="<?php echo $text_files['C'] ?>" required></td>
            </tr>
            <tr>
                <td>D:</td><td><input name="d" value="<?php echo $text_files['D'] ?>" required></td>
            </tr>
            <tr>
                <td>E:</td><td><input name="e" value="<?php echo $text_files['E'] ?>" required></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" value="Test files" name="test"></td>
            </tr>
        </table>
    </form>
    <?php if(isset($_POST["details"])){$_SESSION["show"] = $_SESSION["show"]==1 ? 0:1;}?>
    <form method="post">
    <input type="submit" style="background: <?php echo $_SESSION['show']?'orange':'deepskyblue'; ?>" value="<?php echo $_SESSION['show']?'Hide Details':'Show Details'; ?>" name="details"/>
    </form>
</center>
<h2 style="margin-left: 1%">Results:</h2>
<div name="results" style="margin-left: 4%">
    <h3>
<?php
if(isset($_POST['test'])) {
        $changeFiles['A'] = $_POST['a'];
        $changeFiles['B'] = $_POST['b'];
        $changeFiles['C'] = $_POST['c'];
        $changeFiles['D'] = $_POST['d'];
        $changeFiles['E'] = $_POST['e'];
        newChanges($changeFiles);
    header("Location: ../link_analysis");
    }
   $show = $_SESSION["show"];

    //Reading files from .. to
    $files = require_search_files("A", "E");

    //Change file to text
    $text_files = files_to_text($files);

    //Closing files read files
    close_search_files($files);

    //Get all characters found in documents and query
    $chars = get_whole_chars($text_files);

    //Filter files from same link
    $new_texts = text_filteration($text_files);
    if ($show) show($new_texts, "FILTER");

    //Getting adjacent matrix
    $adjacent_matrix = get_adjacent_matrix($new_texts, $chars);
    if ($show) show($adjacent_matrix, "ADJACENT_MATRIX");

    //Getting transposed adjacent matrix
    $t_adjacent_matrix = get_t_adjacent_matrix($adjacent_matrix, $chars);
    if ($show) show($t_adjacent_matrix, "T_ADJACENT_MATRIX");

    //Initialize hubs
    $hubs = initialize_hubs($new_texts);

    //Calculating final authority and hubs
    $final_hubs_auth = get_hubs_auth($adjacent_matrix, $t_adjacent_matrix, $hubs, 20);
    show($final_hubs_auth, "HUBS_AUTH");
?>
    </h3>
</div>
</body>
</html>
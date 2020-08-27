<?php require "require_files.php";
session_start();
$_SESSION['show']=isset($_SESSION['show'])?$_SESSION['show']:0;
?>
<html>
<head>
    <title>Vector Space Model Search</title>
</head>
<body>
<form method="get">
    <center>
        <h1>Vector Space Model Search</h1>
        <p>Please type search in the following view:<br>"A A A B B C ..."</p>
        <div>
            <input name="query" value="<?php echo isset($_GET['query'])?$_GET['query']:''; ?>" required/>
            <input type="submit" value="Search" name="subReq"/>
        </div>
        <br>
        <?php if(isset($_POST["details"])){$_SESSION["show"] = $_SESSION["show"]==1 ? 0:1;}?>
            <input type="submit" style="background: <?php echo $_SESSION['show']?'orange':'deepskyblue'; ?>" value="<?php echo $_SESSION['show']?'Hide Details':'Show Details'; ?>" name="details" formmethod="post"/>
    </center>
</form>
<h2 style="margin-left: 1%"><?php echo isset($_GET['subReq'])?"Results:":'' ?></h2>
<div name="results" style="margin-left: 4%">
    <h3>
<?php
    if(isset($_GET['subReq'])) {
        $show =$_SESSION["show"];
        //Reading query
        $query = $_GET['query'];

        //Reading files from .. to
        $files = require_search_files(1,5);

        //Change file to text
        $text_files = files_to_text($files);

        //Closing files read files
        close_search_files($files);

        //Get all characters found in documents and query
        $chars = get_whole_chars($text_files,$query);

        //Get files tf
        $fileFreq = allFilesFrequent($text_files, $chars, $query);
        if($show)show(1, $fileFreq);

        //Get files idf
        $file_idf = allFilesIDF($text_files, $chars, $query);
        if($show)show(2, $file_idf);

        //Get files tf-idf
        $tf_idf = get_tf_idf($fileFreq, $file_idf);
        if($show)show(3, $tf_idf);

        //Applying CosSim
        $docs_cos_sim = cos_sim($tf_idf);
        if($show)show(4, $docs_cos_sim);

        //Ranking documents
        $ranked_docs = rank_docs($docs_cos_sim);
        if($show)show(5, $ranked_docs);

        //Filtering ranked documents (remove un relevant documents)
        filter($ranked_docs);
        if($show)show(6, $ranked_docs);

        //Printing relevant documents
        print_div($ranked_docs,$text_files);

        //Check if there is a result returned or not
        check_results($ranked_docs);
    }
?>
    </h3>
</div>
</body>
</html>
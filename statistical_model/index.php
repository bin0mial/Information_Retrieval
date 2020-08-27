<?php require "require_files.php"; ?>
<html>
<head>
    <title>Statistical Model Search</title>
</head>
<body>
<form method="get">
    <center>
        <h1>Statistical Model Search</h1>
        <p>Please type search in the following view:<br>"A:0.2 B:0.9 C:0.8 ..."</p>
        <div>
            <input name="query" value="<?php echo isset($_GET['query'])?$_GET['query']:''; ?>" required/>
            <input type="submit" value="Search" name="subReq"/>
        </div>
    </center>
</form>
<h2 style="margin-left: 1%"><?php echo isset($_GET['subReq'])?"Results:":'' ?></h2>
<div name="results" style="margin-left: 4%">
    <h3>
<?php
    if(isset($_GET['subReq'])) {
        $query = $_GET['query'];
        processQuery($query);
        $files = require_search_files(1,5);
        $fileFreq = allFilesFrequent($files,$query);
        close_search_files($files);
        $ranked_docs = rank_docs($query,$fileFreq);
        $data = require_search_files(1,5);
        filter($ranked_docs);
        print_div($ranked_docs,$data);
        close_search_files($data);
        check_results($ranked_docs);
    }
?>
    </h3>
</div>
</body>
</html>
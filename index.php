<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>

    <link rel="stylesheet" type="text/css" href="src/datatables.min.css"/>
    <link rel="stylesheet" href="src/bootstrap.min.css">
    <style>
        h1{
            font-size: 10rem;
            text-align: center;
            margin: 1rem;
        }
    </style>

</head>

<body>
<div class="container">
<h1>Movies</h1>
<table id="table" class="table  table-striped" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>File</th>
        <th>Year</th>
        <th>Created</th>
        <th>Last seen</th>
        <th>Directory</th>
        <th>Size</th>
        <th>File edited</th>
    </tr>
    </thead>
    <tbody>
    <?php
    function human_filesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    require_once "Databaze.php";
    Databaze::pripoj('localhost', 'root', 'root', 'hdd');
    $query = Databaze::dotaz("SELECT * FROM files left join directories on files.directory_id=directories.id");
    $files = $query->fetchAll();
    foreach ($files as $file){
        echo "<tr>";
        echo "<td>".$file["file"]."</td>";
        echo "<td>".$file["year"]."</td>";
        echo "<td>".$file["created"]."</td>";
        echo "<td>".$file["last_seen"]."</td>";
        echo "<td>".$file["directory"]."</td>";
        echo "<td>".human_filesize($file["size_bytes"])."</td>";
        echo "<td>".$file["file_edited"]."</td>";


        echo "</tr>";
    }


    ?>


    </tbody>


</div>
<script type="text/javascript" src="src/jQuery-2.2.4/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="src/Bootstrap-3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="src/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                "pageLength": 100
            });
        } );
    </script>
</body>

</html>
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
        .db-update{
            font-size: 20px;
            margin-bottom: 10px;
            display: inline-block;
        }
    </style>

</head>

<body>
<?php
function human_filesize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

require_once "Databaze.php";
Databaze::pripoj('localhost', 'root', 'root', 'hdd');
$query = Databaze::dotaz("SELECT max(last_seen) as 'last' FROM files");
$last_seen = $query->fetch();
date_default_timezone_set("Europe/Prague");
$last_seen = new DateTime($last_seen["last"]);
$now = new DateTime();
$diff = $last_seen->diff($now)->format("%d");
$label_color = ($diff > 3) ? "label-danger" : "label-info";
?>
<div class="container">
<h1>Movies</h1>

    <h4>Last DB update </h4>
    <span class="label <?=$label_color?> db-update"><?php echo $last_seen->format("d.m.y H:m") ?></span>
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
    $query = Databaze::dotaz("SELECT * FROM files left join directories on files.directory_id=directories.id");
    $files = $query->fetchAll();
    foreach ($files as $file){
        echo "<tr>";
        echo "<td>".$file["file"]."</td>";
        echo "<td>".$file["year"]."</td>";
        $created = new DateTime($file["created"]);
        echo "<td><span data-toggle='tooltip' title='".$created->format("H:m:s")."'> ".$created->format("d.m.y")."</span></td>";
        $last = new DateTime($file["last_seen"]);
        echo "<td><span data-toggle='tooltip' title='".$last->format("H:m:s")."'> ".$last->format("d.m.y")."</span></td>";
        echo "<td>".$file["directory"]."</td>";
        echo "<td>".human_filesize($file["size_bytes"])."</td>";
        $edit = new DateTime($file["file_edited"]);
        echo "<td><span data-toggle='tooltip' title='".$edit->format("H:m:s")."'> ".$edit->format("d.m.y")."</span></td>";
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

            $('[data-toggle="tooltip"]').tooltip();

        } );
    </script>
</body>

</html>
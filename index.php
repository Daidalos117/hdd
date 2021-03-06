<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movies on HDD</title>

    <link rel="stylesheet" type="text/css" href="src/datatables.min.css"/>
    <link rel="stylesheet" href="src/bootstrap.min.css">
    <link rel="stylesheet" href="src/style.css">


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
<div class="background-layer"></div>
<div class="container">
    <div class="col-md-12">
        <h1>Movies</h1>

        <p>Last DB update </p>

        <div class="row">
            <div class="col-md-6">
                <span class="label <?=$label_color?> db-update"><?php echo $last_seen->format("d.m.y H:m") ?></span>
            </div>
            <div class="col-md-6">
                <div class="random-el">
                    <button class="btn btn-danger random-btn" id="random-btn">Random</button>
                    <a id="reset" class="hidden">Reset</a>
                </div>
            </div>
        </div>


        <table id="table" class="table  table-striped table-responsive" >
            <thead>
            <tr>
                <th>ID</th>
                <th>File</th>
                <th>Year</th>
                <th>Created</th>
                <th>Last seen</th>
                <th>Directory</th>
                <th>Size</th>
                <th>File edited</th>
                <th></th>
                <th><a href="#" id="search-favorite" ><img src="img/favorite.svg" class="animated"> </a> </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php

            $hiddenSql = (!isset($_GET['hidden'])) ? 'hidden != 1' : 'hidden in(0,1)';
            $query = Databaze::dotaz("SELECT * FROM files left join directories on files.directory_id=directories.id where ".$hiddenSql." order by file_edited DESC");
            $files = $query->fetchAll();
            foreach ($files as $file){
                $id = $file[0];
                $hiddenClass = ($file['hidden'] == 1) ? 'hidden-row' : '';
                echo "<tr data-id='".$id."' class='".$hiddenClass."'>";
                echo "<td>".$id."</td>";
                echo "<td>
                        <a class='movie-link' target='_blank' href='https://www.google.cz/#q=".urlencode($file["file"])."'>"
                        .$file["file"].
                    "</a></td>";

                echo "<td>".$file["year"]."</td>";
                $created = new DateTime($file["created"]);
                echo "<td><span data-toggle='tooltip' title='".$created->format("H:m:s")."'> ".$created->format("d.m.y")."</span></td>";
                $last = new DateTime($file["last_seen"]);
                echo "<td><span data-toggle='tooltip' title='".$last->format("H:m:s")."'> ".$last->format("d.m.y")."</span></td>";
                echo "<td>".$file["directory"]."</td>";
                echo "<td>".human_filesize($file["size_bytes"])."</td>";
                $edit = new DateTime($file["file_edited"]);
                echo "<td >
                    <span data-toggle='tooltip' title='".$edit->format("H:m:s")."'>
                    ".$edit->format("d.m.y")."</span>
                </td>";
                echo "<td class='checked-".$id."'>".$file["checked"]."</td>";
                $checked = ($file["checked"]) ? "checked" : "";
                echo '<td><div class="material-switch">
                            <input id="switch-'.$id.'" name="switch-'.$id.'" data-id="'.$id.'" '.$checked.' class="input-switch" type="checkbox"/>
                            <label for="switch-'.$id.'" class="label-success"></label>
                        </div></td>';
                echo '<td>
                        <a href="#" id="hide-this" data-id="'.$id.'">
                            <img src="img/visibility.svg" alt="" class="visibility-img">
                        </a>
                      </td>
                        ';
                echo "</tr>";
                $lastId = $file[0];
            }

            ?>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript" src="src/jQuery-2.2.4/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="src/DataTables-1.10.13/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="src/DataTables-1.10.13/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="src/Bootstrap-3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="src/moment.min.js"></script>
<script type="text/javascript" src="src/datetime-moment.js"></script>
    <script>
        $(document).ready(function() {


            $.fn.dataTable.moment( 'dd.mm.YY' );
            $.fn.dataTable.moment( 'dd.mm.YY' );
            $.fn.dataTable.moment( 'dd.mm.YY' );

            var favorite = false;
            var clickedRandom = false;
            var table = $('#table').DataTable({
                responsive: true,
                "pageLength": 100,
                "order": [],
                columnDefs: [ { orderable: false, targets: [8,9, 10] } ],
            });

            $('#random-btn').on('click', function () {
                var random = Math.floor(Math.random() * <?= $lastId ?>) + 1;
                var reg = "^\\s*"+random+"\\s*$";
                resetTable();
                table
                    .column( 0 )
                    .search( reg, true)
                    .draw();
                clickedRandom = true;
            } );

            $('#search-favorite').on('click', function (e) {
                e.preventDefault();
                //$(this).find("img").addClass("pulse");

                animate($(this).find("img").get(0),"pulse");
                var search = favorite ? "" : 1;
                table
                    .column( 8 )
                    .search( search )
                    .draw();
                favorite = !favorite;
            } );


            $("#reset").on("click",function () {
                resetTable();
            })
            $("#table_filter").on("click",function () {
                if(clickedRandom){
                    resetTable();
                    clickedRandom = false;
                }
            })
            function resetTable() {
                table
                    .search("")
                    .columns().search("")
                    .draw();
            }


            $('[data-toggle="tooltip"]').tooltip();
            //favorite click
            $("#table").on("click",".input-switch",function(){
                var id = $(this).data("id");
                var checked = $(this).prop("checked");
                var json = "id="+id+"&checked="+checked+"&type=check";
                $("td.checked-"+id).html(Number(checked));

                var tr = $('#table tr[data-id='+id+']');
                setTimeout(function () {
                    table
                        .rows(  )
                        .invalidate()
                        .draw();
                },1000);
                $.ajax({url: "ajax.php", type: "POST", data: json });
            });

            $("#table").on("click","#hide-this",function(){
                var id = $(this).data("id");
                var tr = $('#table tr[data-id='+id+']');
                var json = "id="+id+"&type=hide";
                console.log(tr);
                tr.hide(500);
                setTimeout(function () {
                    table
                        .rows(  )
                        .invalidate()
                        .draw();
                    tr.hide();
                },1000);
                $.ajax({url: "ajax.php", type: "POST", data: json });
            });



            function animate(element, animation){
                element.classList.remove(animation);
                void element.offsetWidth;
                element.classList.add(animation);
                element.addEventListener("animationend", function(){
                    element.classList.remove(animation);
                }, false);
            }
        } );
    </script>
</body>

</html>

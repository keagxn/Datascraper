<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getdata($date){

    $url = 'https://api.opencovid.ca/timeseries?stat=cases&loc=prov&date=' . $date ;
    $data = file_get_contents($url);
    $data = json_decode($data, true);
    
}

//if they submit
if(isset($_POST['submitbutton'])){

    $date = $_POST['date'];
    $date = date('d-m-y', stryotime ("-1 days"));
//default date
}else{

    date('d-m-y', strtotime("-1 days"));

}

$data = getdata($date);

    $url = 'https://api.opencovid.ca/timeseries?stat=cases&loc=prov&date=' . $date ;
    $data = file_get_contents($url);
    $data = json_decode($data, true);

    return $data;

    //echo '<pre>';
  //  print_r ($data);
  //  echo '</pre>';

?>

<html>
    <head>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>

    <body>

        <div class = "container">
        <h1> Covid track </h1>

        <form method="POST" action="<?php echo $_SERVER ['PHP_SELF'];?>">

        <input type = "date" name = "date" value= "<?php echo date('y-m-d', strototime($date)); ?>">
        <input type = "submit" name = "submitbutton">

        </form>

        <table class="table table-striped">

            <thead>
                <th>province </th>
                <th> <?php echo $date ; ?> </th>
                <th>cumulative cases </th>
            </thead>
            <tbody>
                <?php
                    for($c = 0; $c < count($data['cases']); $c++){

                        echo '<tr><td>' . $data['cases'][$c]['province'] . '</td>';
                        echo '<td>' .  $data['cases'][$c]['cases'] . '</td>';
                        echo '<td>' . $data['cases'][$c]['cumulative_cases'] . '</td></tr>';

                    }
                ?>
            </tbody>



    </body>
    </head>
</html>
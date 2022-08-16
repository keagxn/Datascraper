<?php
//Create Sum Arrays
$sumDate = array();
$sumDate2 = array();
$sumChange = array();
$sumCum = array();
 
//Get First Day Data
function getData($date){
    $url = "https://api.opencovid.ca/timeseries?stat=cases&loc=prov&date=" . $date;
    $data = file_get_contents($url);
    $data = json_decode($data, true);
    
    return $data;
}
//Get Second Day Data
function getData2($date2){
    $url2 = "https://api.opencovid.ca/timeseries?stat=cases&loc=prov&date=" . $date2;
    $data2 = file_get_contents($url2);
    $data2 = json_decode($data2, true);
    
    return $data2;
}
 
//If they hit submit, grab the date they entered
if(ISSET($_POST['submitButton'])){
    $date = $_POST['date'];
    $datex = $date;
    $date = date('d-m-Y', strtotime($date));
 
    $date2 = $_POST['date2'];
    $datey = $date2;
    $date2 = date('d-m-Y', strtotime($date2));
}
//If they don't enter date, set to default (yesterday)
else{
    $date = date('d-m-Y', strtotime("-1 day"));
    $date2 = date('d-m-Y', strtotime("-8 day"));
 
    $datex = date(strtotime("-1 day"));
    $datey = date(strtotime("-8 day"));
}
 
//Run getData Function
$data = getData($date);
$data2 = getData2($date2);
 
//Which Date is Later
if($datex > $datey){
    $datebig = $datex;
}
else{
    $datebig = $datey;
}
//Chart Data
$chartData = array();
for ($i = 0; $i < count($data["cases"]); $i++) {
    $chartData[$i] = array(
        "province" => $data['cases'][$i]["province"],
        "cases1" => $data['cases'][$i]["cases"],
        "cases2" => $data2['cases'][$i]["cases"]
    );

    $useTop5 = false;
    if (isset($_POST['btnradio']) && $_POST['btnradio'] == "rad2") {
        $useTop5 = true;
        $sorted1 = array();
        $sorted2 = array();
    
        for ($i = 0; $i < count($data["cases"]); $i++) {
            $sorted1[$data["cases"][$i]["cases"]] = $data["cases"][$i];
            $sorted2[$data2["cases"][$i]["cases"]] = $data2["cases"][$i];
        }
        rsort($sorted1);
        rsort($sorted2);
    }
    if ($useTop5) {
        $count = -1;
        $chartData = array();
        foreach ($sorted1 as $key => $value) {
            $count++;
            $chartData[$count] = array(
                "province" => $value['province'],
                "cases1"   => $value['cases'],
                "cases2"   => $data2['cases']
            );
            if ($count >= 4)
                break;
        }
        $count = -1;
        foreach ($sorted2 as $key => $value) {
            $count++;
            $chartData[$count]["cases2"] = $value['cases'];
            if ($count >= 4)
                break;
        }
    }
    //---------------------------------------
    if (isset($_POST['btnradio']) && $_POST['btnradio'] == "rad3") {
        $maritimes = array("New Brunswick", "Nova Scotia", "PEI");
        $chartdata = array();
        
        $count = -1;
        for($i =0; $i < count($data["cases"]); $i++) {
            if(in_array($data['cases'][$i]['province'],$maritimes)){

                $count++;
                $chartData[$count] = array(
                    "province" => $data["cases"][$i]["province"],
                    "cases1"   => $data["cases"][$i]["cases"],
                    "cases2"   => $data2["cases"][$i]["cases"]
                );
            }
            
        }
    
        
    
    }
    } ?>

<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    
        <link rel="stylesheet" type="text/css" href="themes.css">

        <div class="p-3 mb-2 bg-dark text-cyan text-center"><h1>&#x2623; Covid-19 Case Tracker &#x2623;</h1></div>

        <!-- Resources -->
        <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
        <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
        <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
        <script src="https://cdn.amcharts.com/lib/4/themes/dark.js"> </script>

        <!-- Styles -->
    <style>

        #chartdiv {
            width: 100%;
            height: 500px;
        }

        body {
            background-color: black;
            color: white;
            }

            .light-mode {
            background-color: white;
            color: black;
            }

    </style>
 
        <!-- Chart code -->
        <script>
        am4core.ready(function() {
 
            // Themes begin
            am4core.useTheme(am4themes_animated);
            am4core.useTheme(am4themes_dark);
            // Themes end
 
            // Create chart instance
            var chart = am4core.create("chartdiv", am4charts.XYChart3D);
 
            // Add data
            chart.data = <?php echo json_encode($chartData); ?>
 
            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "province";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
 
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.title.text = "Covid cases";
            valueAxis.renderer.labels.template.adapter.add("text", function(text) {
                return text;
            });
 
            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries3D());
            series.dataFields.valueY = "cases1";
            series.dataFields.categoryX = "province";
            series.name = "Cases";
            series.clustered = false;
            series.columns.template.tooltipText = "Covid cases: [bold]{valueY}[/]";
            series.columns.template.fillOpacity = 0.9;
 
            var series2 = chart.series.push(new am4charts.ColumnSeries3D());
            series2.dataFields.valueY = "cases2";
            series2.dataFields.categoryX = "province";
            series2.name = "Cases";
            series2.clustered = false;
            series2.columns.template.tooltipText = "Covid cases: [bold]{valueY}[/]";
 
        }); // end am4core.ready()
    </script>

    </head>
    <body>
        <div class="container"> 
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="date" name="date" value="<?php echo date('Y-m-d', strtotime($date)); ?>">
                    <input type="date" name="date2" value="<?php echo date('Y-m-d', strtotime($date2)); ?>">
                
                <div class="float-end">
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" name="btnradio" value= "rad1" id="btnradio1" autocomplete="off" 
                            <?php if (!isset($_POST["btnradio"])) 
                                echo "checked";

                            elseif ($_POST['btnradio'] == "rad1") 
                                echo "checked";
                            ?>>
                <label class="btn btn-outline-cyan" for="btnradio1">All</label>


                        <input type="radio" class="btn-check" name="btnradio" value= "rad2" id="btnradio2" autocomplete="off"
                            <?php if (!isset($_POST["btnradio"])) 
                                    echo "checked";

                                elseif ($_POST['btnradio'] == "rad1") 
                                    echo "checked";
                                ?>>
                        <label class="btn btn-outline-cyan" for="btnradio2">Big Dogs</label>

                        <input type="radio" class="btn-check" name="btnradio" value= "rad3" id="btnradio3" autocomplete="off"
                            <?php if (!isset($_POST["btnradio"])) 
                                    echo "checked";

                                elseif ($_POST['btnradio'] == "rad1") 
                                    echo "checked";
                                ?>>
                        <label class="btn btn-outline-cyan" for="btnradio3">Martitimes</label>
                    </div>
                    <input class="btn btn-cyan" type="submit" name="submitButton" value="Compare Cases">
                </div>
            
            </form>
            <div id="chartdiv"></div>
            <table class="table table-dark table-hover table-borderless text-cyan">
                <thead>
                    <th>Province</th>
                    <th><?php echo date('d, F, Y', strtotime($date)); ?></th>
                    <th><?php echo date('d, F, Y', strtotime($date2)); ?></th>
                    <th>Net Change</th>
                    <th>Cumulative Cases</th>
                </thead>
                <tbody>
                    <?php
                    for($c = 0;  $c < count($data['cases']); $c++){
                        echo '<tr><td>' . $data['cases'][$c]['province'] . '</td>';
                        //Day1 Day2 Column
                        echo '<td>' . number_format($data['cases'][$c]['cases']) . '</td>'; 
                        echo '<td>' . number_format($data2['cases'][$c]['cases']) . '</td>'; 
                        //Net Change Calculations
                        if($datebig == $datex){
                            $change = $data['cases'][$c]['cumulative_cases'] - $data2['cases'][$c]['cumulative_cases'];  
                        }
                        else{
                            $change = $data2['cases'][$c]['cumulative_cases'] - $data['cases'][$c]['cumulative_cases']; 
                        }
                        //Net Change Column
                        echo '<td>' . number_format($change) . '</td>';
                        array_push($sumChange, $change);
                        //Cumulative Cases Column
                        if($datebig == $datex){
                            echo '<td>' . number_format($data['cases'][$c]['cumulative_cases']) . '</td></tr>';  
                        }
                        else{
                            echo '<td>' . number_format($data2['cases'][$c]['cumulative_cases']) . '</td></tr>';  
                        }
                        
                        //Add the cases for the day to an array and the cumulative cases
                        array_push($sumDate, $data['cases'][$c]['cases']);
                        array_push($sumDate2, $data2['cases'][$c]['cases']);
                        if($datebig == $datex){
                            array_push($sumCum, $data['cases'][$c]['cumulative_cases']);
                        }
                        else{
                            array_push($sumCum, $data2['cases'][$c]['cumulative_cases']);
                        }
                    }
                    //Find the sum of each day's cases and cumulative cases
                    $dateTotal = array_sum($sumDate);
                    $date2Total = array_sum($sumDate2);
                    $dateCumTotal = array_sum($sumCum);
                    $changeTotal = array_sum($sumChange);
 
                    echo '<tr><td><b>TOTAL</b></td>'; 
                    echo '<td><b>' . number_format($dateTotal) . '</b></td>';
                    echo '<td><b>' . number_format($date2Total) . '</b></td>';
                    echo '<td><b>' . number_format($changeTotal) . '</b></td>';
                    echo '<td><b>' . number_format($dateCumTotal) . '</b></td></tr>';
 
                    ?>
                </tbody>
            </table>    
        </div>
    </body>
</html>
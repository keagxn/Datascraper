<?php

$url = 'https://api.opencovid.ca/timeseries?stat=cases&loc=prov&date=01-09-2020';
$rawdata = file_get_contents($url);
$data = json_decode($rawdata,true);

$array = array(
    array(
    "country" => "USA",
    "year2017" => 3.5,
    "year2018" => 4.2
    ), array(
    "country" => "Canada",
    "year2017" => 6.5,
    "year2018" => 8.2
    )
);

echo '<pre>' ;
print_r($data);
echo '<pre>' ;

$chartData = array();
for ($i = 0; $i < count($data1["cases"]); $i++) {
    $chartData[$i] = array(
        "province" => =['cases'][$i]["province"],
        "cases1" => $data1['cases'][$i]["cases"],
        "cases2" => $data2['cases'][$i]["cases"]
    );
}

?>

<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>



<!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/dark.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

<!-- Chart code -->
<script>
    am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_dark);
    am4core.useTheme(am4themes_animated);
    // Themes end

    // Create chart instance
    var chart = am4core.create("chartdiv", am4charts.XYChart3D);

    // Add data
    chart.data = <?php echo json_encode($array); ?>;

    // Create axes
    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "country";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.minGridDistance = 30;

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.title.text = "GDP growth rate";
    valueAxis.renderer.labels.template.adapter.add("text", function(text) {
    return text + "%";
    });

    // Create series
    var series = chart.series.push(new am4charts.ColumnSeries3D());
    series.dataFields.valueY = "year2017";
    series.dataFields.categoryX = "country";
    series.name = "Year 2017";
    series.clustered = false;
    series.columns.template.tooltipText = "GDP grow in {category} (2017): [bold]{valueY}[/]";
    series.columns.template.fillOpacity = 0.9;

    var series2 = chart.series.push(new am4charts.ColumnSeries3D());
    series2.dataFields.valueY = "year2018";
    series2.dataFields.categoryX = "country";
    series2.name = "Year 2018";
    series2.clustered = false;
    series2.columns.template.tooltipText = "GDP grow in {category} (2017): [bold]{valueY}[/]";

    }); // end am4core.ready()
</script>
    
</head>

    <body>
        <div class="container">
            <h1> Covid Tracker</h1>
        </div>

        <div class="container">
            <div id="chartdiv"></div>
        </div>
    </body>

</html>


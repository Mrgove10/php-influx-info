<?php
$page = $_SERVER["PHP_SELF"];
$sec = "1800";

// 600 = 10 minutes,
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='<?php echo $page; ?>'">
  <title>Temperature and Humidity Map</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    .image-container {
      position: relative;
    }

    .background-image {
      display: block;
      max-width: 100%;
      height: auto;
    }

    .temp-box {
      position: absolute;
      background-color: black;
      color: white;
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 15px;
      font-family: 'sans-serif';
      text-align: center;
      transform: translate(-50%, -50%);
    }

    /* Adjust positions as percentages relative to the image */
    .box_LR { top: 25%; left: 24%; } /* Living Room */
    .box_BATH { top: 11%; left: 67%; } /* Bathroom */
    .box_BRAR { top: 59%; left: 59%; } /* Bedroom AR */
    .box_BRYH { top: 81%; left: 63%; } /* Bedroom YH */
    .box_KIT { top: 39%; left: 58%; } /* Kitchen */
    .box_OUT { top: 61%; left: 25%; } /* Outside */
  </style>
</head>
<body>
<?php
require __DIR__ . "/vendor/autoload.php";

// Load secrets
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Create Influx client
$client = new InfluxDB2\Client([
    "url" => $_ENV["URL"],
    "token" => $_ENV["TOKEN"],
    "bucket" => $_ENV["BUCKET"],
    "org" => $_ENV["ORG"],
    "debug" => false,
]);

$queryApi = $client->createQueryApi();

$sensorData = [];
for ($i = 1; $i <= 6; $i++) {
    $data = $queryApi->queryRaw(
        "from(bucket: \"sensors\")
        |> range(start: 1970-01-01T00:00:00.000000001Z)
        |> filter(fn: (r) => r[\"_measurement\"] == \"temp-sens-{$i}\")
        |> filter(fn: (r) => r[\"_field\"] == \"temperature\" or r[\"_field\"] == \"humidity\")
        |> last()"
    );

    $lines = explode("\n", $data);
    $dataLines = array_slice($lines, 4, 2);

    $temperature = null;
    $humidity = null;

    foreach ($dataLines as $line) {
        $fields = str_getcsv($line);
        $value = $fields[6];
        $field = $fields[7];

        if ($field === "temperature") {
            $temperature = $value;
        } elseif ($field === "humidity") {
            $humidity = $value;
        }
    }

    $sensorData[] = ["temperature" => $temperature, "humidity" => $humidity];
}
?>
 <div class="image-container">
    <img src="maps.png" alt="Background" class="background-image">
    <div class="temp-box box_LR">
      <?php echo $sensorData[5]["temperature"]; ?>   C<br>
      <?php echo $sensorData[5]["humidity"]; ?>%
    </div>
    <div class="temp-box box_BATH">
      <?php echo $sensorData[0]["temperature"]; ?>   C<br>
      <?php echo $sensorData[0]["humidity"]; ?>%
    </div>
    <div class="temp-box box_BRAR">
      <?php echo $sensorData[4]["temperature"]; ?>   C<br>
      <?php echo $sensorData[4]["humidity"]; ?>%
    </div>
    <div class="temp-box box_BRYH">
      <?php echo $sensorData[1]["temperature"]; ?>   C<br>
      <?php echo $sensorData[1]["humidity"]; ?>%
    </div>
    <div class="temp-box box_KIT">
      <?php echo $sensorData[2]["temperature"]; ?>   C<br>
      <?php echo $sensorData[2]["humidity"]; ?>%
    </div>
    <div class="temp-box box_OUT">
      <?php echo $sensorData[3]["temperature"]; ?>   C<br>
      <?php echo $sensorData[3]["humidity"]; ?>%
    </div>
  </div>
</body>
</html>

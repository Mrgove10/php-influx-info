<?php
$page = $_SERVER['PHP_SELF'];
$sec = "5";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
  <title>PHP DATA</title>
</head>
<body>
<?php
require __DIR__ . '/vendor/autoload.php';

// load secrets
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Create influx client
$client = new InfluxDB2\Client([
    "url" => $_ENV['URL'],
    "token" => $_ENV['TOKEN'],
    "bucket" => $_ENV['BUCKET'],
    "org" => $_ENV['ORG'],
    "debug" => false
]);


$queryApi = $client->createQueryApi();

$data = $queryApi->queryRaw(
    "from(bucket: \"sensors\")
    |> range(start: 1970-01-01T00:00:00.000000001Z)
    |> filter(fn: (r) => r[\"_measurement\"] == \"temp-sens-2\")
    |> filter(fn: (r) => r[\"_field\"] == \"temperature\" or r[\"_field\"] == \"humidity\")
    |> last()"
);

/// Remove metadata lines and extract only the actual data
$lines = explode("\n", $data);
$dataLines = array_slice($lines, 4);  // Skip the first 3 lines (2 metadata and 1 empty line)

// Now process the data lines
$temperature = null;
$humidity = null;
$lastUpdate = null;
$sensorName = null;

foreach ($dataLines as $line) {

    // Split the line into fields based on commas
    $fields = str_getcsv($line);
    print_r($fields);
    // The time is in the 5th position, and the value is in the 6th
    $time = $fields[5];  // This is the timestamp of the last update
    $value = $fields[6];
    $field = $fields[7];
    $sensor = $fields[8];  // This is the sensor name (_measurement)
 
    // Capture the sensor name (this should be the same for both temperature and humidity)
    if ($sensorName === null) {
        $sensorName = $sensor;
    }

    // Check if this is the temperature or humidity
    if ($field === 'temperature') {
        $temperature = $value;
    } elseif ($field === 'humidity') {
        $humidity = $value;
    }

    // Capture the last update time (we can keep the most recent one)
    if ($lastUpdate === null) {
        $lastUpdate = $time;  // We'll overwrite with the most recent time (as all data appears to have the same timestamp)
    }
}

// Output the results
echo "Sensor: " . $sensorName . "<br>";
echo "Temperature: " . $temperature . "°C<br>";
echo "Humidity: " . $humidity . "%<br>";
echo "Last Update: " . $lastUpdate . "<br>";
?>
</body>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    "precision" => InfluxDB2\Model\WritePrecision::NS,
    "debug" => false
]);


$queryApi = $client->createQueryApi();

$data = $queryApi->query(
    "from(bucket: \"sensors\")
    |> range(start: 1970-01-01T00:00:00.000000001Z)
    |> filter(fn: (r) => r[\"_measurement\"] == \"temp-sens-2\")
    |> filter(fn: (r) => r[\"_field\"] == \"temperature\")
    |> last()"
);

$data = json_encode($data, JSON_PRETTY_PRINT) ;

echo $data;
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';

echo $data[0]['records'];
?>
</body>
</html>
<?php
include("connect.php");

$departureAirportFilter = isset($_GET['departureAirportCode']) ? $_GET['departureAirportCode'] : '';
$arrivalAirportFilter = isset($_GET['arrivalAirportCode']) ? $_GET['arrivalAirportCode'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$order = isset($_GET['order']) ? $_GET['order'] : '';

$flightLogsQuery = "SELECT * FROM flightLogs";

if ($departureAirportFilter != '' || $arrivalAirportFilter != '') {
  $flightLogsQuery = $flightLogsQuery . " WHERE";

  if ($departureAirportFilter != '') {
    $flightLogsQuery = $flightLogsQuery . " departureAirportCode='$departureAirportFilter'";
  }

  if($departureAirportFilter != '' && $arrivalAirportFilter != ''){
    $flightLogsQuery = $flightLogsQuery . " AND";
  }
  
  if ($arrivalAirportFilter != '') {
    $flightLogsQuery = $flightLogsQuery . " arrivalAirportCode='$arrivalAirportFilter'";
  }
}

if ($sort != ''){
  $flightLogsQuery = $flightLogsQuery." ORDER BY $sort";

  if($order != ''){
    $flightLogsQuery = $flightLogsQuery." $order";
  }
}

$flightLogsResults = executeQuery($flightLogsQuery);

$departureAirportCodeQuery = "SELECT DISTINCT(departureAirportCode) FROM flightLogs";
$departureAirportCodeResults = executeQuery($departureAirportCodeQuery);

$arrivalAirportCodeQuery = "SELECT DISTINCT(arrivalAirportCode) FROM flightLogs";
$arrivalAirportCodeResults = executeQuery($arrivalAirportCodeQuery);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PUP Airport</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css" class="rel">
</head>

<body>
  <div class="container">
    <div class="row my-5">
      <div class="col">
        <form method="GET">
          <div class="card p-4 rounded-5 shadow">
            <div class="h6 mb-4">Filter</div>
            <div class="d-flex flex-row align-items-center">
              <label for="depAirSelect" class="filter-label">Departure Airport Code</label>
              <select id="depAirSelect" name="departureAirportCode" class="ms-2 form-control">
                <option value="">Any</option>
                <?php
                if (mysqli_num_rows($departureAirportCodeResults) > 0) {
                  while ($departureAirportCodeRow = mysqli_fetch_assoc($departureAirportCodeResults)) {
                    ?>
                    <option <?php if($departureAirportFilter == $departureAirportCodeRow['departureAirportCode']) {echo "selected";}?> value="<?php echo $departureAirportCodeRow['departureAirportCode'] ?>">
                      <?php echo $departureAirportCodeRow['departureAirportCode'] ?>
                    </option>
                    <?php
                  }
                }
                ?>
              </select>

              <label for="arrivalAirportCodeSelect" class="ms-2 filter-label">Arrival Airport Code</label>
              <select id="arrivalAirportCodeSelect" name="arrivalAirportCode" class="ms-2 form-control">
                <option value="">Any</option>
                <?php
                if (mysqli_num_rows($arrivalAirportCodeResults) > 0) {
                  while ($arrivalAirportCodeRow = mysqli_fetch_assoc($arrivalAirportCodeResults)) {
                    ?>
                    <option <?php if($arrivalAirportFilter == $arrivalAirportCodeRow['arrivalAirportCode']) {echo "selected";}?> value="<?php echo $arrivalAirportCodeRow['arrivalAirportCode'] ?>">
                      <?php echo $arrivalAirportCodeRow['arrivalAirportCode'] ?>
                    </option>
                    <?php
                  }
                }
                ?>
              </select>

              <label for="sort" class="ms-2 filter-label">Sort By</label>
              <select id="sort" name="sort" class="ms-2 form-control">
                <option value="">None</option>
                <option <?php if($sort == "flightNumber") {echo "selected";}?> value="flightNumber">Flight Number</option>
                <option <?php if($sort == "passengerCount") {echo "selected";}?> value="passengerCount">Passenger Count</option>
                <option <?php if($sort == "pilotName") {echo "selected";}?> value="pilotName">Pilot Name</option>
                <option <?php if($sort == "airlineName") {echo "selected";}?> value="airlineName">Airline Name</option>
              </select>

              <label for="order" class="ms-2 filter-label">Order</label>
              <select id="order" name="order" class="ms-2 form-control">
                <option <?php if($order == "ASC") {echo "selected";}?> value="ASC">Ascending</option>
                <option <?php if($order == "DESC") {echo "selected";}?> value="DESC">Descending</option>
              </select>
            </div>

            <div class="text-center mt-4">
              <button class="btn btnFilter">Filter</button>
            </div>

          </div>
        </form>
      </div>
    </div>

    <div class="row my-5">
      <div class="col">
        <div class="card p-4 rounded-5 shadow">
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col" class="<?php echo ($sort == 'flightNumber') ? 'highlight' : ''; ?>"><?php echo ($sort == 'flightNumber' && $order == "ASC") ? ' ↑ ' : ''; echo ($sort == 'flightNumber' && $order == "DESC") ? ' ↓ ' : ''; ?>Flight Number</th>
                <th scope="col" class="<?php echo ($departureAirportFilter != '') ? 'highlight' : ''; ?>">Departure Airport Code</th>
                <th scope="col" class="<?php echo ($arrivalAirportFilter != '') ? 'highlight' : ''; ?>">Arrival Airport Code</th>
                <th scope="col" class="<?php echo ($sort == 'airlineName') ? 'highlight' : ''; ?>"><?php echo ($sort == 'airlineName' && $order == "ASC") ? ' ↑ ' : ''; echo ($sort == 'airlineName' && $order == "DESC") ? ' ↓ ' : ''; ?>Airline Name</th>
                <th scope="col" class="<?php echo ($sort == 'pilotName') ? 'highlight' : ''; ?>"><?php echo ($sort == 'pilotName' && $order == "ASC") ? ' ↑ ' : ''; echo ($sort == 'pilotName' && $order == "DESC") ? ' ↓ ' : ''; ?>Pilot Name</th>
                <th scope="col" class="<?php echo ($sort == 'passengerCount') ? 'highlight' : ''; ?>"><?php echo ($sort == 'passengerCount' && $order == "ASC") ? ' ↑ ' : ''; echo ($sort == 'passengerCount' && $order == "DESC") ? ' ↓ ' : ''; ?>Passenger Count</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (mysqli_num_rows($flightLogsResults) > 0) {
                while ($flightLogsRow = mysqli_fetch_assoc($flightLogsResults)) {
                  ?>
                  <tr>
                    <th scope="row"><?php echo $flightLogsRow['flightNumber'] ?></th>
                    <td><?php echo $flightLogsRow['departureAirportCode'] ?></td>
                    <td><?php echo $flightLogsRow['arrivalAirportCode'] ?></td>
                    <td><?php echo $flightLogsRow['airlineName'] ?></td>
                    <td><?php echo $flightLogsRow['pilotName'] ?></td>
                    <td><?php echo $flightLogsRow['passengerCount'] ?></td>
                  </tr>
                  <?php
                }
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>

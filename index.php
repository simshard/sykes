<?php
/**
 * Sykes Cottages Search demo
 * @author Name <simon.kember@blueyonder.co.uk>
 * 
 */
$MYSQL_HOST='localhost';
$MYSQL_USER='root';
$MYSQL_PASS='';
$DEFAULT_DB='sykes_interview';

$mysqli  = new mysqli($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS, $DEFAULT_DB);
 
if (mysqli_connect_errno()) {
    echo "<strong>Failed to connect to MySQL</strong>: " . mysqli_connect_error();
    exit;
}
if (isset($_GET['page']) & !empty($_GET['page'])) {
    $curpage = $_GET['page'];
} else {
    $curpage = 1;
}


if (isset($_POST['searchform'])&& !empty($_POST)) {
    
  //echo '<pre>';  print_r($_POST);  echo '</pre>';

    $searchterm=isset($_POST['location'])? mysqli_real_escape_string($mysqli, $_POST['location']):null;
    // $cleanSearchterm=Sanitizes_text($_POST['location']);
    $beds=isset($_POST['beds'])? mysqli_real_escape_string($mysqli, $_POST['beds']):null;
    $sleeps=isset($_POST['sleeps'])?  mysqli_real_escape_string($mysqli, $_POST['sleeps']):null;
    $pets=isset($_POST['pets']) ? $_POST['pets']:0;
    $beach=isset($_POST['beach']) ? $_POST['beach']:0;
    $datepicker=isset($_POST['datepicker'])? $_POST['datepicker']:null;

    $pagelimit = 2;
    $totalrowsquery="SELECT count(*) AS totalrows FROM `locations` AS loc
                     LEFT JOIN `properties` AS prop ON loc.__pk=prop._fk_location";
    $totalpages = mysqli_query($mysqli, $totalrowsquery);
    $totalrows = mysqli_num_rows($totalpages);
    $start = ($curpage * $pagelimit) - $pagelimit;
    $endpage = ceil($totalrows/$pagelimit);
    $startpage = 1;
    $nextpage = $curpage + 1;
    $previouspage = $curpage - 1;


    $query="SELECT 
            loc.__pk AS location_id,loc.location_name, prop.__pk AS property_id,
            prop.property_name,prop.near_beach,
            prop.accepts_pets,prop.beds,prop.sleeps
            ,book.end_date,book.start_date
            FROM `locations` AS loc
            LEFT JOIN `properties` AS prop ON loc.__pk=prop._fk_location
            LEFT JOIN `bookings` AS book ON prop.__pk=book._fk_property
            WHERE loc.location_name LIKE '%$searchterm%' ";

    if ($pets) {
        $query.=" AND `accepts_pets`=$pets";
    }
    if ($beach) {
        $query.=" AND `near_beach`=$beach";
    }
    if ($sleeps) {
        $query.=" AND `sleeps`=$sleeps";
    }
    if ($beds) {
        $query.=" AND `beds`=$beds";
    }
     if ($datepicker) {
        $query.=" AND  $datepicker NOT BETWEEN book.start_date AND book.end_date ";
    }
    
    //$query.="LIMIT $start, $pagelimit";
    
    $results='';

    $result =$mysqli->query($query) or die(mysqli_error($mysqli));
    while ($item= $result->fetch_object()) {
        foreach ($item as $key => $value) {
            $$key=$value;
        }
     
        $results.=  '<li> <b><a href="loc'.$location_id.'.html">'. $location_name.'</a></b> ,  <i><a href="prop'.$property_id.'.html">'. $property_name.'</a></i>  sleeps:'.$sleeps.' beds:'. $beds;
        if ($near_beach) {
            $results.=' Near to beach.';
        }
        if ($accepts_pets) {
            $results.=' Pets allowed.';
        }
        if ($datepicker) {
            $results.=' Property available on '.$datepicker.'.' ;
        }
        $results.='</li>';
    }
      
    echo('<h3>Search Results</h3><ul>'.$results.'</ul>');
        
    if (empty($results)) {
        echo('<p>No results</p>');
    }
    echo('<small>Query:'.$query.'</small>');

    $mysqli->close();
} else {
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <title>Sykes Cottages</title>
</head>

<body>
  <div class="container">
    <h1>Search for your Holiday Rental</h1>

    <form name="search" action="" method="POST">
      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" class="form-control" id="location" name="location" aria-describedby="location"
          placeholder="Enter placename or location">
      </div>
      <div class="form-group">
        <label for="beds">Beds (Min) </label>
        <input type="beds" class="form-control" id="beds" name="beds" placeholder="No. of beds (min)">
      </div>
      <div class="form-group">
        <label for="sleeps">sleeps (Min) </label>
        <input type="sleeps" class="form-control" id="sleeps" name="sleeps" placeholder=" sleeps (min)">
      </div>
      <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="pets" name="pets" value="true">
        <label class="form-check-label" for="pets">Pet Friendly</label>
      </div>
      <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="beach" name="beach" value="true">
        <label class="form-check-label" for="beach">Close to beach</label>
      </div>
      <div class="form-group">
        <label for="datepicker">Check Availability</label>
        <input type="text" class="form-control" id="datepicker" name="datepicker" aria-describedby="datepicker"
          placeholder="Check availability; Pick a date">
      </div>
      <input type="hidden" name="searchform" value=true>
      <button type="submit" class="btn btn-primary">Search</button>
    </form>
  </div>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
  </script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
    integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>

  <script>
    $(function () {
      $("#datepicker").datepicker();
      $("#datepicker").datepicker("option", "dateFormat", "yy-mm-dd");
    });
  </script>
</body>

</html>
    <?php
}
 
 
 
function Sanitizes_text($value)
{
    if (get_magic_quotes_gpc()) {
         $value = stripslashes($value);
    }
     $value=strip_tags($value);
    $value=filter_var(
        $value,
        FILTER_SANITIZE_STRING,
        FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW
    );
     $value=htmlspecialchars($value, ENT_QUOTES);
     $crlf=array('&#13;','&#10;');
     $value= str_replace($crlf, '', $value);
     return trim($value);
}

 
function Sanitizes_numeric($value)
{
    if (get_magic_quotes_gpc()) {
         $value = stripslashes($value);
    }
     $value=filter_var($value, FILTER_SANITIZE_NUMBER_INT);//integers only
     return trim($value);
}

    /*
     $stmt = $mysqli->prepare("SELECT
            loc.__pk AS location_id,loc.location_name, prop.__pk AS property_id,
            prop.property_name,prop.near_beach,
            prop.accepts_pets,prop.beds,prop.sleeps
           FROM `locations` AS loc
            LEFT JOIN `properties` AS prop ON loc.__pk=prop._fk_location
             WHERE
              MATCH(loc.location_name) AGAINST( '?' IN BOOLEAN MODE )
              AND `near_beach`=?
              AND `accepts_pets`=?
              AND `beds`>=?
              AND `sleeps`>=?
           ");
    $stmt->execute();
    $stmt -> store_result();
    if ( $stmt &&
        $stmt -> bind_param("siiii", $searchterm, $beach, $pets, $beds, $sleeps) &&
        $stmt -> execute() &&
        $stmt -> store_result() &&
        $stmt -> bind_result($location_id, $location_name, $property_id, $property_name, $near_beach, $accepts_pets, $beds, $sleeps )
        ) {
        while ($stmt -> fetch()){
            $res.='<li>'.$location_id.' :'. $location_name.'  '.$property_id.'<i>'. $property_name.'</i>  beach:'. $near_beach.' pets:'. $accepts_pets.' sleeps:'.$sleeps.' beds:'. $beds.'</li>';
        }
    } else {
        echo 'Prepared Statement Error';
       //echo $mysqli -> error;
}
 */
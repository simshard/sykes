<?php
/**
 * Sykes Cottages Search demo
 * @author Name <simon.kember@blueyonder.co.uk>
 *
 */

 function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}




$host = '127.0.0.1';
$db   = 'sykes_interview';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
  //  echo "Connected successfully";

} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

 

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
<?php

if (isset($_POST['searchform'])&& !empty($_POST)) {
    
  // echo '<pre>';  print_r($_POST);  echo '</pre>'; 

    $searchterm=isset($_POST['location'])?  '%'.$_POST['location'].'%':null;
    $beds=isset($_POST['beds'])?   $_POST['beds']:0;
    $sleeps=isset($_POST['sleeps'])?$_POST['sleeps']:0;
    $accepts_pets=isset($_POST['pets']) && $_POST['pets'] ? 1:0;
    $near_beach=isset($_POST['beach']) && $_POST['beach'] ? 1:0;
    $datepicker=isset($_POST['datepicker'])?   $_POST['datepicker']:null;
    $params=array();
/*
    $offset=0;
    $pagelimit = 2;
    if (isset($_GET['page']) & !empty($_GET['page'])) {
        $curpage = $_GET['page'];
        if ($curpage>1) {
            $offset = ($curpage - 1) * $pagelimit;
        }
    } else {
        $curpage = 1;
    }
    */


   
$params['searchterm']= $searchterm;
 


    $query="SELECT loc.__pk AS location_id,loc.location_name,
     prop.__pk AS property_id, prop.property_name,prop.near_beach,
      prop.accepts_pets,prop.beds,prop.sleeps ,book.end_date,book.start_date
       FROM `locations` AS loc 
       LEFT JOIN `properties` AS prop ON loc.__pk=prop._fk_location 
       LEFT JOIN `bookings` AS book ON prop.__pk=book._fk_property  
       WHERE loc.location_name LIKE :searchterm ";

    if ($accepts_pets) {
        $query.=" AND `accepts_pets`=:accepts_pets ";
        $params[ 'accepts_pets']= $accepts_pets;
    }
    if ($near_beach) {
        $query.=" AND `near_beach`=:near_beach ";
                $params['near_beach']=$near_beach;
    }
    if ($sleeps) {
        $query.=" AND `sleeps`>=:sleeps ";
                $params['sleeps']= $sleeps;
    }
    if ($beds) {
        $query.=" AND `beds`>=:beds ";
                $params['beds']=  $beds;
    }
    if ($datepicker) {
        $query.=" AND  :datepicker NOT BETWEEN  'book.start_date' AND 'book.end_date' ";
                $params['datepicker']=  $datepicker;
    }
   

    
    //echo $query; echo'<pre>';print_r($params);echo'</pre>';exit;
 
  $stmt = $pdo->prepare("$query");
  $stmt->execute($params);


 $output='';
 $i=0;

 while ($result = $stmt->fetch(PDO::FETCH_OBJ)) {
     

      $output.=  '<li> <b><a href="loc'.$result->location_id.'.html">'. $result->location_name.'</a></b> ,  <i><a href="prop'.$result->property_id.'.html">'. $result->property_name.'</a></i>  sleeps:'.$result->sleeps.' beds:'. $result->beds;
        if ($result->near_beach) {
            $output.=' Near to beach.';
        }
        if ($result->accepts_pets) {
            $output.=' Pets allowed.';
        }
        if ($datepicker) {
            $output.=' Property available on '.$datepicker.'.' ;
        }
        $output.='</li>';
        $i++;
    }


    echo('<h3>Search Results</h3>');
    if ($i==0) {
      echo('<p>No results found.</p>');
    }else{
      echo('<ul>'.$output.'</ul>');
    }
    echo('<p><a href="index3.php">Search again</a></p>');


 
 // echo'<pre>';
 // print_r($result);
 // echo'</pre>';
 



} else {
    ?>

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
 
 
 

  
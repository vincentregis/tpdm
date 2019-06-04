<?php
// echo $file= __DIR__."\codes.csv";
// die;
$file = fopen("codes.csv","r");
$data=array();
$i=0;
while($row=fgetcsv($file)){
// fgetcsv($file);
	$data[$row[0]]=$row[1];
$i++;
}
// echo $i;
// echo "<pre>";
	// print_r($data);
// echo "</pre>";
fclose($file);
// die;
function getcode($city){
	global $data;
	// echo $city;
	// echo $data[$city];
	// die;
	if(isset($data[$city]))
		return $data[$city];
	return $data['Abanda'];
}
if(!isset($_POST['start_date'])){
	header("location:index.php");
}
else{
	$clientKey="ODYwNzAxfDE1NDE2NTEwNzYuMA";
	$aid="13792";
	$start_date=date("Y-m-d",strtotime($_POST['start_date']))."T00:00:00";
	$end_date=date("Y-m-d",strtotime($_POST['end_date']))."T23:59:59";
	// die;
	 $url="https://api.seatgeek.com/2/events?client_id=$clientKey&aid=$aid&lat=".$_POST['lat']."&lon=".$_POST['long']."&range=".$_POST['range']."mi&per_page=5000&datetime_local.gte=".$start_date."&datetime_local.lt=".$end_date;
	// die;
	$taxonomies=array_keys($_POST['taxonomies']);
	if(!empty($taxonomies)){
		foreach($taxonomies as $taxonomy){
			$url.="&taxonomies.name=$taxonomy";
		}
	}
	// echo $url;
	// die;
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_SSL_VERIFYPEER => false,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_HTTPHEADER => array(
		"cache-control: no-cache",
		"postman-token: afc70a7a-f210-c17e-9e99-84047e240c38"
	  ),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	$response=json_decode($response);
	// echo "<pre>";
		// print_r($response);
		// echo "</pre>";
		// die;
	if($response->meta->total>0){
		$events=$response->events;
		
	}
	else{
		echo "NO events Found";
		die;
	}
	
	}
	
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <title>Events</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<style>
		.active{
			background:#ccc;
		}
		.event{
			cursor:pointer;
		}
	</style>
  </head>
   <body>
      <div class="container">
         <div class="panel panel-info">
			<div class="panel-heading">
			 <h2>Events</h2>
			</div>
			<div class="panel-body">
			<form action="map.php" method="post">
			  <?php
				if(!empty($events)){
					foreach($events as $event){
						// echo "<pre>";
						// print_r(getcode($event->venue->city));
						// echo "</pre>";
		   if(isset($data[$event->venue->city]))
						  $citycode= $data[$event->venue->city];
						else
                          $citycode= $data['Abanda'];
						// die;
						echo "<div class='col-sm-12 event' data-lat='".$event->venue->location->lat."' data-citycode='".getcode($event->venue->city)."' data-time='".strtotime($event->datetime_local)."'  data-id='".$event->id."'  data-lon='".$event->venue->location->lon."'  style='padding: 6px 0px;border-bottom: 1px solid #ccc;'>";
						echo "<h4 class='title'>$event->title</h4>";
						echo "<font class='price' hidden>".$event->stats->average_price."</font><br>";
						echo "<strong>Type:</strong><font class='type'>$event->type</font><br>";
						echo "<strong>Date:</strong><font class='date'>".date("Y-m-d | h:i",strtotime($event->datetime_local))."</font><br>";
						echo "<strong>Venue Name:</strong><font class='venue'>".$event->venue->address."</font><br>";
						echo "<font class='url' hidden>$event->url</font><br>";
						// echo "<strong>Performers</strong><br>";
						echo "<ul class='performers' hidden>";
						foreach($event->performers as $performers){
							echo "<li>$performers->name</li>";
						}
						echo "</ul>";
						
						echo "</div>";
						// echo "<hr>";
						// echo "<pre>";
						// print_r($event);
						// echo "</pre>";
						// die;
					}
				}
			  
			  ?>
			  <input type="hidden" name="start_lat" value="<?php echo @$_POST['lat']; ?>">
			  <input type="hidden" name="start_lon" value="<?php echo @$_POST['long']; ?>">
			  <div class=" col-sm-6  col-sm-offset-3 ">
			  <br><input type="submit" class="submt-btn form-control btn btn-info" value="Submit">
				</div>
				</form>
			</div>
		</div>
       
      </div>
   </body>
   <script>
   var waypts=new Array();
   var data=new Array();
	$(document).ready(function(){
		$(document).on("submit","form",function(){
			waypts=waypts.sort();
			// alert("usman");
			// console.log(waypts);
				
			html="";
			for(var i in waypts){
				html+="<input type='hidden' name='waypts["+i+"][lat]' value='"+waypts[i].lat+"'>";
				html+="<input type='hidden' name='waypts["+i+"][lon]' value='"+waypts[i].lon+"'>";
				html+="<input type='hidden' name='waypts["+i+"][name]' value='"+waypts[i].name+"'>";
				html+="<input type='hidden' name='waypts["+i+"][citycode]' value='"+waypts[i].citycode+"'>";
				html+="<input type='hidden' name='waypts["+i+"][type]' value='"+waypts[i].type+"'>";
				html+="<input type='hidden' name='waypts["+i+"][date]' value='"+waypts[i].date+"'>";
				html+="<input type='hidden' name='waypts["+i+"][price]' value='"+waypts[i].price+"'>";
				html+="<input type='hidden' name='waypts["+i+"][venue]' value='"+waypts[i].venue+"'>";
				html+="<input type='hidden' name='waypts["+i+"][url]' value='"+waypts[i].url+"'>";
				for(var j in waypts[i].performers){
					html+="<input type='hidden' name='waypts["+i+"][performers]["+j+"]' value='"+waypts[i].performers[j]+"'>";
					
				}
				// if(waypts.length-1==i){
					
				// }
			}
				
				$(this).append(html);	
				// $("form").submit();
					// return false;
					
		});
		$(document).on("click",".event",function(){
			id=$(this).data("id");
			if(!$(this).hasClass("active")){
				$(this).addClass("active");
				title=$(this).find(".title").text();
				venue=$(this).find(".venue").text();
				date=$(this).find(".date").text();
				price=$(this).find(".price").text();
				type=$(this).find(".type").text();
				url=$(this).find(".url").text();
				// code=$(this).find(".url").text();
				performers=new Array();
				$(this).find(".performers li").each(function(){
					performers.push($(this).text());
				})
				lat=$(this).data("lat");
				lon=$(this).data("lon");
				code=$(this).data("citycode");
				object= new Object();
				object={
					id:id,
					lat:lat,
					lon:lon,
					name:title,
					citycode:code,
					venue:venue,
					date:date,
					price:price,
					type:type,
					url:url,
					performers:performers,
				};
				waypts.push(object);
				// id=waypts.length-1;
				// $(this).attr("data-id",id);
			}
			else{
				$(this).removeClass("active");
				// var id = 88;

				for(var i = 0; i < waypts.length; i++) {
					if(waypts[i].id == id) {
						waypts.splice(i, 1);
						break;
					}
				}
				// delete waypts[id];
			}
			// console.log(waypts[id]);
			console.log(waypts);
			
			// $("[name='events']").val(JSON.stringify(waypts));
		})
		
	})
   </script>
</html>
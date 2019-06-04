   $(document).ready(function(){
	   $(document).on('blur','[name="start_date"]',function(){
		   $("[name='end_date']").val($(this).val());
		   // alert("usman0");
	   })
	   $(document).on('submit','form',function(){
		   if($('div.checkbox-group :checkbox:checked').length==0){
			   $(".msg").show();
			   return false;
		   }

	   })
   })
function initMap(){
		var j,i;
		var latitude=12.9715987,longitude=77.59456269999998;
	 map = new google.maps.Map(document.getElementById('map-canvas'), {
     center: {
       lat: latitude,
       lng: longitude
     },
     zoom: 18
   });
   var options = {
  types: ['(cities)']
 };
	var autocomplete = new google.maps.places.Autocomplete(document.getElementById('start_address'),options);
	autocomplete.bindTo('bounds', map);
	google.maps.event.addListener(autocomplete, 'place_changed', function(){
      var place = autocomplete.getPlace();
	  $("[name='lat']").val(place.geometry.location.lat());
	  $("[name='long']").val(place.geometry.location.lng());
	  // console.log(place.geometry.location.lng());
	});
}
$('.input-daterange').datepicker({
	format: "yyyy-mm-dd",
	autoclose: true
});
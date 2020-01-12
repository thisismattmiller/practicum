<?php


	require_once('include.php');
	$project = new Chart;

	if (isset($_REQUEST['address'])){
	
			header("content-type: application/json");
			
			$_REQUEST['address']=str_replace(" ","+",$_REQUEST['address']);
			$data = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . $_REQUEST['address'] . '+near+brooklyn+NY&sensor=false');				
	
			echo $data;
			exit();
	}
	
	if (isset($_REQUEST['toDoCount'])){		
		header("content-type: application/json");	
		$toDoCount = $project->returnToDoImageCount();	
		echo $toDoCount;
		exit();		
	}


	if (isset($_REQUEST['toDoImages'])){		
		header("content-type: application/json");	
		$toDoCount = $project->returnToDoImages();	
		echo $toDoCount;
		exit();		
	}
	
	if (isset($_REQUEST['returnImages'])){		
		header("content-type: application/json");	
		$toDoCount = $project->returnImages();	
		echo $toDoCount;
		exit();		
	}	
	
	if (isset($_REQUEST['returnNotes'])){		
		header("content-type: application/json");	
		$notes = $project->returnNotes($_REQUEST['returnNotes']);	
 		exit();		
	}	
	
	

	if (isset($_REQUEST['lat']) && isset($_REQUEST['lng']) && isset($_REQUEST['id'])){		
		header("content-type: application/json");	
		$toDoCount = $project->savePlacement($_REQUEST['id'],$_REQUEST['lat'],$_REQUEST['lng']);	
		exit();		
	}

	if (isset($_REQUEST['pov']) && isset($_REQUEST['id'])){		
		header("content-type: application/json");	
		$toDoCount = $project->savePov($_REQUEST['id'],$_REQUEST['pov']);	
		exit();		
	}	

	if (isset($_REQUEST['note']) && isset($_REQUEST['id'])){		
		header("content-type: application/json");	
		$toDoCount = $project->saveNote($_REQUEST['id'],$_REQUEST['note']);	
		exit();		
	}	
	
	if (isset($_REQUEST['removeItemFromMap'])){		
		header("content-type: application/json");	
		$toDoCount = $project->removeItemFromMap($_REQUEST['removeItemFromMap']);	
		exit();		
	}		



?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="X-UA-Compatible" content="IE=8;FF=3;OtherUA=4" />



<link type="text/css" href="css/flick/jquery-ui-1.8.10.custom.css" rel="Stylesheet" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.10.custom.min.js"></script>


<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0px; padding: 0px; font-family:Arial, Helvetica, sans-serif;}
  #map_canvas { height: 100% }
  #todoBox{ position:absolute; top:35px; right:7px; z-index:1000; height:90%; width:250px;
  			-moz-box-shadow: 3px 3px 4px #666;-webkit-box-shadow: 3px 3px 4px #666;box-shadow: 3px 3px 4px #666;/* For IE 8 */-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666')";/* For IE 5.5 - 7 */filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666');
			background-position:top;
			visibility:hidden; 
			
  
  }
  
  
  .infoWindowLock{text-align:center; font-size:14px;}
  #toolBar { padding: 5px 2px; position:absolute; top:20px; left:75px; z-index:1000; font-size:12px; 
  
		-moz-box-shadow: 3px 3px 4px #666;-webkit-box-shadow: 3px 3px 4px #666;box-shadow: 3px 3px 4px #666;/* For IE 8 */-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666')";/* For IE 5.5 - 7 */filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666');
  
  
  }  
  #searchBox{ position:absolute; top:80px; left: 75px; z-index:1000; height:200px; width:325px;
  			-moz-box-shadow: 3px 3px 4px #666;-webkit-box-shadow: 3px 3px 4px #666;box-shadow: 3px 3px 4px #666;/* For IE 8 */-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666')";/* For IE 5.5 - 7 */filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666');
			background-position:top;
			visibility:hidden;
  			
  
  }
  #filterBox{ position:absolute; top:80px; left: 75px; z-index:1000; height:300px; width:325px;
  			-moz-box-shadow: 3px 3px 4px #666;-webkit-box-shadow: 3px 3px 4px #666;box-shadow: 3px 3px 4px #666;/* For IE 8 */-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666')";/* For IE 5.5 - 7 */filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666');
			background-position:top;
			visibility:hidden;
  			
  
  }  
  
  
  #previewBox{ position:absolute; top:20px; right: 10px; z-index:1000; height:310px; width:380px;
  			-moz-box-shadow: 3px 3px 4px #666;-webkit-box-shadow: 3px 3px 4px #666;box-shadow: 3px 3px 4px #666;/* For IE 8 */-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666')";/* For IE 5.5 - 7 */filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#666666');
			background-position:top;
			visibility:hidden;
  			
  
  }  
  #previewBoxImage{width:380px; background-position:center; background-repeat:no-repeat; background-color:#000; height:310px;}
  #previewBoxTitle{width:380px; text-align:center; height:65px; position:absolute; bottom:0px;  overflow:hidden; background:transparent url(img/trans_25.png) repeat;}
  
  #previewBoxClose{position:absolute; right:0px; bottom:0px; color:#000; text-decoration:none;}
  
  
  
  #formSearch_text{font-size:18px; border:none; width:320px; margin:2px; margin-top:5px;}
  
  #searchBox li{font-style:oblique;}	
  #searchBox div{margin:10px;}	
  #searchBox button{font-size:12px;}
  
  
  
  #todoBoxImages img{margin:3px; cursor:pointer;}
  #todoBoxImages{overflow:auto; max-height:85%; height:85%; background-color:#000; color:#FFF;}

  .todoBoxInstruction{
	  font-size:12px;
	  text-align:center;
	  margin:2px;
	  
	  
  }
  .todoBoxImagesHeader{
	  color:fff#;
	  font-size:14px;
  }

  #filterBox button{font-size:12px;}

  #filterBoxTitleList li{
	  cursor:pointer;
	  margin-bottom:2px;
	    
  
  }
  #filterBoxTitleList li:hover{
	  color:#06C;  
  }
  
  .liCheck{
	  background-image: url(img/li_check.png);
	  background-position: center left;
	  background-repeat: no-repeat;
	  padding-left: 25px;
	  padding-top: 2px;	  
	  list-style: none;	  	  
  }
  .liX{
	  background-image: url(img/li_x.png);
	  background-position: center left;
	  background-repeat: no-repeat;
	  padding-left: 25px;
	  list-style: none;	  	  
  }  
  .ui-dialog-titlebar{
	  padding:0em !important;

  }
  
  #dialog-modal{
	  position:relative;
	  background-color:#F9F9F9;
	  
  }

  #modalPano{
	  height:200px;
	  width:200px;
	  position:absolute;
	  left:9px;
	  top:8px;
	  
	  z-index:10000;
 
  }
  
  #streetViewControlsStart, #streetViewControlsEdit, #streetViewControlsPresent{
	  display:none;
	  width:500px;
	  min-width:500px;   
  }
  #streetViewTitle{
	  text-align:justify;
  }
  #streetViewMetaHolder{
	  float:left;
 	  height:465px;
	  overflow:auto;
	  position:relative;
	  
	  
  }
  
  #addNoteHolder{
	  position:absolute;
	  background-color:#fff;
	  top:0px;
	 
	  height:400px;
	  display:none;	  
  }
  
  .streetViewNotesHolderNote{
	  font-size:0.6em;
	  margin-top:15px;
	  
  }

  
</style>

<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?sensor=false">
</script>


<script type="text/javascript">
/*
	var toDoCount = <?=$toDoCount?>;
*/
</script>



<script type="text/javascript">
  function initialize() {
    var latlng = new google.maps.LatLng(40.6511, -73.9490);
    var myOptions = {
      zoom: 13,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
	  minZoom: 13
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);

	

	this.map = map;
	
	$.panorama=null;
	
	this.allMarkers=new Array();

	
	var southWestLimit = new google.maps.LatLng(40.5562, -74.0583);
	var northEastLimit = new google.maps.LatLng(40.7064, -73.8566);		
	var allowedBounds = new google.maps.LatLngBounds(southWestLimit, northEastLimit);

	this.allowedBounds = allowedBounds;

	//inital size of the markers
	this.markerSize= new google.maps.Size(50, 50);
	this.markerAnchor= new google.maps.Point(15, 30);	

/*
	this.shadow = new google.maps.MarkerImage('img/ring_shadow.png',
      // The shadow image is larger in the horizontal dimension
      // while the position and offset are the same as for the main image.
      new google.maps.Size(50, 50),
      new google.maps.Point(0,0),
      new google.maps.Point(15, 30));	
*/
	//the are you done infowindow		
	this.infowindowLockImage = new google.maps.InfoWindow({
		content: '<div class="infoWindowLock">Click and drag again to get the <br />location just right (try zooming in).<br />When you are finished:<br /><input type="button" value="Click to Lock Location" onclick="lockMarkers(); return false;"/></div>'
	});	
	
		
    // Add a move listener to restrict the bounds range
	google.maps.event.addListener(map, 'idle', function() {
		checkBounds(map,allowedBounds);
	});		

	google.maps.event.addListener(this.map, 'zoom_changed', function() {
		updateZoom();
	});


	google.maps.event.addListener(this.map, 'mousemove', function(mEvent) {
		setCurrentLatLng(mEvent.latLng.lat(),mEvent.latLng.lng());


	});	
	
	
		
	
  }
	
	
	//keeps track of the lat lng in order to add the drag and drop image into roughly the right spot
	function setCurrentLatLng(lat,lng){
		this.currentLat = lat;
		this.currentLng = lng;	

	}
	
	//get active images
	function addActiveMarkers(){

		$.get("http://thisismattmiller.com/chart/", { "returnImages": true},
			function(data){


				var title='';
				var oldTitle='';
				
				
				
				$('#filterBoxTitleList').empty();

				for (aImage in data)
				{	
			
					
					title = filterNotesIntoTitle(data[aImage].notes);
					
					
					if (title!=oldTitle){
						//add it to the list
						$('#filterBoxTitleList').append(
							$('<li>')
								.text(title)
								.addClass('filterListItem')
								.addClass('liX')								
								.attr('title','Click to toggle diplay on map')								
								.data('title',title)
								.click(function(){toggleListItem($(this))})

								
						
						
						);
						
					}
	
	
	
					setCurrentLatLng(data[aImage].lat,data[aImage].lng);
					$.markerDataNotes = data[aImage].notes;
					$.markerPano = data[aImage].pov;
					$.markerDataId = data[aImage].museId;						
					$.markerIsNew=false;
	 
	
					addMarkerToCurrentPos();			
										
					oldTitle=title;
		
				}
				
				lockMarkers();
						
				//we want to display the resdicences by defualt				
				$('.filterListItem').each(function(index) { 
					if ($(this).data('title').toLowerCase() == 'brooklyn residences'){
					
						$(this).click();

						
					}
				
				
				});						
								
				
		}, "json");	
		
		
			
	 
		
	}
	
	
	function toggleListItem(useDom){
		 
			
		if ($(useDom).hasClass('liX')){
		
			//$(useDom).css('list-style-type','square');
			$(useDom).removeClass('liX');
			$(useDom).addClass('liCheck');			
			filterImages($(useDom).data('title'),true);
			
		}else{

			//$(useDom).css('list-style-type','none');
			$(useDom).removeClass('liCheck');
			$(useDom).addClass('liX');						
			filterImages($(useDom).data('title'),false);					
			
		}
		
		
	}
	
	
	function filterImages(useTitle,show){
		
		
		var arLen=this.allMarkers.length;
		for ( var i=0, len=arLen; i<len; ++i ){
			
			//remake the image with the new size and 

			
			
			if (this.allMarkers[i].dataTitleShort == useTitle){
			
			
			
				this.allMarkers[i].setAnimation(null);			
				this.allMarkers[i].setVisible(show);
				//console.log(this.allMarkers[i]);				


			}
			

			//this.allMarkers[i].setMap(this.map);
			
		}			
		
		 
	}
	
	
	
	
	
	//called when the drop of a new image happens
	function addMarkerToCurrentPos(){
	
		//rought location based off of the mouse
	  var location = new google.maps.LatLng(this.currentLat,this.currentLng);	
		
		//construct the marker
	  var id = $.markerDataId;
	  var title = $.markerDataNotes;	
	  var isNew = $.markerIsNew;
	  var panoData = $.markerPano;	  
	  var titleShort=filterNotesIntoTitle($.markerDataNotes);
	   
	   
	   
	  if (isNew){
	
		var visible = true; 
		var isDraggable = true; 
		  
	  }else{
		var visible = false;		
		var isDraggable = false;		  
	  }

	  
	  
	  var image = new google.maps.MarkerImage('img/'+id+'_ring.png',
		  this.markerSize,
		  // The origin for this image is 0,0.
		  new google.maps.Point(0,0),
		  // The anchor for this image is the base/middle of image
		  this.markerAnchor,
		  //scale image
		  this.markerSize);
		  
	  	
		marker = new google.maps.Marker({
			map:this.map,
			draggable: isDraggable,
			animation: google.maps.Animation.DROP,
			position: location,			
			visible: visible,
			icon: image,
			title: '(Click For More)',
			dataId: id,
			dataTitle: title,
			dataTitleShort:titleShort,
			dataPano: panoData
		});
		//google.maps.event.addListener(marker, 'click', toggleBounce);		
		
		
		
		

  		if (isNew){
			this.infowindowLockImage.open(this.map,marker);
		}

		this.allMarkers.push(marker);

		
		
		google.maps.event.addListener(marker, 'mouseover', function() {
			
			//if ($('#previewBox').css('visibility') != 'visible'){				
			
				$('#previewBox').css('visibility','visible');										
				$('#previewBox').fadeIn('fast');
				//$('#previewBox').show('slide', {direction: 'left'}, 300, function() {  });
				
				
			//}
			
			
			
			
			$("#previewBoxImage").css('background-image','url(' + 'img/' + this.dataId + '_medium.jpg'  + ')');
			$("#previewBoxTitle").text(this.dataTitle);

		});	


		google.maps.event.addListener(marker, 'mouseout', function() {
			
			//$('#previewBox').hide('slide', {direction: 'left'}, 300, function() {
			//	$('#previewBox').css('visibility','hidden');
			//});
			$('#previewBox').fadeOut(10);
		
		
		});	
		

		google.maps.event.addListener(marker, 'click', function() {
			
		 	openDetailWindow(this);
		
		});			
		
			
		
	}
	
	
	
	function openDetailWindow(marker){
		
	 
		$.activeId = marker.dataId; 
		 
		 
		$('#modalLargeImage').empty();
		
		$('#modalLargeImage').append(
			$("<img>")
				.attr('src','img/'+marker.dataId+'_large.jpg')
				.css('width','auto')
				.css('height','425px')
				.attr('id','modalLargeImageImg')
				.load(function () {
					
					
					//alert($('#modalLargeImage img').attr('offsetWidth'));
					
					$('#modalPano').css('height', $('#modalLargeImage img').outerHeight());
					$('#modalPano').css('width', $('#modalLargeImage img').outerWidth());	
					$('#streetViewControlsStart').css('width', $('#modalLargeImage img').outerWidth());				
					$('#streetViewControlsEdit').css('width', $('#modalLargeImage img').outerWidth());				
					$('#streetViewControlsPresent').css('width', $('#modalLargeImage img').outerWidth());								
					$('#streetViewImageHolder').css('width', $('#modalLargeImage img').outerWidth()+5);													
										
					var remainder = 860 - $('#modalLargeImage img').outerWidth();
					$('#streetViewMetaHolder').css('width', remainder);													
					 										
										
					//alert($('#modalLargeImage img').outerHeight());
					//$('#modalPano').css('left','17px');
					//$('#modalPano').css('top','7px');							
					
					
				})
				.each(function(){
					if(this.complete){ $(this).trigger("load");}
				})
		
		
		
		);
			 

		//get any notes
		$('#streetViewNotesHolder').empty();
		returnNotes(marker.dataId);
		 
		
		


		
		//what is the streetview status?
		
		
		$('#streetViewControlsStart').css('display','none');		
		$('#streetViewControlsPresent').css('display','none');		
		$('#streetViewControlsEdit').css('display','none');		
		 
		//console.log('doing pano, data:' +marker.dataPano); 
		 
		 
		 
		 
		if (marker.dataPano==''){
			
			
			$('#streetViewControlsStart').css('display','block');		
			var panoramaOptions = {
			  position: marker.position,
			  addressControl: true,
			  linksControl: true,
			  visible: false
			};			
			
				
		}else{
			
			
			
			var temp = new Array();
			temp = marker.dataPano.split(',');			
			
			var lat = parseFloat(temp[0]);
			var lng = parseFloat(temp[1]);			
			var heading = parseFloat(temp[2]);
			var pitch = parseFloat(temp[3]);
			var zoom = parseFloat(temp[4]);						
			
			var position = new google.maps.LatLng(lat,lng);
			 				 
			var panoramaOptions = {
			  position: position,
			  addressControl: false,
			  linksControl: false,
			  panControl: false,
			  zoomControl: false,
			  visible: true,
			  pov: {
				heading: heading,
				pitch: pitch,
				zoom: zoom
			  }			  
			  
			};			 
			
			$('#modalPano').fadeTo('fast', 0);
			$( "#streetViewPresentSlider" ).slider( "option", "value", 0 );
			$('#streetViewControlsPresent').css('display','block');		
			
			
			
			
		}
		
		 $('#streetViewTitle').html(marker.dataTitle +  '<a target="_blank" href="http://www.brooklynmuseum.org/opencollection/archives/image/' + marker.dataId + '/"><img border="0" src="img/bm.png"/></a>');

		
		delete $.panorama;
		
		$.panorama = new  google.maps.StreetViewPanorama(document.getElementById("modalPano"),panoramaOptions);
		map.setStreetView($.panorama);	
		
				
		
		$( "#dialog-modal" ).dialog( "option" , "title" , marker.dataTitleShort);
		
		$( "#dialog-modal" ).dialog( "open" );
		$( "#addNoteButtonNvm" ).click();

delete $.panorama;

                $.panorama = new  google.maps.StreetViewPanorama(document.getElementById("modalPano"),panoramaOptions);
                map.setStreetView($.panorama);

delete $.panorama;

                $.panorama = new  google.maps.StreetViewPanorama(document.getElementById("modalPano"),panoramaOptions);
                map.setStreetView($.panorama);


		
		  
		$('#modalPano').css('height', $('#modalLargeImage img').outerHeight());
		$('#modalPano').css('width', $('#modalLargeImage img').outerWidth());		
		
		
		google.maps.event.trigger($.panorama, 'resize');
		
		$('#streetViewControlsStart').css('width', $('#modalLargeImage img').outerWidth());				
		$('#streetViewControlsEdit').css('width', $('#modalLargeImage img').outerWidth());				
		$('#streetViewControlsPresent').css('width', $('#modalLargeImage img').outerWidth());								
		$('#streetViewImageHolder').css('width', $('#modalLargeImage img').outerWidth()+5);													

		var remainder = 860 - $('#modalLargeImage img').outerWidth();
		$('#streetViewMetaHolder').css('width', remainder);		

		

	}
	
	
	
	
	
	
	//this just locks all the markers down, called after positioning is done
	function lockMarkers(){
		
		
		//close the infobox that is probablly open
		this.infowindowLockImage.close();
		//loop		
		var arLen=this.allMarkers.length;
		for ( var i=0, len=arLen; i<len; ++i ){

		  if (this.allMarkers[i].draggable){
			//this one is not saved yet because it can be dragged  
			 
			 savePlacement(this.allMarkers[i].dataId,this.allMarkers[i].getPosition().lat(),this.allMarkers[i].getPosition().lng());
			  
			  
			  
		  }
			
		  this.allMarkers[i].draggable=false;
		  this.allMarkers[i].setMap(this.map)
		  
		  
		  
		  //savePlacement
		  
		}		
		
	}
	
	
	//this changes the sizes of the marker images, it scales so they are smaller when zoomed out but get bigger as the user explores
	function updateZoom(){
	
		//set the zoom level that will tell new markers how big to be
				
		if (this.map.zoom==13){
			
			var size = 50;

			
		}else if (this.map.zoom==14){
			
			var size = 70;
			
			
		}else if (this.map.zoom==15){
			
			var size = 80;
			
			
		}else if (this.map.zoom==16){
			
			var size = 90;
			
			
		}else if (this.map.zoom==17){
			
			var size = 130;
			
			
		}else if (this.map.zoom==18){
			
			var size = 160;
			
			
		}else if (this.map.zoom==19){
			
			var size = 180;
			
			
		}
		
		this.markerSize= new google.maps.Size(size, size);
		this.markerAnchor= new google.maps.Point(size/2,size);		
			
	
		
		//update the sizes 
		var arLen=this.allMarkers.length;
		for ( var i=0, len=arLen; i<len; ++i ){
			
			//remake the image with the new size and 
			var image = new google.maps.MarkerImage('img/'+ this.allMarkers[i].dataId +'_ring.png',
			  this.markerSize,
			  // The origin for this image is 0,0.
			  new google.maps.Point(0,0),
			  // The anchor for this image is the base/middle of image
			  this.markerAnchor,
			  //scale image
			  this.markerSize);				
				
			
			this.allMarkers[i].icon=image;
			this.allMarkers[i].animation=null;
			this.allMarkers[i].setMap(this.map);
		}			
		
		
		//this.markSize=new google.maps.Size(50, 50);
			
		
		
		
	}

	function panToResults(LatLng){

				if (this.allowedBounds.contains(LatLng)){					
					//it is so center the map on that spot
					
					this.map.panTo(LatLng);
					if (this.map.zoom<15){
						this.map.setZoom(15);
					}
					
					//throw a temp marker on it to grab thier attention
					this.tempMarker = new google.maps.Marker({
						map:this.map,
						draggable:false,
						animation: google.maps.Animation.BOUNCE,
						position: LatLng
					});					
					setTimeout("removeTempMarker()", 3000);
				}else{
				
					alert('Sorry, Could not find that in the Brooklny Area');	
					
				}
	}
	//thus just removes any tempory markers on the map;
	function removeTempMarker(){
		this.tempMarker.setMap(null);
	}
	
	

	
	
	

	//this uses the google maps geocode api to find something in map, 
	function searchMap(){
				
		//send the ajax to the server that then sends it to google because of cross domain request
		$.get("http://thisismattmiller.com/chart/", { "address": $('#formSearch_text').val(), "sensor" : "false" },
			function(data){

				if (data.status!='OK'){
					alert('Sorry, could not find that');
					return false;					
				}
				
				
				
				
				
				//just grab the first result
				var lat = data.results[0].geometry.location.lat;
				var lng = data.results[0].geometry.location.lng;				
				
				if (lat == 40.6500000 && lng == -73.9500000){
					alert('Sorry, could not find that');
					return false;						
				}
		
				panToResults(new google.maps.LatLng(lat,lng));
				//console.log(data); 
				//alert(lat + ' x ' + lng);
				
				//check if this results is within bounds

				
								
				
		}, "json");	
	
	
		
		
	}


  function savePlacement(id,lat,lng){
	
		$.get("http://thisismattmiller.com/chart/", { "id": id, "lat" : lat, "lng" : lng},
			function(data){
				
		}, "json");		  
	  
	  
  }
  
  function savePov(id,pov){
	
		$.get("http://thisismattmiller.com/chart/", { "id": id, "pov" : pov},
			function(data){				
			
			
		}, "json");		  
	  

		//also update the marker of the current one was just sent to the server
		var arLen=this.allMarkers.length;
		
		for ( var i=0, len=arLen; i<len; ++i ){ 
			if (this.allMarkers[i].dataId == id){
				
				this.allMarkers[i].dataPano = pov;			
				
				$( "#dialog-modal" ).dialog("close"); 				
				



			}
		}				  
	  
	  
  }  
  
  function returnNotes(museId){
		$.get("http://thisismattmiller.com/chart/", { "returnNotes": museId},
			function(data){				
				 
				 
				$('#streetViewNotesHolder').empty();
				 
				 
				for (aNote in data)
				{		
				 
					var note = data[aNote].note;
					note = note.replace(/&#39;/g,"'");
					note = note.replace(/&#34;/g,'"');
					note = note.replace('&quot;','"');								
					
					
					$('#streetViewNotesHolder').append(
						$('<div>')
							.text(note)
							.addClass('streetViewNotesHolderNote')				
					
					);
					
				
				}
				
			});
	  
	  
  }
  
  



  //we want to limit going too far from brooklyn, if they move out of bounds return to it.
  function checkBounds(map,allowedBounds) {
	// Perform the check and return if OK
	if (allowedBounds.contains(map.getCenter())) {
	  return;
	}	
	
	// It`s not OK, so find the nearest allowed point and move there
	var C = map.getCenter();
	var X = C.lng();
	var Y = C.lat();

	var AmaxX = allowedBounds.getNorthEast().lng();
	var AmaxY = allowedBounds.getNorthEast().lat();
	var AminX = allowedBounds.getSouthWest().lng();
	var AminY = allowedBounds.getSouthWest().lat();

	if (X < AminX) {X = AminX;}
	if (X > AmaxX) {X = AmaxX;}
	if (Y < AminY) {Y = AminY;}
	if (Y > AmaxY) {Y = AmaxY;}
	map.setCenter( new google.maps.LatLng(Y,X));	
  }
   
   
   
   
   function updateToDoCount(){
		$.get("http://thisismattmiller.com/chart/", { "toDoCount": true},
			function(data){				
				$('#toDoCount').text(data+' Left');
		}, "json");		   
   }
   
   
   function closePreviewBox(){
	   
	   $('#previewBox').show('slide', {direction: 'right'}, 700);
   }

	function filterNotesIntoTitle(notes){

		notes = notes.substring(notes.search(/\./)+1);
		notes = notes.substring(0, notes.search(/View/));
		notes = notes.replace('S., ','');
		notes = notes.replace(/\./g,'');
		notes = notes.replace(/brooklyn brooklyn/ig,'Brooklyn');					
		notes = notes.replace('  ',' ');					
		notes = notes.replace(/^\s+|\s+$/g, '');		
		notes = notes.replace('Brooklyn,','Brooklyn');							
		
		
		return notes;
	}


   
   function buildToDoImages(){
	   
	   $('#todoBoxImages').empty();
	   $('#todoBoxImages').text('Loading Images');
	   
		$.get("http://thisismattmiller.com/chart/", { "toDoImages": true},
			function(data){				
				
				
				$('#todoBoxImages').text('');
				
				
				
				var oldnotes = '';
				
				
				for (aImage in data.results)
				{				
				
					var notes = filterNotesIntoTitle(data.results[aImage].notes);	

					if (oldnotes != notes){
						
						$('#todoBoxImages')
							.append(
								$("<div>")
									.text(notes)
									.addClass('todoBoxImagesHeader')
							
						);
					
						
						
					}
				
				
					$('#todoBoxImages')
						.append(
							$("<img>")
								.attr('src','img/' + data.results[aImage].museId + '_ring.png')
								.attr('height','70')
								.attr('width','70')
								.data('id',data.results[aImage].museId)
								.data('pano',data.results[aImage].pov)								
								.data('notes',data.results[aImage].notes)																						
								.mouseover(function() {
									
									$('#previewBox').css('visibility','visible');										
									$('#previewBox').fadeIn('fast');

									$("#previewBoxImage").css('background-image','url(' + 'img/' + $(this).data('id') + '_medium.jpg'  + ')');
									$("#previewBoxTitle").text($(this).data('notes'));
									
									
								})
																		
			
						
						);
						
					oldnotes=notes;
				
				}
				$( "#todoBox img" ).draggable({ 
					cursorAt: { bottom : -5 },
					start:function(event, ui){ lockMarkers(); }, 
					stop: function(event, ui){	
					
						

						
						
						//are we clear of the lightbox yet?
						if ($.clearOfLightBox==false){return;}

						//store the data we are about to drop
						$.markerDataNotes = $(this).data('notes');
						$.markerDataId = $(this).data('id');						
						$.markerIsNew=true;
						$.markerPano=$(this).data('pano');


						$(this).remove();							
						//so Internet explore has a behavior that it does not track the lat/lng correctly when we are dragging
						//imgs out of the div over the map, but it does register just after you let go, so delay the action until
						//right after we left go of the img.

						setTimeout("addMarkerToCurrentPos()", 10);
						
						
						
						
					},
					helper: 'clone',
					scroll: false,
					appendTo: 'body'
				
				});				
				
				
				
				
				
		}, "json");		   
   }   
   
   
   
   
   
   
    
	$(document).ready(function(){ 
		initialize();
		
		


		$( "#dialog-modal" ).dialog({
			height: 510,
			width:900,
			modal: true,
			autoOpen: false,
			close: function(event, ui) { 
				
				$('#streetViewControlsStart').css('display','block');
				$('#streetViewControlsEdit').css('display','none');						
				$('#streetViewControlsPresent').css('display','none');
				$('#modalPano').fadeTo('fast', 0);			
				$.panorama.setVisible(false);			
				
			
			
			
			}

		});	


		
		$('#formSearch').submit(function() {
	      searchMap();
		  return false;
		});		
		
		
		
		$('#todoBoxImages').mouseout(function() {
			
						$('#previewBox').fadeOut(10);
			


		});		
		
		
		
		
		$( "#streetViewPresentSlider" ).slider({				
			slide: function(event, ui) {			
				var value = $(this).slider( "option", "value" );				
				value=value/100;				
				$('#modalPano').fadeTo(1, value);
			},
			change: function(event, ui) {			
				var value = $(this).slider( "option", "value" );				
				value=value/100;				
				$('#modalPano').fadeTo(1, value);
			}		
		});		
		
		$( "#streetViewEditSlider" ).slider({				
			slide: function(event, ui) {			
				var value = $(this).slider( "option", "value" );				
				value=value/100;				
				$('#modalPano').fadeTo(1, value);
			},
			change: function(event, ui) {			
				var value = $(this).slider( "option", "value" );				
				value=value/100;				
				$('#modalPano').fadeTo(1, value);
			}		
		});
		
		
		//buttons
		
		
		
		
		$( "#linkRemoveItem" ).click(function() {
			
			if (confirm("Are you positve remove this from the map? All data will be remove.")){			
				
				$.get("http://thisismattmiller.com/chart/", { "removeItemFromMap": $.activeId},
					function(data){		
							
							    
							  alert('Done, you will have to refresh the page to see the change.');
	  		  				  $( "#dialog-modal" ).dialog("close"); 				
				  
				}, "json");					
			
			
			}
			  
			 
		});
		

		
		$( "#linkAddNote" ).click(function() {
			
			$('#addNoteHolder').fadeIn('fast');
			$('#addNoteHolder').css('display','block');
			  
			  
			 
		});
		
				
		
		$( "#addNoteButtonAdd" ).button({
			text: true
		})
		.click(function() {
			
			
			var note = $('#addNoteText').val();			
			note = encodeURIComponent(note);
							
			$.get("http://thisismattmiller.com/chart/", { "note": note, "id": $.activeId},
				function(data){		
						 
				  $('#addNoteText').val('');			  
				  $('#addNoteHolder').fadeOut('fast');			
				  
			}, "json");				
			  
			 
		});	
		
		
		
		
		
		$( "#addNoteButtonNvm" ).button({
			text: true
		})
		.click(function() {
			  
			  
			  $('#addNoteText').val('');			  
			  $('#addNoteHolder').fadeOut('fast');
			 
		});			
				
		
		$( "#streetViewButtonSave" ).button({
			text: true
		})
		.click(function() {
			 
			 

			var pov = $.panorama.getPov();			
			var latLng = $.panorama.getPosition();			
			
			
			var povString = latLng.lat() + ',' + latLng.lng() + ',' +  pov.heading + ',' + pov.pitch + ',' + pov.zoom;		
			 
			savePov($.activeId,povString);
			
			
			
			 
			 
		});	
		
		$( "#streetViewButtonNvm" ).button({
			text: true
		})
		.click(function() {
			
			$('#modalPano').fadeTo('fast', 0);
			$.panorama.setVisible(false);		
			$('#streetViewControlsStart').css('display','block');
			$('#streetViewControlsEdit').css('display','none');				
			 
		});			
		
		
				
		$( "#streetViewButtonAdd" ).button({
			text: true
		})
		.click(function() {
			
			
			$('#streetViewControlsStart').css('display','none');
			$('#streetViewControlsEdit').css('display','block');			
			$( "#streetViewEditSlider" ).slider( "option", "value", 75 );
			$('#modalPano').fadeTo('fast', 0.75);
			$.panorama.setVisible(true);
			
		 
		});				
		
		$( "#buttonAdd" ).button({
			text: true
		})
		.click(function() {
			buildToDoImages();
			$('#todoBox').show('slide', {direction: 'right'}, 700);
			
			$("button").removeClass('ui-state-hover ui-state-focus');
			$('#previewBox').css('right','270px');		

		});		

		$( "#buttonFilter" ).button({
			text: true
		})
		.click(function() {
			$('#filterBox').show('slide', {direction: 'up'}, 700,function() {});
		});	
				
		
		$( "#buttonSearch" ).button()
		.click(function() {
			$("button").removeClass('ui-state-hover ui-state-focus');
			$('#searchBox').show('slide', {direction: 'up'}, 700,function() {$('#formSearch_text').focus(); $('#formSearch_text').select(); $("button").removeClass('ui-state-hover ui-state-focus');});
		});			




		$( "#buttonCloseFilter" ).button({
			text: true
		})
		.click(function() {
			$('#filterBox').hide('slide', {direction: 'up'}, 700);
		});			



		$( "#buttonCloseSearch" ).button({
			text: true
		})
		.click(function() {
			$('#searchBox').hide('slide', {direction: 'up'}, 700);
		});			
		
		$( "#buttonTodoClose" ).button({
			text: true
		})
		.click(function() {
			$('#todoBox').hide('slide', {direction: 'right'}, 700);
			$('#previewBox').css('right','10px');					
		});				
		
		
		
		
		
		//this keeps track of the mouse if it over the light box, to prevent mishaps with the drag and drop
		if (navigator.appName != 'Microsoft Internet Explorer'){
			$('#todoBox').mouseout(function() {$.clearOfLightBox=true;});
			$('#todoBox').mouseover(function() {$.clearOfLightBox=false;});		
			$.clearOfLightBox=false;
		}else{
			//internet explorer thinks that even while dragging the dragged opbject is withing the lightbox DIV, FIXME			
			$.clearOfLightBox=true;
		}
		
		
		$("#previewBoxClose").click(function() {
			$('#previewBox').hide('slide', {direction: 'left'}, 700, function() {
				$('#previewBox').css('visibility','hidden');
			});
			
			return false;
		});
		
		
		//some setup
		$('#searchBox').hide('slide', {direction: 'up'}, 1,function() {
			$('#searchBox').css('visibility','visible');
		});
		$('#filterBox').hide('slide', {direction: 'up'}, 1,function() {
			$('#filterBox').css('visibility','visible');
		});		
		$('#todoBox').hide('slide', {direction: 'right'}, 1,function() {
			$('#todoBox').css('visibility','visible');
		});	
		$('#previewBox').hide('slide', {direction: 'left'}, 1,function() {

		});				
		


		
		
		
		
		updateToDoCount();
		addActiveMarkers();



				
		
	});  
	
	
	
  

</script>






<title>Chart Demo</title>
</head>



<body>


	<div id="map_canvas" style="width:100%; height:100%"></div>

    <span id="toolBar" class="ui-widget-header ui-corner-all">
    
	    <button id="buttonFilter">Filter Images</button>    
        <button id="buttonAdd">Add Images To Map (<span id="toDoCount"></span>)</button>    
        <button id="buttonSearch">Search For Location</button>
        
   
    </span>
    
    <div id="searchBox" class="ui-widget-header ui-corner-all">
    
        
    <form id="formSearch" name="formSearch" action="/">
        <input type="text" id="formSearch_text" class="ui-corner-all" name="formSearch_text" value="Brooklyn Museum" />
    	<div>
        Enter a term and hit enter to search the Brooklyn Area. Try terms like:
        </div>
        <ul>
        	<li>Church and Bedford Ave</li>
        	<li>Brooklyn Heights</li>            
        	<li>Lefferts House</li>                        
        </ul>
        
        
    </form>
	    <div style="text-align:center"><button id="buttonCloseSearch">Close</button> </div>
    </div>
    

    <div id="filterBox" class="ui-widget-header ui-corner-all">
    	
        <ul id="filterBoxTitleList">
        
        
        </ul>
        
        
 
	    <div style="text-align:center"><button id="buttonCloseFilter">Close</button> </div>
    </div>

	<div id="todoBox" class="ui-widget-header ui-corner-all"> 
    	<div class="todoBoxInstruction">
        	Click image for more info. Click and Drag to place an image on the map. Don't worry, once placed it can be re-adjusted! 
        </div>    
        <div id="todoBoxImages"></div>
    
    	<div class="todoBoxInstruction">
			<button id="buttonTodoClose">Close</button>
        </div>
    
    </div>
    
    <div id="previewBox">
	    <a href="#" id="previewBoxClose">close</a>
    	<div id="previewBoxImage"></div>
        <div id="previewBoxTitle"></div>
        
    
    </div>
    
    

<div id="dialog-modal" title="Past and Present" style="padding:.5em 0.5em">
	
    <div id="streetViewImageHolder" style="float:left;">
		<div id="modalLargeImage"></div>
    	<div id="modalPano"></div>
        
        <div id="streetViewControlsStart" style="text-align:center;">
        	<button style="font-size:0.75em; margin-top:5px;" id="streetViewButtonAdd">Add Google Street View Layer</button>
        </div>
        
        <div id="streetViewControlsEdit">
        
        	<div style="float:left; margin-top:5px;"><button style="font-size:0.75em" id="streetViewButtonSave">Save View</button></div>
        	<div style="float:left; margin-top:5px;"><button style="font-size:0.75em" id="streetViewButtonNvm">Never Mind</button></div>
        	<div style="float:right; width:280px; height:35px; position:relative; margin-right:5px;">
            
            
            	<div id="streetViewEditSlider" style="margin-top:5px;" ></div>
            
            	<span>Past</span>
            	<span style="position:absolute; right:0px">Present</span>                
            
            	
            
            </div>                        
        	

        
        </div>        	
        <div id="streetViewControlsPresent">
        	<div style="width:280px; height:35px; position:relative; margin-right:auto; margin-left:auto;">

        	
		        <div id="streetViewPresentSlider" style="margin-top:5px;" ></div>
            	<span>Past</span>
            	<span style="position:absolute; right:0px">Present</span>                
                

                
                
                
                
            </div>
	        
        </div>	        
        
    </div>
    
    
    
    <div id="streetViewMetaHolder">
    
    	<div id="streetViewTitle"></div>
        
        <div id="streetViewNotes"></div>

				<div id="streetViewNotesHolder"></div>

                <div id="addNoteHolder">
                
                	<textarea id="addNoteText" style="height:200px; width:97%;">Enter Note Here</textarea>
                    <button style="font-size:0.75em" id="addNoteButtonAdd">Add Note</button>
                    <button style="font-size:0.75em" id="addNoteButtonNvm">Never Mind</button>
                    
                
                </div>    
    
    	<a id="linkAddNote" style="position:absolute; text-decoration:none; bottom:0px; font-size:14px; left:0px" href="#">Add Note</a>&nbsp;<a style="position:absolute; text-decoration:none; right:0px; bottom:0px; font-size:14px;" id="linkRemoveItem" href="#">Remove From Map</a>
    
    </div>
    
    
</div>


</body>




</html>

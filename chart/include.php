<?php



 
# Spherical Law of Cosines
function distance_slc($lat1, $lon1, $lat2, $lon2) {
  //global $earth_radius;
  //global $delta_lat;
  //global $delta_lon;
  
  $earth_radius = 3960.00; # in miles
  $delta_lat = $lat2 - $lat1 ;
  $delta_lon = $lon2 - $lon1 ; 
   
  $distance  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($delta_lon)) ;
  $distance  = acos($distance);
  $distance  = rad2deg($distance);
  $distance  = $distance * 60 * 1.1515;
  $distance  = round($distance, 4);
 
  return $distance;
}
 
 
 


function multisort($array, $sort_by, $key1, $key2=NULL, $key3=NULL, $key4=NULL, $key5=NULL, $key6=NULL){
    // sort by ?
    foreach ($array as $pos =>  $val)
        $tmp_array[$pos] = $val[$sort_by];
    asort($tmp_array);
    
    // display however you want
    foreach ($tmp_array as $pos =>  $val){
        $return_array[$pos][$sort_by] = $array[$pos][$sort_by];
        $return_array[$pos][$key1] = $array[$pos][$key1];
        if (isset($key2)){
            $return_array[$pos][$key2] = $array[$pos][$key2];
            }
        if (isset($key3)){
            $return_array[$pos][$key3] = $array[$pos][$key3];
            }
        if (isset($key4)){
            $return_array[$pos][$key4] = $array[$pos][$key4];
            }
        if (isset($key5)){
            $return_array[$pos][$key5] = $array[$pos][$key5];
            }
        if (isset($key6)){
            $return_array[$pos][$key6] = $array[$pos][$key6];
            }
        }
    return $return_array;
}


class Chart
{
	
	
		
	public $db=NULL;			//holds the database class object

	function __construct() {
			
		//connect to the database because we are likely going to do some DB shit
		require_once('db.class.php');		
		$this->db = new db_class;	
		
		//error_reporting(0);

		if (!$this->db->connect('192.168.171.37', 'chart', 'chart', 'chart', true)){
			
			$this->db->print_last_error(false);	
			die("Dorry Swags, we got database problems");
			
		}
			
					
	
	}	
	
	function __destruct() {
	   
	}	
	
	
		
	
	
	function returnNearByMarkers($lat,$lng,$max){
		
		
		$max = 1000;
		
		
		$lat=filter_var($lat, FILTER_SANITIZE_STRING);$lat=mysql_real_escape_string($lat);
		$lng=filter_var($lng, FILTER_SANITIZE_STRING);$lng=mysql_real_escape_string($lng);
		$max=filter_var($max, FILTER_SANITIZE_STRING);$max=mysql_real_escape_string($max);		

		

		
		
		$results = $this->db->select("select * from `images` WHERE  `placed`=1;");		

		$counter = 0;
		$data = Array();
		
		//echo mysql_num_rows($results) . " results";
		
		
		if (mysql_num_rows($results)==0){
		
				echo '{"results": []}';
				return false;
			
		}
		

		while($row = mysql_fetch_assoc($results)) {
			 
			
			
			$slc_distance = distance_slc($row['lat'], $row['lng'], $lat, $lng);
			
			if ($slc_distance < $max){
			
				$data[$counter]['distance']=$slc_distance;
				$data[$counter]['title']=$row['notes'];
				$data[$counter]['id']=$row['museId'];
				$data[$counter]['lat']=$row['lat'];
				$data[$counter]['lng']=$row['lng'];				
				
				
			}else{
			
				//echo "$slc_distance<br />";
				//print_r($row);
				
			}

			
			
			$counter++;			
		}
		
		
		
		
		$sorted = multisort($data,'distance','id','title','lat','lng');
		
		$counter=0;
		 
		echo '{"results": [';
		 
		$output =''; 
		 
		foreach ($sorted  as $aItem) {
			
			//echo print_r($aItem);
			if ($counter==15){
				
				continue;	
				
			}			
			
			$output .= "{";
			
			$title = $aItem['title'];
			
			$title = htmlentities($title,ENT_QUOTES); 
			
			$output .= '"id": "' . $aItem['id'] . '",';
			$output .= '"dist": "' . $aItem['distance'] . '",';
			$output .= '"title": "' . $title . '",';						
			$output .= '"lat": "' . $aItem['lat'] . '",';						
			$output .= '"lng": "' . $aItem['lng'] . '"';						
			
			
			$output .= "},";
			
			$counter++;
			

			
		}
		
		$output = substr($output,0,strlen($output)-1);
		
		echo $output;	
		
		echo ']}';
		
		
		
		
		
	}
	
	
	function returnImages(){
	
	
		echo sql2json("select * from `images` where `placed` = 1");
		
		
		
	}
	
	
	
	function returnUserImages($id){
	
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);

		$path = '/var/www/thisismattmiller.com/web/chart/img/users/*';
		$pattern = $id;
		 
		 
		$output = "[";
									 
		foreach (glob($path) as $fullname) {
			 
			 if (strpos($fullname,$id)!== false){
				 
				 
				 $filename = explode('/',$fullname);
				 $filename = $filename[count($filename)-1];
				 $output .= '{"filename":' . '"' . $filename . '"},';
				 
				 
			 }
		}
		
		$output = substr($output,0,strlen($output)-1);
		
		$output .= "]";		 
		
		echo $output;
		
		
	}	
	
		
	function returnNotes($id){
	
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);

		$notes = sql2json("select * from `notes` where `museId` = $id");
		
		if ($notes==''){
		
			echo "[]";
			
		}else{
			echo $notes;
				
		}
		
		
	}	
	
	
	function returnToDoImageCount(){


		$results = $this->db->select("select count(`id`) as `count` from `images` WHERE  `placed`=0;");		
		$row = mysql_fetch_assoc($results);
		$count = (int)$row['count'];	
		
		return '{"count": ' + $count + '}';
	
	
	}
	
	
	 
	
	function returnToDoImages(){

		
		$results = $this->db->select("select * from `images` WHERE `placed`=0;");		
		$row = mysql_fetch_assoc($results);
		
		
		if (mysql_num_rows($results)==0){
		
			return '{"results": []}';
			
			
		}
		
		$mtj = new mysql_to_json($results, 'results');
		$json = $mtj->get_json();	
				
		return $json;
		
		
	}	
	
	
	function savePlacement($id,$lat,$lng){
	
	
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);
		$lat=filter_var($lat, FILTER_SANITIZE_STRING);$lat=mysql_real_escape_string($lat);
		$lng=filter_var($lng, FILTER_SANITIZE_STRING);$lng=mysql_real_escape_string($lng);				
	

		$this->db->update_sql("UPDATE `images` SET `placed` = '1', `lat` = '$lat', `lng` = '$lng' WHERE `museId` = $id");		
		
		echo "[]";
		
	}
	
	function savePov($id,$pov){
	
	
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);
		$pov=filter_var($pov, FILTER_SANITIZE_STRING);$pov=mysql_real_escape_string($pov);
	
		$this->db->update_sql("UPDATE `images` SET `pov` = '$pov' WHERE `museId` = $id");		
		echo "[]";
		
	}	
	
	function saveNote($id,$note){
		
		$note = urldecode($note);
		$note=str_replace('"','&quot;',$note);	
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);				
		$note=filter_var($note, FILTER_SANITIZE_STRING);$note=mysql_real_escape_string($note);
			
	 
		$this->db->update_sql("INSERT INTO `notes` SET `note` = '$note', `museId` = $id");		
		echo "[]";
		
	}		


	function removeItemFromMap($id){
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);
		$this->db->update_sql("UPDATE `images` SET `lat` = '', `lng` = '', `placed` = 0, `pov` = '' WHERE `museId` = $id LIMIT 1");
		$this->db->update_sql("DELETE FROM `notes` WHERE `museId` = $id");		
		echo "[]";
	}		
	
	
	
	
	////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	function productList($filterSize = '',$filterType=''){
		
		$filterSize=filter_var($filterSize, FILTER_SANITIZE_STRING);$filterSize=mysql_real_escape_string($filterSize);
		$filterType=filter_var($filterType, FILTER_SANITIZE_STRING);$filterType=mysql_real_escape_string($filterType);


		if ($filterSize == '' && $filterType==''){
			$results = $this->db->select("select * from `products` ORDER BY `order` ASC");
		}
		if ($filterSize != '' && $filterType==''){			
			/*TODO When we get some data*/
			//$results = $this->db->select("select * from `products` WHERE `size ORDER BY `order` DESC");
		}
		
		if ($filterSize == '' && $filterType!=''){			
			$results = $this->db->select("select * from `products` WHERE `type` == '$filterType' ORDER BY `order` DESC");
		}		
		
		
		if (mysql_num_rows($results)==0){
		
			return '{"results": [],"inventory": []}';
			
			
		}
		
		
		$mtj = new mysql_to_json($results, 'results');
		$json = $mtj->get_json();		

		$results2 = $this->db->select("select * from `inventory` ORDER BY `product` ASC, `edition` ASC, `number` ASC");

		$mtj = new mysql_to_json($results2, 'inventory');
		$json2 = $mtj->get_json();				
		
		$json = str_replace("}]}" , "}],", $json);
		
		$json = str_replace(array("\r", "\r\n", "\n"), '', $json);
		
		$final =  $json . substr($json2,1);		
		
		$final = str_replace(chr(13), "", $final);		
		$final = str_replace(chr(10), "", $final);
		$final = str_replace("\n", "", $final);
		$final = str_replace("\r", "", $final);				
		$final = str_replace('\n', "", $final);
		$final = str_replace('\r', "", $final);			
		
		return $final; 				
				
		
	}



	function returnStaticInventory($id){
		
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);
		
		

		return $this->db->select("select * from `inventory` WHERE `product` = '$id' ORDER BY `product` ASC, `edition` ASC, `number` ASC");

		
	}

	//this returns stuff for the static page
	
	function returnStaticProduct($title){
		
		$title=filter_var($title, FILTER_SANITIZE_STRING);$title=mysql_real_escape_string($title);
		
		
		$title = str_replace('_',' ',$title);
		$title = str_replace('/','',$title);
		$title = str_replace('&39;',"'",$title);		


		

		return $this->db->select("select * from `products` WHERE `short_title` = '$title'");

		
	}
	
	
	function productImageRemove($id,$shirt){
	
		if (!is_numeric($id)){return 0;}
		if (!is_numeric($shirt)){return 0;}		
	
		if (is_file('/var/www/aoristic.us/web/img/shirt'.$id.'_'.$shirt.'.jpg')){
			unlink('/var/www/aoristic.us/web/img/shirt'.$id.'_'.$shirt.'.jpg');
		}
		if (is_file('/var/www/aoristic.us/web/img/shirt'.$id.'_'.$shirt.'.png')){
			unlink('/var/www/aoristic.us/web/img/shirt'.$id.'_'.$shirt.'.png');
		}
		
		return 1;
	}
	
	function productImageList($id){	
	
		$shirt1=0;$shirt2=0;$shirt3=0;$shirt4=0;$shirt5=0;
	
	
		//if (is_file("/var/www/aoristic.us/web/img/shirt$id_1.png")){$shirt1="shirt$id_1.png";}
		//if (is_file("/var/www/aoristic.us/web/img/shirt$id_1.png")){$shirt1="shirt$id_1.jpg";}
		//if (is_file("/var/www/aoristic.us/web/img/shirt$id_1.png")){$shirt1="shirt$id_1.gif";}
		
		
		if(is_file("/var/www/aoristic.us/web/img/shirt" . $id . "_1.png")){$shirt1=true;}
		if(is_file("/var/www/aoristic.us/web/img/shirt" . $id . "_2.jpg")){$shirt2=true;}
		if(is_file("/var/www/aoristic.us/web/img/shirt" . $id . "_3.jpg")){$shirt3=true;}
		if(is_file("/var/www/aoristic.us/web/img/shirt" . $id . "_4.jpg")){$shirt4=true;}
		if(is_file("/var/www/aoristic.us/web/img/shirt" . $id . "_5.jpg")){$shirt5=true;}

		
	
		echo '{
			"id": ' . $id . ',
			"shirt1": ' . $shirt1 . ',
			"shirt2": ' . $shirt2 . ',
			"shirt3": ' . $shirt3 . ',
			"shirt4": ' . $shirt4 . ',
			"shirt5": ' . $shirt5 . '
		}';
		
	
	
	}

	
	
	function productUpdate($id,$title='',$titleShort='',$desc='',$type='',$run=0,$printed=0,$enabled=0){
		
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);
		$title=filter_var($title, FILTER_SANITIZE_STRING);$title=mysql_real_escape_string($title);
		$titleShort=filter_var($titleShort, FILTER_SANITIZE_STRING);$titleShort=mysql_real_escape_string($titleShort);
		//$desc=filter_var($desc, FILTER_SANITIZE_STRING);$desc=mysql_real_escape_string($desc);
		$type=filter_var($type, FILTER_SANITIZE_STRING);$type=mysql_real_escape_string($type);
		$run=filter_var($run, FILTER_SANITIZE_STRING);$run=mysql_real_escape_string($run);
		$printed=filter_var($printed, FILTER_SANITIZE_STRING);$printed=mysql_real_escape_string($printed);
		$enabled=filter_var($enabled, FILTER_SANITIZE_STRING);$enabled=mysql_real_escape_string($enabled);


		$desc=str_replace('<scr','',$desc);
		$desc=str_replace('<?','',$desc);		
		

		$desc=htmlspecialchars($desc, ENT_QUOTES);

		if ($id == 0){
							
			$results = $this->db->select("select max(`order`) as `max` from `products`;");		
			$row = mysql_fetch_assoc($results);
			$order = (int)$row['max'] + 1;									
			$this->db->insert_sql("INSERT INTO `products` SET `title` = '$title', `desc` = '$desc', `order` = $order, `type` = '$type', `run` = $run, `printed` = $printed, `short_title` = '$titleShort'");
			
			return $id;
			
		}else{

			
			
			$this->db->update_sql("UPDATE `products` SET `title` = '$title', `desc` = '$desc', `type` = '$type', `run` = $run, `printed` = $printed, `short_title` = '$titleShort', `enable` = $enabled WHERE `id` = $id");
			

			return $id;			
		}
		
		
	}
	
	
	function productOrder($sort){
	
		$sort = explode(',',$sort);
		
		
		
		$counter = 0;
				
		foreach ($sort  as $aItem) {


			if ($aItem==''){continue;}

			//echo "aItem = $aItem<br />";

			
			$sql="UPDATE `products` SET `order` = $counter WHERE `id` = $aItem";
			//echo $sql;
			$this->db->update_sql($sql);
			$counter=$counter+1;
		}
		
		
		
	}
	
	


	
	function inventoryAdd($product, $size, $number, $total , $edition, $sold=''){
		$product=filter_var($product, FILTER_SANITIZE_STRING);$product=mysql_real_escape_string($product);
		$size=filter_var($size, FILTER_SANITIZE_STRING);$size=mysql_real_escape_string($size);
		$number=filter_var($number, FILTER_SANITIZE_STRING);$number=mysql_real_escape_string($number);
		$total=filter_var($total, FILTER_SANITIZE_STRING);$total=mysql_real_escape_string($total);
		$edition=filter_var($edition, FILTER_SANITIZE_STRING);$edition=mysql_real_escape_string($edition);
		
		if ($edition=='First'){$edition=1;}
		if ($edition=='Second'){$edition=2;}
		if ($edition=='Third'){$edition=3;}
		if ($edition=='Forth'){$edition=4;}						
		if ($edition=='Fifth'){$edition=5;}
		if ($edition=='Six'){$edition=6;}

		$edition=(int)$edition;
								
		return $this->db->insert_sql("INSERT INTO `inventory` SET `product` = $product, `size` = '$size', `number` = $number, `total` = $total, `edition` = $edition, `sold` = '$sold'");
		
	}
	
	function inventoryMarkSold($id, $sold){
		$sold=filter_var($sold, FILTER_SANITIZE_STRING);$sold=mysql_real_escape_string($sold);
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);
		
		
		if ($sold=='sold'){$sold = date("M j y");}
		
		
		$this->db->update_sql("UPDATE `inventory` SET `sold` = '$sold'  WHERE `id` = $id");
		
		return '{"status": "'.$sold.'"}';
		
		
		
		
	}	
	
	function inventoryRemove($id){
		$id=filter_var($id, FILTER_SANITIZE_STRING);$id=mysql_real_escape_string($id);
		return $this->db->update_sql("DELETE FROM  `inventory` WHERE `id` = $id");
	}	
	
	
	/*
	
	function returnSearch($search,$page){
		
		$pageSize=5;	
		
		
		if ( is_numeric($page) == false){$page=0;}
		
		
		
		$search=filter_var($search, FILTER_SANITIZE_STRING);
		$search=mysql_real_escape_string($search);				
		
			
		//find out how many there are
		$results = $this->db->select("select count(`id`) as ID from `phrases` where `phrase` like '%$search%' OR `definition` like '%$search%';");		
		$row = mysql_fetch_assoc($results);
		$count = (int)$row['ID'];		
		
		$perPage = round($count/$pageSize);
		
		$start = $page*$pageSize;
		$end = $start + $pageSize;
		
		$results = $this->db->select("select id,hash,phrase,definition,example,credit,score,date,state,city from `phrases` where `phrase` like '%$search%' OR `definition` like '%$search%' LIMIT $start, $end;");


		
		
		$mtj = new mysql_to_json($results, 'results');
		$json = $mtj->get_json();		
		
		if ($page==0){$navJson =  ',' . '"count":"' . $count . '"'  . ',"pagePrevious":"false"';}else{$navJson = ',' . '"count":"' . $count . '"'  . ', "pagePrevious":"true"';}

		
		if ($perPage==1){
			$navJson .= ',"pageNext":"false"';
		}else{		
			if ($page*$perPage>=$count){$navJson .= ',"pageNext":"false"';}else{$navJson .= ',"pageNext":"true"';}	
		}
		
		$json = str_replace("}]}" , "}] $navJson }", $json);
		
		return $json;
	
		
	}	
	*/
	
	
	
}


	
	

class mysql_to_json {
	var $json;
	var $cbfunc;
	var $json_array;

	//constructor
	function mysql_to_json($query = '', $cbfunc = '') {
		//set cbfunc
		$this->set_cbfunc($cbfunc);
		
		//check they don't just want a new class
		if($query != '') {
			//set query
			$this->set_query($query);
		}
	}

	//produces json output
	function get_json() {
		//generate json
		$this->json = '{' . '"' . $this->cbfunc . '":' . json_encode($this->json_array) . '}';

		//return json
		return $this->json;
	}
	
	//produces json from query
	function get_json_from_query($query, $cbfunc = '') {
		//set cbfunc
		$this->set_cbfunc($cbfunc);
		
		//set query
		$this->set_query($query);
		
		//return json data
		return $this->get_json();
	}
	
	//set query
	function set_query($query) {
		//reset json array
		$this->json_array = array();

		//loop through rows
		while($row = mysql_fetch_assoc($query)) {
			array_push($this->json_array, $row);			
		}
		
		//enable method chaining
		return $this;
	}
	
	//set cbfunc
	function set_cbfunc($cbfunc) {
		//set cbfunc
		$this->cbfunc = $cbfunc;

		//enable method chaining
		return $this;
	}
}	


function sql2json($query) {
    $data_sql = mysql_query($query) or die("'';//" . mysql_error());// If an error has occurred, 
            //    make the error a js comment so that a javascript error will NOT be invoked
    $json_str = ""; //Init the JSON string.

    if($total = mysql_num_rows($data_sql)) { //See if there is anything in the query
        $json_str .= "[\n";

        $row_count = 0;    
        while($data = mysql_fetch_assoc($data_sql)) {
            if(count($data) > 1) $json_str .= "{\n";

            $count = 0;
            foreach($data as $key => $value) {
                //If it is an associative array we want it in the format of "key":"value"
                if(count($data) > 1) $json_str .= "\"$key\":\"$value\"";
                else $json_str .= "\"$value\"";

                //Make sure that the last item don't have a ',' (comma)
                $count++;
                if($count < count($data)) $json_str .= ",\n";
            }
            $row_count++;
            if(count($data) > 1) $json_str .= "}\n";

            //Make sure that the last item don't have a ',' (comma)
            if($row_count < $total) $json_str .= ",\n";
        }

        $json_str .= "]\n";
    }

    //Replace the '\n's - make it faster - but at the price of bad redability.
    $json_str = str_replace("\n","",$json_str); //Comment this out when you are debugging the script

    //Finally, output the data
    return $json_str;
}

	
?>

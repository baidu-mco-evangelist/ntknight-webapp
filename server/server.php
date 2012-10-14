<?php

require_once('../pcs.class.php');

if($_SERVER['REQUEST_METHOD'] === "POST" ){
  $method = $_POST['method'];
}else{
  $method = $_GET['method'];
}

$access_token='3.cb3db0f757313660b764a2a9625d838b.2592000.1352254558.1831690938-379902';

//set dename 
$dbname = 'IPMtwoPWhephbxhWVYiL';

$host = getenv('HTTP_BAE_ENV_ADDR_SQL_IP');
$port = getenv('HTTP_BAE_ENV_ADDR_SQL_PORT');
$user = getenv('HTTP_BAE_ENV_AK');
$pwd = getenv('HTTP_BAE_ENV_SK');


$link = @mysql_connect("{$host}:{$port}",$user,$pwd,true);
if(!$link) {
	die("Connect Server Failed");
}

if(!mysql_select_db($dbname,$link)) {
	die("Select Database Failed: " . mysql_error($link));
}

mysql_query("set names utf8",$link);


//get activity list from mysql
if($method == "getActivityList"){

	$sql = "select activity_name,activity_time,activity_address,flag from activity_info order by activity_id asc";	
	$ret = mysql_query($sql,$link);
	
	$i = 0;
	$activityList = array();
	$activityTimeList = array();
	$activityAddress = array();
	$flagList = array();
	
	while($row = mysql_fetch_assoc($ret) ){
		$activityList[$i] = $row['activity_name'];
		$activityTimeList[$i] = $row['activity_time'];
		$activityAddress[$i] = $row['activity_address'];
		$flagList[$i++] = $row['flag'];
	}
	
	echo json_encode(array('activityList'=> $activityList,'activityTimeList'=>$activityTimeList,'activityAddressList'=>$activityAddress,'flagList'=>$flagList));

}


//add activity to list
if($method == "addActivity"){
	$activity_name = $_POST['activity_name'];
	$activity_time = $_POST['activity_time'];
	$activity_address = $_POST['activity_address'];
	$activity_introduce = $_POST['activity_introduce'];
	$count_person = $_POST['count_person'];
	
	$sql = "insert into activity_info(activity_name,activity_time,activity_address,activity_introduce,count_person) values('$activity_name','$activity_time','$activity_address','$activity_introduce','$count_person')";
	
	$ret = mysql_query($sql,$link);
	
	echo $sql;

}

//get activity information
if($method == "getActivityInfo"){

	$activity_name = $_GET["activity_name"];

	$sql = "select * from activity_info where activity_name='$activity_name'";
  
	$ret = mysql_query($sql,$link);
		
	while($row = mysql_fetch_assoc($ret) ){	
		$activity_time = $row['activity_time'];
		$activity_address = $row['activity_address'];
		$activity_introduce = $row['activity_introduce'];
		$count_person = $row['count_person'];
      	$flag = $row['flag'];
	}
	
	echo json_encode(array('activity_name'=> $activity_name,'flag'=>$flag,'activity_time'=> $activity_time,'activity_address'=> $activity_address,'activity_introduce'=> $activity_introduce,"count_person"=>$count_person));
	
}


//save photo to server
if($method == "savePhotoToServer"){

	$source_file = $_POST['source_file'];
	$target_file = urldecode($_POST['target_file']);

	$auth = array (
		'access_token' => $access_token,
	);
	
	$pcs = new BaiduPCS($auth);
	$pcs->set_ssl(true); 
	
	if (!($data = $pcs->upload_file($source_file,$target_file))){
      
		echo json_encode(array("error_message"=>$pcs->get_error_message()));
	} else {
		echo json_encode($data);
	}

}

//save photo to pcs
if($method == "savePhotoToPCS"){

	$access_token = $_GET['access_token'];
	$source_file = $_GET['source_file'];
	$target_file = $_GET['target_file'];

	$auth = array (
		'access_token' => $access_token,
	);
	
	$app = 'ntknight';

	$pcs = new BaiduPCS($auth);
	$pcs->set_ssl(true); 
	
	if (!($data = $pcs->upload_file($source_file,$target_file))){
      
		echo json_encode(array("error_message"=>$pcs->get_error_message()));
	} else {
		echo json_encode($data);
	}

}

//insert members infomation to mysql 
if($method == "addMember"){

	$member_name = $_POST['member_name'];
	$member_company = $_POST['member_company'];
	$member_position = $_POST['member_position'];
	$member_phone = $_POST['member_photo_number'];
	$member_Email = $_POST['member_Email'];
	$member_tech_field = $_POST['member_tech_field'];
	$hello_world = $_POST['hello_world'];
	$user_id = $_POST['user_id'];
	$user_name = $_POST['user_name'];
	
	$sql = "insert into member_info(member_name,member_company,member_position,member_phone,member_Email,member_tech_field,hello_world) values('$member_name','$member_company','$member_position','$member_phone','$member_Email','$member_tech_field','$hello_world')";
	 
	$ret = mysql_query($sql,$link);
	
	$sql = "select member_id from member_info ORDER BY member_id DESC LIMIT 0, 1";
	
	$ret = mysql_query($sql,$link);
	
	while($row = mysql_fetch_assoc($ret) ){
		$member_id = $row["member_id"];
	}
	 
	$sql = "insert into bd_link_member values('$user_id','$user_name','$member_id')";
 	$ret = mysql_query($sql,$link);
	
	echo json_encode(array('member_id'=> $member_id));	
}


//create Dir in server
if($method == "mkdirInServer"){

	$my_dir = $_POST["target"];

	$auth = array (
		'access_token' => $access_token,
	);
	echo $access_token;
  
  	echo $my_dir;
	
	$pcs = new BaiduPCS($auth);
	$pcs->set_ssl(true);

	/* mkdir */
	if (!($data = $pcs->create_dir($my_dir))) {
		echo json_encode(array("error_message"=>$pcs->get_error_message()));
	} else {
		echo json_encode($data);
	}

}

if($method == "getPhotoList"){
	$source_dir = $_GET["source_dir"];
  
 	$auth = array (
		'access_token' => $access_token,
	);

	$pcs = new BaiduPCS($auth);
	$pcs->set_ssl(true);

	/* list */
	if (!($data = $pcs->list_file($source_dir,"time"))) {
		echo json_encode(array("error_message"=>$pcs->get_error_message()));
	} else {
		$photoList = array();
      	for($i=0; $i<count($data); $i++){    		
          	foreach($data[$i] as $key => $value){
              if($key == 'path'){
              	$photoList[$i] = $value;
              }
            }          	
        }
		
      	echo json_encode(array("photoList"=>$photoList));
	}

}

//download photo
if($method == "downloadPhoto"){
	$source_file = urldecode($_GET['source_file']);

	$auth = array (
		'access_token' => $access_token,
	);
    
	$pcs = new BaiduPCS($auth);
	$pcs->set_ssl(true);
     echo $source_file;
	/* download photo */
  if (!($data = $pcs->download_file($source_file))) {
		echo json_encode(array("error_message"=>$pcs->get_error_message()));
	} else {
		echo json_encode(array("content"=>$data));
	}

}

//search member 

if($method == "searchMember"){
	
	$user_id = $_GET['user_id'];
	
	$sql = "select bd_id from bd_link_member ";
	
	$ret = mysql_query($sql,$link);
	$flag = 0;
	
	while($row = mysql_fetch_assoc($ret) ){
		 if($user_id == $row['bd_id']){
		 	$flag = 1;
		 }
	}
	
	if($flag == 0){
		echo json_encode(array('ret'=> "false" ));
	}else{
		echo json_encode(array('ret'=> "true"));
	}

}

mysql_close($link);

?>
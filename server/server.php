<?php

require_once('../pcs.class.php');
//require_once('../bcs.class.php');

if($_SERVER['REQUEST_METHOD'] === "POST" ){
  $method = $_POST['method'];
}else{
  $method = $_GET['method'];
}

//set dename 
$dbname = 'IPMtwoPWhephbxhWVYiL';

$sql_host = getenv('HTTP_BAE_ENV_ADDR_SQL_IP');
$port = getenv('HTTP_BAE_ENV_ADDR_SQL_PORT');
$user = getenv('HTTP_BAE_ENV_AK');
$pwd = getenv('HTTP_BAE_ENV_SK');



$link = @mysql_connect("{$sql_host}:{$port}",$user,$pwd,true);
if(!$link) {
	die("Connect Server Failed");
}

if(!mysql_select_db($dbname,$link)) {
	die("Select Database Failed: " . mysql_error($link));
}

mysql_query("set names utf8",$link);


//get activity list from mysql
if($method == "getActivityList"){

	$sql = "select activity_id,activity_name,activity_time,activity_address,flag,main_banner,myntbanner,main_new_banner from activity_info order by activity_id asc";	
	$ret = mysql_query($sql,$link);
	
	$i = 0;
	$activityList = array();
	$activityTimeList = array();
	$activityAddress = array();
	$flagList = array();
	$currentPerson = array();
	$activityIdList = array();
	$mainBannerList = array();
	$myntBannerList = array();
	$mainNewBanner = array();
	
	while($row = mysql_fetch_assoc($ret) ){
		$activityIdList[$i] = $row['activity_id'];
		$activityList[$i] = $row['activity_name'];
		$activityTimeList[$i] = $row['activity_time'];
		$activityAddress[$i] = $row['activity_address'];
		$mainBannerList[$i] = $row['main_banner'];
		$myntBannerList[$i] = $row['myntbanner'];
		$mainNewBanner[$i] = $row['main_new_banner'];
		
 		$activity_id = $row['activity_id'];
		$selectIDSql = "select count(member_id) from member_join_activity where activity_id='$activity_id'";
		$ret2 = mysql_query($selectIDSql,$link);
		
		while($count_member = mysql_fetch_assoc($ret2)){
			$currentPerson[$i] = $count_member['count(member_id)'];
		}
				
		$flagList[$i++] = $row['flag'];
	}
	
	echo json_encode(array('activityIdList'=>$activityIdList,'activityList'=> $activityList,'activityTimeList'=>$activityTimeList,'activityAddressList'=>$activityAddress,'flagList'=>$flagList,'currentPersonList'=>$currentPerson,'mainBannerList'=>$mainBannerList,'myntBannerList'=>$myntBannerList,'mainNewBanner'=>$mainNewBanner));

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
		$activity_id = $row['activity_id'];
		
		$selectIDSql = "select count(picture_url) from activity_picture where activity_id='$activity_id'";
		$ret2 = mysql_query($selectIDSql,$link);
		
		while($count_picture_row = mysql_fetch_assoc($ret2)){
			$count_picture = $count_picture_row['count(picture_url)'];
		}
		
		$selectCommentSql = "select count(comment) from activity_comment where activity_id='$activity_id'";
		
		$ret3 = mysql_query($selectCommentSql,$link);
		
		while($count_momment_row = mysql_fetch_assoc($ret3)){
			$count_comment = $count_momment_row['count(comment)'];
		}
		
		$activity_time = $row['activity_time'];
		$activity_address = $row['activity_address'];
		$activity_introduce = $row['activity_introduce'];
		$count_person = $row['count_person'];
      	$flag = $row['flag'];
	}
	
	echo json_encode(array('activity_name'=> $activity_name,'flag'=>$flag,'activity_time'=> $activity_time,'activity_address'=> $activity_address,'activity_introduce'=> $activity_introduce,'count_person'=>$count_person,'count_picture'=>$count_picture,'count_comment'=>$count_comment));
	
}

//get picture list
if($method == "getPictureList"){
	
	$activity_id = $_GET['activity_id'];
	
	$sql="select picture_url,picture_name from activity_picture where activity_id='$activity_id'";
	
	$ret = mysql_query($sql,$link);
	$pictureUrlList = array();
	$pictureNameList = array();
	$i = 0;
	
	while($row = mysql_fetch_assoc($ret)){
		$pictureUrlList[$i] = $row['picture_url'];
		$pictureNameList[$i++] = $row['picture_name'];
	}
	 
	echo json_encode(array('pictureUrlList'=>$pictureUrlList,'pictureNameList'=>$pictureNameList));
}


//save photo to server
if($method == "savePhotoToServer"){
	
	$activity_id = $_GET['activity_id'];
	$object = $_GET['source_path'];
	$picture_name = $object.substr(strrpos('/')+1,strrpos('.'));
	$fileUpload = $_GET['target_path'];
	$baiduBCS = new BaiduBCS();
	$response = $baiduBCS->create_object( $bucket, $object, $fileUpload );
	if (! $response->isOK ()) {
		die ( "Create object failed." );
	}
	
	$url = $baiduBCS->generate_get_object_url( $bucket, $object);
	
	$sql = "insert into activity_picture values('$activity_id','$url','$picture_name')";  
	$ret = mysql_query($sql,$link);
	
}

if($method == 'savePhotoToPCS'){
	
	$activity_name = $_GET['activity_name'];
	$access_token = $_GET['access_token'];
	$source_file_list = array();
	$source_file_list = $_GET['source_file'];
	$picture_name_list = array();
	$picture_name_list = $_GET['picture_name_list'];
		
	$bucket = 'ntknight';
	$baiduBCS = new BaiduBCS($ak, $sk, $host);
	
	for($i=0; $i<count($picture_name_list); $i++){

		$object = '/' . $activity_name . '/' . $picture_name_list[$i];
		echo $object;
		$fileWriteTo = './a.' . time () . '.png';
		
	/*	$response = $baiduBCS->create_object ( $bucket, $object, $fileUpload );
		if (! $response->isOK ()) {
			die ( "Create object failed." );
		}
		echo "Create object[$object] in bucket[$bucket] success\n";*/
		
		$opt = array (
				"fileWriteTo" => $fileWriteTo, 
				);
		$response = $baiduBCS->get_object ( $bucket, $object, $opt );
		if (! $response->isOK ()) {
			die ( "Download object failed." );
		}
		echo "Download object[$object] in bucket[$bucket] success. And write to [$fileWriteTo]\n";
		
		$auth = array (
			'access_token' => $access_token,
		);
		
		$target_dir = "/apps/ntknight/" . $activity_name . '/' ;
		
		echo $target_file;
		
		$pcs = new BaiduPCS($auth);
		$pcs->set_ssl(true); 
		
		if (!($data = $pcs->upload_file($fileWriteTo,$target_dir,$picture_name_list[$i]))){
		  
			echo json_encode(array("error_message"=>$pcs->get_error_message()));
		} else {
			echo json_encode($data);
		}
	
		unlink($fileWriteTo);
		echo "this is end";
	}
}

//insert members infomation to mysql 
if($method == "addMember"){

	$member_name = $_POST['member_name'];
	$member_company = $_POST['member_company'];
	$member_position = $_POST['member_position'];
	$member_phone = $_POST['member_phone_number'];
	$member_Email = $_POST['member_email'];
	$member_user_name = $_POST['member_user_name'];
	$password= $_POST['password'];
	$sex = $_POST['sex'];
	
	$sql = "insert into member_info(member_name,member_company,member_position,member_phone,member_Email,member_user_name,password,sex,status) values('$member_name','$member_company','$member_position','$member_phone','$member_Email','$member_user_name','$password','$sex','1')";
	 
	$ret = mysql_query($sql,$link);
	
	$sql = "select member_id from member_info ORDER BY member_id DESC LIMIT 0, 1";
	
	$ret = mysql_query($sql,$link);
	
	while($row = mysql_fetch_assoc($ret) ){
		$member_id = $row["member_id"];
	}
	 	
	echo json_encode(array('member_id'=> $member_id));	
}


//get comemnt

if($method == "getComment"){
	
	$activity_id = $_GET['activity_id'];
	
	$sql = "select * from activity_comment where activity_id='$activity_id' order by submit_time asc";
	$ret = mysql_query($sql,$link);
	
	
	$member_user_name_list = array();
	$comment_list =array();
	$time_list = array();
	$sex_list = array();
	$i = 0;
	
	while($row = mysql_fetch_assoc($ret) ){
		$member_user_name_list[$i] = $row['member_user_name'];
		$member_id = $row['member_id'];
		$sql2 = "select sex from member_info where member_id='$member_id'";
		$ret2 = mysql_query($sql2,$link);
		
		while($row2 = mysql_fetch_assoc($ret2) ){
			$sex_list[$i] = $row2['sex'];
		}
		
		$comment_list[$i] = $row['comment'];
		$time_list[$i++] = $row['submit_time'];
	}
	
	echo json_encode(array('member_user_name_list'=> $member_user_name_list,'comment_list'=>$comment_list,'time_list'=>$time_list,'sex_list'=>$sex_list));
	
}


// sign up activity

if($method == "signUpActivity"){
	
	$activity_id = $_POST['activity_id'];
	$member_id = $_POST['member_id'];
	
	$sql = "insert into member_join_activity value('$activity_id','$member_id','1') ";
	
	$ret = mysql_query($sql,$link);
	
	
}

//Judge whether the user registration
if($method == "isSignUp"){
	
	$activity_id = $_POST['activity_id'];
	$member_id = $_POST['member_id'];
	
	$sql = "select status from member_join_activity  where activity_id='$activity_id' and member_id='$member_id' ";
	
	$ret = mysql_query($sql,$link);
	
	
	
	while($row = mysql_fetch_assoc($ret) ){
		 $status = $row['status'];
	}
	
	echo json_encode(array('status'=>$status));
	
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

if($method == "submitComment"){
	$comment = $_POST['comment'];
	$activity_id = $_POST['activity_id'];
	$member_id = $_POST['member_id'];
	$member_user_name = $_POST['member_user_name'];
	
	$sql = "insert into activity_comment(activity_id,member_id,comment,member_user_name) values('$activity_id','$member_id','$comment','$member_user_name') ";
	
	$ret = mysql_query($sql,$link);

}

if($method == "login"){
	
	$member_user_name = $_POST['member_user_name'];
	$member_password = $_POST['member_password'];
	$flag = 1;
	
	$sql = "select member_id,member_user_name,password,sex from member_info ";
	
	$ret = mysql_query($sql,$link);
	
	while($row = mysql_fetch_assoc($ret) ){
		 if($member_user_name == $row['member_user_name']){
		 	if($member_password == $row['password']){
				$flag = 0;
				$member_id = $row['member_id'];
				$sex = $row['sex'];
				$current_time = date('Y-m-d H:i:s', time());
				$sql2 = "update member_info set status=1,login_time='$current_time' where member_id='$member_id'";
				$ret = mysql_query($sql2,$link);
			}else{				
				$flag = 2;
			}
		 }
	}
	
	echo json_encode(array('ret'=> $flag,'member_id'=>$member_id,'sex'=>$sex));
	
}

if($method == "suggestToNT"){
	
	$member_user_name = $_POST['member_user_name'];
	$member_id = $_POST['member_id'];
	$suggest = $_POST['suggest_content'];
	
	$sql = "insert into member_suggest values('$member_id','$member_user_name','$suggest')";
	$ret = mysql_query($sql,$link);
	
}

if($method == "getMemberInfo"){
	
	$member_id = $_GET['member_id'];
	
	$sql = "select * from member_info where member_id='$member_id'";
	$ret = mysql_query($sql,$link);
	
	while($row = mysql_fetch_assoc($ret)){
		$member_user_name = $row['member_user_name'];
		$password = $row['password'];
		$member_name = $row['member_name'];
		$sex = $row['sex'];
		$member_company = $row['member_company'];
		$member_position = $row['member_position'];
		$member_phone = $row['member_phone'];
		$member_email = $row['member_Email']; 
	}
	
	echo json_encode(array('member_user_name'=> $member_user_name,'password'=>$password,'member_name'=>$member_name,'sex'=>$sex,'member_company'=>$member_company,'member_position'=>$member_position,'member_phone'=>$member_phone,'member_email'=>$member_email,));
	
}


if($method == "updateMemberInfo"){
	
	$member_id = $_POST['member_id'];
	$member_company = $_POST['member_company'];
	$member_position = $_POST['member_position'];
	$member_phone = $_POST['member_phone_number'];
	$member_Email = $_POST['member_email'];
	$member_user_name = $_POST['member_user_name'];
	$password= $_POST['password'];
	
	$sql = "update member_info set member_company='$member_company',member_position='$member_position',member_phone='$member_phone',member_Email='$member_Email',member_user_name='$member_user_name',password='$password' where member_id='$member_id'";
	
	$ret = mysql_query($sql,$link);

}

if($method == "logout"){
	$member_id = $_POST['member_id'];
	$current_time = date('Y-m-d H:i:s', time());
	$sql = "update member_info set status=0,login_time='$current_time' where member_id='$member_id'"; 
	
	$ret = mysql_query($sql,$link);
	
}

//get member status : 0 - offline    1 - online
if($method == 'getMemberStatus'){
	
	$sql = "select * from member_info where status='1' order by login_time  asc";
	$ret = mysql_query($sql,$link);
	
	$member_name_list = array();
	$company_list = array();
	$position_list = array();
	$sex_list = array();
	
	$i = 0;
	
	while($row = mysql_fetch_assoc($ret)){
		$member_name_list[$i] = $row['member_name'];
		$company_list[$i] = $row['member_company'];
		$position_list[$i] = $row['member_position'];
		$sex_list[$i++] = $row['sex'];
	}
	
	echo json_encode(array('member_name_list'=>$member_name_list,'company_list'=>$company_list,'position_list'=>$position_list,'sex_list'=>$sex_list));
	
}


if($method == "getMemberJoinActivityStatus"){
	
	$member_id = $_GET['member_id'];
	
	$sql = "select * from member_join_activity where member_id='$member_id' and status='2' ";
	$ret = mysql_query($sql,$link);
	
	$activity_id_list = array();
	$activity_list = array();
	$flag_list = array();
	$mynt_banner_list = array();
	$i = 0;
	
	while($row = mysql_fetch_assoc($ret)){
		$activity_id = $row['activity_id'];
		$activity_id_list[$i] = $activity_id;
		
		$sql2 = "select activity_name,flag,myntbanner  from activity_info where activity_id='$activity_id'";
		
		$ret2 = mysql_query($sql2,$link);
		while($row2 = mysql_fetch_assoc($ret2)){
			$activity_list[$i] = $row2['activity_name'];
			$flag_list[$i] = $row2['flag'];
			$mynt_banner_list[$i++] = $row2['myntbanner'];
		}
			
	}
	
	echo json_encode(array('activity_list'=>$activity_list, 'activity_id_list'=> $activity_id_list,'flag_list'=>$flag_list,'mynt_banner_list'=>$mynt_banner_list));
	
}

mysql_close($link);

?>
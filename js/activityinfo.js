addEventListener('load',function(){
	setTimeout(function(){window.scrollTo(0,1); },100);
});


function loadMainPage(){
	self.location.href="mainpage.html";
}



//mynt button
function myNtIncoPress(){
	document.getElementById('myNtIcon').src = "images/main/file_tital_meicon_press.png";
}

function myNtIconMouseOut(){
	document.getElementById('myNtIcon').src = "images/main/file_tital_meicon.png";
}

function loadMyNtPage()
{

	self.location.href="mainpage.html";
}


//moreinfo button
function moreInfoIncoPress(){
	document.getElementById('moreInfoIcon').src = "images/main/file_tital_moreicon_press.png";
}

function moreInfoIncoMouseOut(){
	document.getElementById('moreInfoIcon').src = "images/main/file_tital_moreicon.png";
}

function loadMoreInfoPage()
{

	self.location.href="mainpage.html";

}


var activity_name;
var activity_id;
var member_id;
var status;

if(window.localStorage)
{
	activity_name = window.localStorage.getItem("current_activity_name");
	activity_id =  window.localStorage.getItem("current_activity_id");
}

document.getElementById('activity_title').innerHTML = activity_name;


var postData = {
	'method': 'getActivityInfo',
	'activity_name':activity_name,
};
$.ajax({
	url : '/server/server.php', 
	data : postData,
	type : 'GET', 
	dataType : 'json',
	cache : false
	}).done(function(data) {
		
		activity_time = data.activity_time;
      	activity_address = data.activity_address;
      	activity_introduce = data.activity_introduce;
      	count_person = data.count_person;
      	flag = data.flag;
		count_picture = data.count_picture;
		count_comment = data.count_comment;
		
		if(window.localStorage){
			
			window.localStorage.setItem("current_activity_address",activity_address);
			window.localStorage.setItem("current_activity_flag", flag);
		}
		
		document.getElementById('time_info').innerHTML = activity_time;
		
		document.getElementById('current_person_info').innerHTML = count_person+'人';
		
		document.getElementById('address_info').innerHTML = activity_address;
		
		document.getElementById('activity_introduce').innerHTML = activity_introduce;
		
		if(flag == 0){
			
//			document.getElementById('join_activity').style.display = "none";
			document.getElementById('picture_num').innerHTML = count_picture;
			
		}else{
			document.getElementById('detail_picture_icon').style.display = "none";
		}
		
			document.getElementById('comment_num').innerHTML = count_comment;
		
	})
	.fail(function(data, txt) {
	  //					alert("Internal Server Error" + data);
	})
	.always(function(data) {
		
	});

function loadShowPictureList(){
	self.location.href="showpicturelist.html";
}

function loadShowCommentList(){
	self.location.href="comment.html";
}

//join activity function
function joinActivity(){
	if(window.localStorage)
	{	
		if(window.localStorage.getItem('NT_user_name') && window.localStorage.getItem('NT_user_id')){
			member_id = localStorage.getItem('NT_user_id');
			isSignUp();
		}else{
			window.localStorage.setItem("current_action","joinactivity");
			self.location.href="login.html";
		}
	}else{
	}
}


function isSignUp(){
	var postData = {
		'method': 'isSignUp',
		'activity_id':activity_id,
		'member_id' : member_id,
	};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'POST', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
	  		status = data.status;
			
			if(status == 'null'){
				signUpActivity();
				
			}else{
				alert("这次活动您已经报过了哦！请等待我们的审核！");
			}
		})
		.fail(function(data, txt) {
		  //					alert("Internal Server Error" + data);
		})
		.always(function(data) {
		});	
	
}


function signUpActivity(){
	
	var postData = {
		'method': 'signUpActivity',
		'activity_id':activity_id,
		'member_id' : member_id,
	};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'POST', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
			alert("报名成功，请等待审核！");
		})
		.fail(function(data, txt) {
		  //					alert("Internal Server Error" + data);
		})
		.always(function(data) {
		});	
	
}
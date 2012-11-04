var url =  window.location.search;
var activity_name;
var activity_id;

//
var action;
var member_id;


if(window.localStorage)
{
	activity_name = window.localStorage.getItem("current_activity_name");
	activity_id = window.localStorage.getItem("current_activity_id");
	action = window.localStorage.getItem("current_action");
}


function login(){
	var member_user_name = document.getElementById('member_user_name').value;
	var member_password = document.getElementById('member_password').value;
	
	var postData = {
			'method': 'login',
			'member_user_name':member_user_name,
			'member_password' : member_password,
		};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'POST', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
			
			validate_flag = data.ret;
			// 1.username is not exist ; 2.password is wrong  0.right
			if( validate_flag == "1"){
				alert("用户不存在！请先注册！");
			}
			
			if( validate_flag == '2'){
				alert('密码错误！');
			}
			
			if( validate_flag == '0'){
				window.localStorage.setItem('NT_user_name',member_user_name);
				window.localStorage.setItem('NT_user_id',data.member_id);
				window.localStorage.setItem('NT_user_sex',data.sex);
				
				if(action == "joinactivity"){
					isSignUp();
					
				}
				if(action == "loadMyNtPage"){
					self.location.href="myntpage.html";
				}
				
				if(action == "comment"){
					self.location.href="comment.html";
				}
				
				if(action == "loadMoreInfoPage"){
					self.location.href="moreinfopage.html";
				}
				
			}
			
			
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {

		});
}


function register(){
	self.location.href="register.html";
}

function cancel(){
	
	if(action == "loadMyNtPage" || action == "loadMoreInfoPage"){
		
		self.location.href="mainpage.html";
	}
	
	if(action == "comment"){
		self.location.href="comment.html";
	}
	
	if(action == "joinactivity"){
		self.location.href="activityinfo.html";
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
			self.location.href = "activityinfo.html";
		})
		.fail(function(data, txt) {
		  //					alert("Internal Server Error" + data);
		})
		.always(function(data) {
		});	
	
}
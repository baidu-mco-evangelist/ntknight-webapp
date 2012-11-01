// JavaScript Document

var url =  window.location.search;
var activity_name;
var activity_id;
var check_flag = 0;
//
var flag;

var member_id;


if(url.indexOf("?")!=-1)
{
	var str = url.substr(1);
	strs= str.split("&");
	activity_name = unescape(strs[0].split("=")[1]);
	activity_id = strs[1].split("=")[1];
	flag = strs[2].split("=")[1];
}

function isEmail(str){ 
	var myReg  = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/; 
	if(myReg.test(str)) return true; 
	return false; 
} 

function isNull(str){ 
	if ( str == "" ) return true; 
	var regu = "^[ ]+$"; 
	var re = new RegExp(regu); 
	return re.test(str); 
} 

function checkMobile(str){ 
	var regu = /^1[3,5]\d{9}$/; 
	var re = new RegExp(regu); 
	if (re.test(str)) { 
	return true; 
	}else{ 
		return false; 
	} 
} 

function submitMemberInfo(){
	
	var member_user_name = document.getElementById('member_user_name').value;
	
	if(isNull(member_user_name)){
		check_flag = 1;
	}
	var password = document.getElementById('member_password').value;
	
	if(isNull(password)){
		check_flag = 1;
	}
	
	var member_name = document.getElementById('user_name').value;
	
	if(isNull(member_name)){
		check_flag = 1;
	}
	
	var sex;
	if(document.getElementById('RadioGroup1_0').checked){
		sex = document.getElementById('RadioGroup1_0').value;
	}else{
		sex = document.getElementById('RadioGroup1_1').value;
	}
	
	var member_company = document.getElementById('user_company').value;
	
	if(isNull(member_company)){
		check_flag = 1;
	}
	var member_position = document.getElementById('user_position').value;
	
	if(isNull(member_position)){
		check_flag = 1;
	}
	var member_phone_number = document.getElementById('user_phone_nubmer').value;
	if(isNull(member_phone_number)){
		check_flag = 1;
	}else{
/*		if(!checkMobile(member_phone_number)){
			check_flag = 3; //the phone number is wrong
		}*/
	}
	
	var member_email = document.getElementById('user_email').value;
	
	if(isNull(member_email)){
		check_flag = 1; // the email is null
	}else{
		if(!isEmail(member_email)){
			chenk_flag = 2;//the email is wrong
		}
	}
	
	if(check_flag == 0){	
		var postData = {
			'method': 'addMember',
			'member_user_name':member_user_name,
			'password':password,
			'member_name': member_name,
			'sex' : sex,
			'member_company': member_company,
			'member_position': member_position,
			'member_phone_number': member_phone_number,
			'member_email': member_email,
		};
		$.ajax({
			url : '/server/server.php', 
			data : postData,
			type : 'POST', 
			dataType : 'json',
			cache : false
			}).done(function(data) {
				
				alert("注册成功！");
				
				member_id = data.member_id;
	
				window.localStorage.setItem('NT_user_name',member_user_name);
				window.localStorage.setItem('NT_user_id',member_id);
				window.localStorage.setItem('NT_user_sex',sex);
				
				if(action == "joinactivity"){
					signUpActivity();
					self.location.href="feedback.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
					
				}
				if(action == "loadMyNtPage"){
					self.location.href="myntpage.html";
				}
				
				if(action == "comment"){
					self.location.href="comment.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
				}
				
				if(action == "loadMoreinfoPage"){
					self.location.href="moreinfopage.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
				}
		  
			})
			.fail(function(data, txt) {
			  //					alert("Internal Server Error" + data);
			})
			.always(function(data) {
			});
	
	}else{
		if(check_flag == 1)
		{
			alert("值不能为空");
		}else{
			
			if(check_flag == 2)
			{
				alert("邮箱格式不对！");
			}else{
				alert("电话格式不对");
			}
		}
	}
	

}

function signUpActivity(){
	
	var postData = {
		'method': 'signUpActivity',
		'activity_id': activity_id,
		'member_id' : member_id,
	};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'POST', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
	  		self.location.href="feedback.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
		})
		.fail(function(data, txt) {
		  //					alert("Internal Server Error" + data);
		})
		.always(function(data) {
		});	
	
}



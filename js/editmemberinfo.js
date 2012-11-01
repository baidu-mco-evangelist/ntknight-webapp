
document.getElementById('user_name').readOnly = "ture";
document.getElementById('RadioGroup1_0').readOnly = "ture";
document.getElementById('RadioGroup1_1').readOnly = "ture";


var member_id;
var check_flag = 0;

if(window.localStorage){
	if(window.localStorage.getItem("NT_user_name") && window.localStorage.getItem("NT_user_id")){
		
		member_id = window.localStorage.getItem("NT_user_id");
		connectServer();
	}
}


function connectServer(){
	
	var getData={
		'method' : 'getMemberInfo',
		'member_id' : member_id,
	}
	$.ajax({
		url : '/server/server.php', 
		data : getData,
		type : 'GET', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
			
			 document.getElementById('member_user_name').value = data.member_user_name;
 			 document.getElementById('member_password').value = data.password;
			 document.getElementById('user_name').value = data.member_name;
			 
			 if(data.sex == "male"){
				 document.getElementById('RadioGroup1_0').checked = "ture";
			 }else{
				document.getElementById('RadioGroup1_1').checked = "ture"; 
			 }
			 			 
			 document.getElementById('user_company').value = data.member_company;
			 document.getElementById('user_position').value = data.member_position;
			 document.getElementById('user_phone_nubmer').value = data.member_phone;
			 document.getElementById('user_email').value = data.member_email;
			 
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {

		});

}


function isNull(str){ 
	if ( str == "" ) return true; 
	var regu = "^[ ]+$"; 
	var re = new RegExp(regu); 
	return re.test(str); 
} 


function submitMember(){
	document.getElementById('member_user_name').readOnly = "false";
	document.getElementById('member_password').readOnly = "false";
	document.getElementById('user_company').readOnly = "false";
	document.getElementById('user_position').readOnly = "false";
	document.getElementById('user_phone_nubmer').readOnly = "false";
	document.getElementById('user_email').readOnly = "false";
	
	var member_user_name = document.getElementById('member_user_name').value;
	
	if(isNull(member_user_name)){
		check_flag = 1;
	}
	var password = document.getElementById('member_password').value;
	
	if(isNull(password)){
		check_flag = 1;
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
/*		if(!isEmail(member_email)){
			chenk_flag = 2;//the email is wrong
		}*/
	}
	
	if(check_flag == 0){	
		var postData = {
			'method': 'updateMemberInfo',
			'member_id' : member_id,
			'member_user_name':member_user_name,
			'password':password,
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
				alert("信息修改成功！");
				self.location.href="showmemberinfopage.html";
			})
			.fail(function(data, txt) {
			  //					alert("Internal Server Error" + data);
			})
			.always(function(data) {
			});
	}
	
}

function cancel(){
	
	self.location.href="showmemberinfopage.html";
}
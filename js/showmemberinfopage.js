
document.getElementById('member_user_name').readOnly = "ture";
document.getElementById('member_password').readOnly = "ture";
document.getElementById('user_name').readOnly = "ture";
document.getElementById('RadioGroup1_0').readOnly = "ture";
document.getElementById('RadioGroup1_1').readOnly = "ture";
document.getElementById('user_company').readOnly = "ture";
document.getElementById('user_position').readOnly = "ture";
document.getElementById('user_phone_nubmer').readOnly = "ture";
document.getElementById('user_email').readOnly = "ture";


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



function eidtMember(){
	
	self.location.href="editmemberinfo.html";
	
}

function cancel(){
	
	self.location.href="moreinfopage.html";
}
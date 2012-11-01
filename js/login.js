var url =  window.location.search;
var activity_name;
var activity_id;

//
var action;

var member_id;


if(url.indexOf("?")!=-1)
{
	var str = url.substr(1);
	strs= str.split("&");
	activity_name = unescape(strs[0].split("=")[1]);
	activity_id = strs[1].split("=")[1];
	action = unescape(strs[2].split("=")[1]);
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
					self.location.href="feedback.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
					
				}
				if(action == "loadMyNtPage"){
					self.location.href="myntpage.html";
				}
				
				if(action == "comment"){
					self.location.href="comment.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
				}
				
				if(action == "loadMoreInfoPage"){
					self.location.href="moreinfopage.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
				}
				
			}
			
			
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {

		});
}


function register(){
	self.location.href="joinactivity.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id+"&type="+escape(action);
}

function cancel(){
	
	if(action == "loadMyNtPage" || action == "loadMoreInfoPage"){
		
		self.location.href="mainpage.html";
	}
	
	if(action == "comment"){
		self.location.href="comment.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
	}
	
	if(action == "joinactivity"){
		self.location.href="activityinfo.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
	}
	
}
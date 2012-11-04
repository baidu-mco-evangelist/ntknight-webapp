

/*var scrrenWidth = document.body.clientWidth;

document.getElementById('big_background').width = (parseInt(scrrenWidth)-20);

alert(document.getElementById('big_background').width);*/

var activity_name;
var activity_id;
var member_id;
var comment;
var member_user_name;

if(window.localStorage)
{
	activity_name = window.localStorage.getItem("current_activity_name");
	activity_id = window.localStorage.getItem("current_activity_id");
}


function cancel(){
	self.location.href="comment.html";
}

		
function isNull(str){ 
	if ( str == "" ) return true; 
	var regu = "^[ ]+$"; 
	var re = new RegExp(regu); 
	return re.test(str); 
} 
		
function submitComment(){
	
	if(window.localStorage){
		if(window.localStorage.getItem('NT_user_name') && window.localStorage.getItem('NT_user_id')){
			member_id = window.localStorage.getItem('NT_user_id');
			member_user_name = window.localStorage.getItem('NT_user_name');
			
			comment = document.getElementById('comment_text').value;
			
			if(isNull(comment)){
				alert("内容不能为空！");
			}else{
				connectServer();
			}
			
		}else{
			window.localStorage.setItem("current_action","comment");
			self.location.href="login.html";
		}
	}
	
}

function connectServer(){
	var postData = {
		'method': 'submitComment',
		'activity_id' : activity_id,
		'member_id' : member_id,
		'comment' :comment,
		'member_user_name' :member_user_name,
	};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'POST', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
			self.location.href = "comment.html";
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {
			
		});
	
}
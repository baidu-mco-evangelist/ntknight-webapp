
var url =  window.location.search;
var activity_name;
var activity_id;
var member_id;
var comment;
var member_user_name;

if(url.indexOf("?")!=-1)
{
	var str = url.substr(1);
	strs = str.split("&");
	activity_name = unescape(strs[0].split("=")[1]);
	activity_id = strs[1].split("=")[1];
}


function backToActivityInfo(){
	self.location.href="activityinfo.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id;
}


var postData = {
		'method': 'getComment',
		'activity_id' : activity_id,
	};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'GET', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
			user_name_list = data.member_user_name_list;
			comment_list = data.comment_list;
			time_list = data.time_list;
			
			for(i=0;i<user_name_list.length;i++){
				var newTr = document.getElementById('commentList').insertRow();
				var newTd = newTr.insertCell();
				newTd.innerHTML = '<div><img src="images/comment/comment_article02_610-320.png" width="365" id="big_background" /><img src="images/comment/comment_avatarkuang98-98.png" width="107" height="75" id="icon_border" /><img src="images/comment/comment_avatar92-92.png" width="77" height="75" id="person_icon" /><div id="comment_info"><p id="username" name="username">'+user_name_list[i]+'</p><p id="comment">'+comment_list[i]+'</p></div></div>';
			}
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {
			
		});
		
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
			self.location.href="login.html?activity_name="+escape(activity_name)+"&activity_id="+activity_id+"&type="+escape("comment");
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
			window.location.href=window.location.href;
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {
			
		});
	
}
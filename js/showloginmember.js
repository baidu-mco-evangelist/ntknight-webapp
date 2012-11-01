
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

if(window.localStorage){
	window.localStorage.setItem("login_user_name_list","");
}

function init(){
	
	var postData = {
		'method': 'getMemberStatus',
	};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'GET', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
			old_user_name_list = window.localStorage.getItem("login_user_name_list");
						
			user_name_list = data.member_name_list;
			company_list = data.company_list;
			position_list = data.position_list;
			sex_list = data.sex_list;
			
			k = 0;
			new_user_name_list = Array();
			new_user_company = Array();
			
			for(i=0;i<user_name_list.length;i++){
				
				flag = 0;
				
				for( j=0; j<old_user_name_list.length; j++){
					
					if(user_name_list[i] == old_user_name_list[j]){
						flag = 1;
					}
					
				}
				
				if(flag == 0){					
					new_user_name_list[k] = user_name_list[i];
					new_company_list[k++] = company_list[i];
				}

			}
			
			for(i=0; i<new_user_name_list.length; i++){
				var newTr = document.getElementById('commentList').insertRow();
				var newTd = newTr.insertCell();
				if(sex_list[i] == "male"){
					newTd.innerHTML = '<div><img src="images/comment/comment_article02_610-320.png" width="365" id="big_background" /><img src="images/comment/comment_avatarkuang98-98.png" width="107" height="75" id="icon_border" /><img src="images/more/fillin_avatar126-126.png" width="77" height="75" id="person_icon" /><div id="comment_info"><p id="username" name="username">'+new_user_name_list[i]+'</p><p id="comment">'+new_company_list[i]+'</p></div></div>';
					
				}else{					
					newTd.innerHTML = '<div><img src="images/comment/comment_article02_610-320.png" width="365" id="big_background" /><img src="images/comment/comment_avatarkuang98-98.png" width="107" height="75" id="icon_border" /><img src="images/more/fillin_avatar_woman126-126.png" width="77" height="75" id="person_icon" /><div id="comment_info"><p id="username" name="username">'+user_name_list[i]+'</p><p id="comment">'+company_list[i]+'</p></div></div>';
				}
				
			}
			
			init();
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {
			
		});
}



//setTimeout('init()',1000); //1s refresh 
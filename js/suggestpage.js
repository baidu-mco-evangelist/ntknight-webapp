
function cancel(){
	self.location.href="moreinfopage.html";
}


function send(){
	
	var content = document.getElementById('suggest_content').value;
	if(window.localStorage){
		member_user_name = window.localStorage.getItem("NT_user_name");
		member_id = window.localStorage.getItem("NT_user_id");
	}
	
	var postData = {
			'method': 'suggestToNT',
			'member_user_name':member_user_name,
			'member_id' : member_id,
			'suggest_content' : content,
		};
	$.ajax({
		url : '/server/server.php', 
		data : postData,
		type : 'POST', 
		dataType : 'json',
		cache : false
		}).done(function(data) {
			alert("发送成功");
			document.getElementById('suggest_content').value ="";
		})
		.fail(function(data, txt) {
		})
		.always(function(data) {

		});
	
}
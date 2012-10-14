var postData = {
	'method': 'getActivityList',
};
$.ajax({
	url : 'server/server.php', 
	data : postData,
	type : 'POST', 
	dataType : 'json',
	cache : false
	}).done(function(data) {
  
	})
	.fail(function(data, txt) {
	  //					alert("Internal Server Error" + data);
	})
	.always(function(data) {
		activityList = data.activityList;
		flagList = data.flagList;
		activityAddressList = data.activityAddressList;
		activityTimeList = data.activityTimeList;
		
		for( i=0; i< activityList.length ; i++){
			var newTr = document.getElementById('activityList').insertRow();
			var newTd = newTr.insertCell();
			if(flagList[i] == '1')
			{
				newTd.innerHTML = '<div class="content"><ul><li><div>'+activityList[i]+'<span align="right"><input type="button" value="报名" id="joinactivity" /></span></div></li><li><img src="images/log200x118.png" onClick="loadActivityInfo(\''+activityList[i]+'\')" /></li><li><div>'+activityTimeList[i]+'</div></li> </ul><div>';
			}else{
				newTd.innerHTML = '<div class="content"><ul><li><div>'+activityList[i]+'<input disabled="true" type="button" value="报名" id="joinactivity" align="right" /></div></li><li><img src="images/log200x118.png" onClick="loadActivityInfo(\''+activityList[i]+'\')" /></li><li><div>'+activityTimeList[i]+'</div></li> </ul><div>';
			}
        }
	});
	
	
function loadActivityInfo(activityName){
	self.location.href="activityinfo.html?activity_name="+escape(activityName);
}
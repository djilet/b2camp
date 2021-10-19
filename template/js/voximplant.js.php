<?php 
header('Content-Type: application/javascript');
define('IS_ADMIN', true);
require_once(dirname(__FILE__)."/../../include/init.php");
es_include("voximplant.php");
es_include("user.php");
$user = new User();
$user->LoadBySession();
$voximplant = new VoxImplant();
?>

(function(){
	
	var viApplication = "<?php echo $voximplant->application;?>";
	var viLogin = "<?php echo $user->GetProperty("VoxImplantLogin");?>";
	var viPassword = "<?php echo $user->GetProperty("VoxImplantPassword");?>";
	var PROJECT_PATH = "<?php echo PROJECT_PATH;?>";
	var ADMIN_PATH = "<?php echo ADMIN_PATH;?>";
	var URL_PREFIX = "<?php echo GetUrlPrefix();?>";
	var AJAX_PATH = ADMIN_PATH+"module/crm/ajax.php";
	
	var vox = null;
	var viInitialized = false;
	var viConnected = false;
	var viLoggedIn = false;
	var viPopup = null;
	var currentCall = null;
	var currentCallType = null;
	var currentCallKey = null;
	var currentLinkedEntity = null;
	var currentLinkedEntityID = null;
	var currentIncomingCallerList = null;
	var currentErrorCode = null;
	var currentIncomingCallInterval = null;
	var callConnected = false;
	
	function handleSDKReady()
	{
		viInitialized = true;
		vox.connect();
	}
	
	function handleConnectionEstablished() 
	{
		//CreateMessage(GetTranslation("voximplant-connected"), "success");
		viConnected = true;
		vox.login(viLogin+"@"+viApplication, viPassword);
	}
	 
	function handleConnectionFailed(e) 
	{
		//CreateMessage(GetTranslation("voximplant-connection-failed")+e.message, "error");
		$('#voximplant-connection-status').removeClass().addClass('text-danger');
		viConnected = false;
	}
	
	function handleConnectionClosed() 
	{
		//CreateMessage(GetTranslation("voximplant-connection-closed"), "error");
		$('#voximplant-connection-status').removeClass().addClass('text-danger');
		viConnected = false;
		viLoggedIn = false;
	}
	 
	function handleAuthResult(e) 
	{
		if (e.result)
		{
			$('a.voximplant-create-call').removeClass('hidden');
			$('#voximplant-connection-status').removeClass().addClass('text-success');
			//CreateMessage(GetTranslation("voximplant-authorization-success")+e.displayName, "success");
			viLoggedIn = true;
			vox.setOperatorACDStatus(VoxImplant.OperatorACDStatuses.Online);
			vox.setOperatorACDStatus(VoxImplant.OperatorACDStatuses.Ready);
		} 
		else 
		{
			// authorization failed
			$('#voximplant-connection-status').removeClass().addClass('text-danger');
			CreateMessage(GetTranslation("voximplant-authorization-error")+viLogin, "error");
		}
	}
	
	function handleCallConnected() 
	{
		callConnected = true;
		viPopup.find('.voximplant-redirect').removeClass('hidden');
		LogCallStatus(GetTranslation("voximplant-call-during"));
		StopIncomingCallRinging();
	}
	
	function handleCallFailed(e)
	{
		callConnected = false;
		LogCallStatus(GetTranslation("voximplant-call-failed")+ " " + GetTranslation("voximplant-call-failed-"+e.code));
		currentErrorCode = e.code;
		SaveCallToDB("error");
		
		viPopup.find('.modal-footer button').addClass('hidden');
		viPopup.find('.voximplant-close').removeClass('hidden');
		StopIncomingCallRinging();
	}
	
	function handleCallDisconnected(e)
	{
		currentErrorCode = null;
		if(callConnected == true)
		{
			//ended normally
			SaveCallToDB("success");
		}
		else
		{
			//cancelled by user before connected
			SaveCallToDB("canceled");
		}
		callConnected = false;
		LogCallStatus(GetTranslation("voximplant-call-disconnected"));
		setTimeout(function(){
			vox.setOperatorACDStatus(VoxImplant.OperatorACDStatuses.Online);
			vox.setOperatorACDStatus(VoxImplant.OperatorACDStatuses.Ready);
		}, 3000);
		
		viPopup.find('.modal-footer button').addClass('hidden');
		viPopup.find('.voximplant-close').removeClass('hidden');
		StopIncomingCallRinging();
	}
	
	function handleIncomingCall(e)
	{
		//check current tab
		if(currentCall !== null && currentCall.active() === true){
			e.call.reject();
			return;
		}
		//check other tabs
		var requestID = new Date().getTime() + "_" + (Math.floor(Math.random() * (1000000 - 1)) + 1);
		localStorage.setItem('other_tab_command_check_current_call_request', requestID);
		setTimeout(function(){
			var response = localStorage.getItem('other_tab_command_check_current_call_response_'+requestID);
			if(typeof response != 'undefined' && response == "1"){
				e.call.reject();
				return;
			}
			
			currentCall = e.call;
			currentCallType = "in";
					
			currentCall.addEventListener(VoxImplant.CallEvents.Connected, handleCallConnected);
			currentCall.addEventListener(VoxImplant.CallEvents.Failed, handleCallFailed);
			currentCall.addEventListener(VoxImplant.CallEvents.Disconnected, handleCallDisconnected);
	
			var headers = currentCall.headers();
			currentCallKey = headers['X-CallKey'];
			if(typeof headers['X-DisplayName'] != 'undefined')
				var displayName = headers['X-DisplayName'];
			else	
				var displayName = currentCall.displayName();
			var links = "";
			var callerList = [];
			if(typeof headers['X-CallerListJSON'] != 'undefined')
			{
				callerList = JSON.parse(headers['X-CallerListJSON']);
				if(callerList.length == 0)
				{
					var childTitle = GetTranslation("entity-child");
					var legalTitle = GetTranslation("entity-legal");
					callerList = [{"Entity":"child", "EntityID":"", Title:childTitle}, {"Entity":"legal", "EntityID":"", Title:legalTitle}];
					currentIncomingCallerList = null;
				}
				else
				{
					currentIncomingCallerList = callerList;
				}
			}
			for(var i = 0; i < callerList.length; i++)
			{
				if(callerList[i]["EntityID"] == "")
				{
					links += '<a href="'+ADMIN_PATH+'module.php?load=crm&entity='+callerList[i]["Entity"]+'&EntityID=" target="_blank" class="btn btn-success btn-icon left15"><i class="fa fa-plus"></i> '+callerList[i]["Title"]+'</a>';
				}
				else
				{
					links += '<a href="'+ADMIN_PATH+'module.php?load=crm&entity='+callerList[i]["Entity"]+'&EntityViewID='+callerList[i]["EntityID"]+'" target="_blank" class="left15 dashed">'+callerList[i]["Title"]+'</a>';
				}
			}
			SetCallTitle(displayName + links);
			ClearCallStatus();
			LogCallStatus(GetTranslation("voximplant-call-incoming"));
			viPopup.find('.modal-footer button').addClass('hidden');
			viPopup.find('.voximplant-answer').removeClass('hidden');
			viPopup.find('.voximplant-reject').removeClass('hidden');
			viPopup.modal('show');
			
			if (!("Notification" in window)) 
			{
				alert("This browser does not support desktop notification");
				return;
			}
			if (Notification.permission === "granted") 
			{
				ShowIncomingCallNotification(displayName);
			}
			else if (Notification.permission !== 'denied')
			{
				Notification.requestPermission(function (permission) {
					if (permission === "granted") 
					{
						ShowIncomingCallNotification(displayName);		
					}
				});
			}
		}, 750);
	}
	
	function ShowIncomingCallNotification(title)
	{
		var rand = Math.floor(Math.random() * (1000000 - 1)) + 1;
		localStorage.setItem('sound_random', rand);
		setTimeout(function(){
			if(localStorage.getItem('sound_random') == rand)
			{
				StartIncomingCallRinging();
			}
		}, 500);
		for(var i = 0; i < 3; i++)
		{
			var text = GetTranslation("voximplant-call-incoming-notification");
			var options = {
				body: text, 
				icon: URL_PREFIX+'template/images/incoming_call_icon.png',  
				tag: title+text+i,
				requireInteraction: true
			};
			var notification = new Notification(title, options);
			notification.onclick = function(event) {
				this.close();
				event.preventDefault();
				window.focus();
			}
		}
	}
	
	function StartIncomingCallRinging()
	{
		PlaySound(URL_PREFIX+"template/sound/incoming_call_notification.mp3");
		currentIncomingCallInterval = setInterval(function(){
			PlaySound(URL_PREFIX+"template/sound/incoming_call_notification.mp3");
		}, 4000);
	}
	
	function StopIncomingCallRinging()
	{
		clearInterval(currentIncomingCallInterval);
	}
	
	function PlaySound(url)
	{
		var audio = new Audio(url);
		audio.play();
	}
	
	function CreateCall(phone, title, LinkedEntity, LinkedEntityID)
	{
		if (!viInitialized)
		{
			vox.init();
		}
		else
		{
			if (!viConnected) 
			{
				vox.connect();
			}
			else
			{
				if (!viLoggedIn) 
				{
					vox.login(viLogin+"@"+viApplication, viPassword);
				}
				else
				{
					currentCallKey = viLogin + new Date().getTime();
					currentCall = vox.call(phone, false, currentCallKey);
					
					currentCallType = "out";
					currentCall.addEventListener(VoxImplant.CallEvents.Connected, handleCallConnected);
					currentCall.addEventListener(VoxImplant.CallEvents.Failed, handleCallFailed);
					currentCall.addEventListener(VoxImplant.CallEvents.Disconnected, handleCallDisconnected);
					
					currentLinkedEntity = LinkedEntity;
					currentLinkedEntityID = LinkedEntityID;
					
					SetCallTitle(title);
					ClearCallStatus();
					LogCallStatus(GetTranslation("voximplant-call-starting"));
					viPopup.find('.modal-footer button').addClass('hidden');
					viPopup.find('.voximplant-hangup').removeClass('hidden');
					viPopup.find('.voximplant-mute-mic').removeClass('hidden');
					viPopup.find('.voximplant-unmute-mic').addClass('hidden');
					viPopup.modal('show');
				}
			}
		}
	}
	
	function SaveCallToDB(status)
	{
		if(currentCallType == "out")
		{
			$.ajax({
				url: AJAX_PATH,
				type: "POST",
				dataType: "JSON",
				data: {
					"Action": "SaveCall", 
					"LinkedEntity": currentLinkedEntity,
					"LinkedEntityID": currentLinkedEntityID,
					"CallType": currentCallType,
					"CallKey": currentCallKey,
					"Status": status,
					"ErrorCode": currentErrorCode
				}, 
				success: function(data){
					for(var i = 0; i < data.ErrorList.length; i++)
					{
						CreateMessage(data.ErrorList[i]["Message"], "error");
					}
					for(var i = 0; i < data.MessageList.length; i++)
					{
						CreateMessage(data.MessageList[i]["Message"], "success");
					}
				}
			});
		}
		else
		{
			if(currentIncomingCallerList != null && currentIncomingCallerList.length > 0)
			{
				for(var i = 0; i < currentIncomingCallerList.length; i++)
				{
					$.ajax({
						url: AJAX_PATH,
						type: "POST",
						dataType: "JSON",
						data: {
							"Action": "SaveCall", 
							"LinkedEntity": currentIncomingCallerList[i]["Entity"],
							"LinkedEntityID": currentIncomingCallerList[i]["EntityID"],
							"CallKey": currentCallKey,
							"Status": status,
							"ErrorCode": currentErrorCode
						}, 
						success: function(data){
							for(var i = 0; i < data.ErrorList.length; i++)
							{
								CreateMessage(data.ErrorList[i]["Message"], "error");
							}
							for(var i = 0; i < data.MessageList.length; i++)
							{
								CreateMessage(data.MessageList[i]["Message"], "success");
							}
						}
					});
				}
			}
		}
	}
	
	function LogCallStatus(status)
	{
		var html = '<div class="status-row">['+(new Date()).toLocaleTimeString()+'] '+status+'</div>';
		viPopup.find('.status').append($(html));
	}
	
	function ClearCallStatus()
	{
		viPopup.find('.status').empty();
	}
	
	function SetCallTitle(title)
	{
		viPopup.find('.modal-title').html(title);
	}
	
	function GetCallTitle()
	{
		return viPopup.find('.modal-title').html();
	}
	
	if(typeof VoxImplant != 'undefined' && viLogin.length > 0 && viPassword.length > 0)
	{
		vox = VoxImplant.getInstance();
		vox.init({showDebugInfo:false, micRequired: true, progressTone: true, progressToneCountry: "RU"});
		vox.addEventListener(VoxImplant.Events.SDKReady, handleSDKReady);
		vox.addEventListener(VoxImplant.Events.ConnectionEstablished, handleConnectionEstablished);
		vox.addEventListener(VoxImplant.Events.ConnectionFailed, handleConnectionFailed);
		vox.addEventListener(VoxImplant.Events.ConnectionClosed, handleConnectionClosed);
		vox.addEventListener(VoxImplant.Events.AuthResult, handleAuthResult);
		vox.addEventListener(VoxImplant.Events.IncomingCall, handleIncomingCall);
		
		$(document).ready(function(){
			viPopup = $('#voximplant-popup');
			viRedirectPopup = $('#voximplant-redirect-popup');
			$(document).on('click', 'a.voximplant-create-call', function(e){
				var phone = $(this).attr('viPhone');
				var title = $(this).attr('viTitle');
				var LinkedEntity = $(this).attr('viLinkedEntity');
				var LinkedEntityID = parseInt($(this).attr('viLinkedEntityID'));
				CreateCall(phone, title, LinkedEntity, LinkedEntityID);
				e.preventDefault();
			});
			viPopup.find('.voximplant-answer').click(function(e){
				currentCall.answer();
				viPopup.find('.modal-footer button').addClass('hidden');
				viPopup.find('.voximplant-hangup').removeClass('hidden');
				viPopup.find('.voximplant-mute-mic').removeClass('hidden');
				localStorage.setItem('other_tab_command_answer', new Date().getTime());
				e.preventDefault();
			});
			viPopup.find('.voximplant-reject').click(function(e){
				currentCall.reject();
				$(this).addClass('hidden');
				viPopup.find('.voximplant-answer').addClass('hidden');
				localStorage.setItem('other_tab_command_reject', new Date().getTime());
				e.preventDefault();
			});
			viPopup.find('.voximplant-mute-mic').click(function(e){
				currentCall.muteMicrophone();
				$(this).addClass('hidden');
				viPopup.find('.voximplant-unmute-mic').removeClass('hidden');
				e.preventDefault();
			});
			viPopup.find('.voximplant-unmute-mic').click(function(e){
				currentCall.unmuteMicrophone();
				$(this).addClass('hidden');
				viPopup.find('.voximplant-mute-mic').removeClass('hidden');
				e.preventDefault();
			});
			viPopup.find('.voximplant-hangup').click(function(e){
				currentCall.hangup();
				e.preventDefault();
			});
			viRedirectPopup.find('.voximplant-redirect-confirm').click(function(e){
				var redirectTo = $('#voximplant-redirect-popup .voximplant-redirect-user').val();
				var callTitle = GetCallTitle();
				var redirectCallKey = viLogin + new Date().getTime();
				var redirectCall = vox.call(redirectTo, false, redirectCallKey, (currentCallType == "in" ? currentCall.headers() : {"X-DisplayName": callTitle}));
				LogCallStatus(GetTranslation('voximplant-call-redirecting')+' '+redirectTo);
				redirectCall.addEventListener(VoxImplant.CallEvents.TransferComplete, function(e){
					LogCallStatus(GetTranslation('voximplant-call-redirected'));
				});
				redirectCall.addEventListener(VoxImplant.CallEvents.TransferFailed, function(e){
					LogCallStatus(GetTranslation('voximplant-call-redirect-failed'));
				});
				redirectCall.addEventListener(VoxImplant.CallEvents.Connected, function(e){
					vox.transferCall(redirectCall, currentCall);
				});
				redirectCall.addEventListener(VoxImplant.CallEvents.Failed, function(e){
					LogCallStatus(GetTranslation('voximplant-call-redirect-failed'));
				});
				viRedirectPopup.modal('hide');
				e.preventDefault();
			});
			viPopup.find('.voximplant-close').click(function(e){
				localStorage.setItem('other_tab_command_close', new Date().getTime());
				viPopup.modal('hide');
				e.preventDefault();
			});
			window.addEventListener('storage', function(e) {
				switch(e.key)
				{
					case "other_tab_command_reject":
						currentCall.reject();
						viPopup.modal('hide');
						break;
					case "other_tab_command_answer":
						currentCall = null;
						currentCallType = null;
						currentCallKey = null;
						currentLinkedEntity = null;
						currentLinkedEntityID = null;
						currentIncomingCallerList = null;
						currentErrorCode = null;
						callConnected = false;
						viPopup.modal('hide');
						break;
					case "other_tab_command_close":
						viPopup.modal('hide');
						break;
					case "other_tab_command_check_current_call_request":
						if(currentCall != null && currentCall.active() === true){
							var requestID = e.newValue;
							localStorage.setItem('other_tab_command_check_current_call_response_'+requestID, "1");
						}
						break;
				}
			});
		});
	}

})();
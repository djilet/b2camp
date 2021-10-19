function DataMask(elt){
    elt.find("[data-mask]").each(function(i, el) {
        var $this = $(el);
        var mask = $this.data('mask').toString();
        var opts = {
            numericInput: getValue($this, 'numeric', false),
            radixPoint: getValue($this, 'radixPoint', ''),
            rightAlign: getValue($this, 'numericAlign', 'left') == 'right'
        };
        var placeholder = getValue($this, 'placeholder', '');
        var is_regex = getValue($this, 'isRegex', '');
        if (mask.toLowerCase() == "phone") {
            mask = "+7-(999)-999-99-99";
            if (is_regex) {
                opts.regex = mask;
                mask = 'Regex';
            }
            $this.inputmask(mask, opts);
        }
    });
}

//remove control
function MultipleRemove(form, elmName)
{
	var checked = 0;
	for (var i = 0; i < form.elements.length; i++)
	{
		if (form.elements[i].name == elmName && form.elements[i].type == "checkbox" && form.elements[i].checked)
		{
			checked++;
		}
	}

	if (checked == 0)
	{
		alert('Не выбрано ни одной строки для удаления');
	}
	else
	{
		ModalConfirm('Вы действительно хотите удалить выделенные строки?', function(){
			form.elements['Do'].value = 'Remove';
			form.submit();	
		});	
	}
	return false;
}

function MultipleCancel(form, elmName)
{
	var checked = 0;
	for (var i = 0; i < form.elements.length; i++)
	{
		if (form.elements[i].name == elmName && form.elements[i].type == "checkbox" && form.elements[i].checked)
		{
			checked++;
		}
	}

	if (checked == 0)
	{
		alert('Не выбрано ни одной строки');
	}
	else
	{
		ModalConfirm('Вы действительно хотите установить статус "Отменен" выделенным строкам?', function(){
			form.elements['Do'].value = 'Cancel';
			form.submit();	
		});	
	}
	return false;
}

//clear panel
function MultipleClearPanel(form, elemName)
{
	var checked = 0;
	for (var i = 0; i < form.elements.length; i++)
	{
		if (form.elements[i].name == elemName && form.elements[i].type == "checkbox" && form.elements[i].checked)
		{
			checked++;
		}
	}

	if (checked == 0)
	{
		alert('Не выбрано ни одной строки для изменения статусов!');
	}
	else
	{
		ModalConfirm('Вы действительно хотите выставить статус "Не звонили" на выделенные строки?', function(){
			form.elements['Do'].value = 'Action';
			$(form).append('<input type="hidden" name="Action" value="ClearPanel" />');
			form.submit();
		});	
	}
	return false;
}

//reassign control
function MultipleReassign(form, elmName)
{
	var checked = 0;
	for (var i = 0; i < form.elements.length; i++)
	{
		if (form.elements[i].name == elmName && form.elements[i].type == "checkbox" && form.elements[i].checked)
		{
			checked++;
		}
	}

	if (checked == 0)
	{
		alert('Не выбрано ни одной строки для переназначения');
	}
	else
	{
		ModalConfirm('Вы действительно хотите переназначить выделенные строки?', function(){
			form.elements['Do'].value = 'Action';
			$(form).append('<input type="hidden" name="Action" value="Reassign" />');
			$(form).append('<input type="hidden" name="ManagerID" value="'+$('#ReassignManagerID').val()+'" />');
			form.submit();	
		});			
	}
	return false;
}

//send to archive control
function MultipleSendToArchive(form, elmName)
{
	var checked = 0;
	for (var i = 0; i < form.elements.length; i++)
	{
		if (form.elements[i].name == elmName && form.elements[i].type == "checkbox" && form.elements[i].checked)
		{
			checked++;
		}
	}

	if (checked == 0)
	{
		alert('Не выбрано ни одной строки для отправки в архив');
	}
	else
	{
		ModalConfirm('Вы действительно хотите убрать в архив выделенные строки?', function(){
			form.elements['Do'].value = 'Action';
			$(form).append('<input type="hidden" name="Action" value="SendToArchive" />');
			form.submit();	
		});			
	}
	return false;
}

//remove from archive control
function MultipleRemoveFromArchive(form, elmName)
{
    var checked = 0;
    for (var i = 0; i < form.elements.length; i++)
    {
        if (form.elements[i].name == elmName && form.elements[i].type == "checkbox" && form.elements[i].checked)
        {
            checked++;
        }
    }

    if (checked == 0)
    {
        alert('Не выбрано ни одной строки для отправки в архив');
    }
    else
    {
        ModalConfirm('Вы действительно хотите восстановить из архива выделенные строки?', function(){
            form.elements['Do'].value = 'Action';
            $(form).append('<input type="hidden" name="Action" value="RemoveFromArchive" />');
            form.submit();
        });
    }
    return false;
}
//email control
function InitEmailControl(control)
{
	control.find(".custom-control-email-row").hide();
	control.find(".add-row").click(function(){
		AddEmailRow(control, "");
		return  false;
	});
}
function AddEmailRow(control, email)
{
	var ind = 1;
	
	while(control.find(".custom-control-email-row#row"+ind).length > 0){
		ind++;
	}
	
	var namePrefix = control.attr("id")+ind;
	var newRow =  control.find(".custom-control-email-row:first").clone();
	newRow.attr("id", "row"+ind);
//	console.log(email);
	newRow.find(".custom-control-email-row-number").attr("name", namePrefix+"Number").attr("value", email);

	newRow.find(".remove-row").click(function(){
		$(this).parent().parent().parent().remove();
		return false;
	});
	
	control.children(".custom-control-email-rows").append(newRow);
	newRow.show();
}

//phone control
function InitPhoneControl(control)
{
	control.find(".custom-control-phone-row").hide();
	control.find(".add-row").click(function(){
		AddPhoneRow(control, "mobile", "", "");
		return  false;
	});
}
function AddPhoneRow(control,type,number)
{
	var ind = 1;

	while(control.find(".custom-control-phone-row#row"+ind).length > 0){
		ind++;
	}
	
	var namePrefix = control.attr("id")+ind;
	var newRow =  control.find(".custom-control-phone-row:first").clone();
	newRow.attr("id", "row"+ind);
	newRow.find(".custom-control-phone-row-type").attr("name", namePrefix+"Type").children("option[value="+type+"]").attr("selected", "selected");
	//newRow.find(".custom-control-phone-row-prefix").attr("name", namePrefix+"Prefix").attr("value", prefix);
	//newRow.find(".custom-control-phone-row-number").attr("name", namePrefix+"Number").attr("value", number);
	newRow.find(".custom-control-phone-row-number").attr("name", namePrefix+"Number").attr("value", number);
	newRow.find(".remove-row").click(function(){
		$(this).parent().parent().parent().remove();
		return false;
	});
	control.children(".custom-control-phone-rows").append(newRow);
	newRow.find("[data-mask]").each(function(i, el) {
		var $this = $(el);
		var mask = $this.data('mask').toString();
		var opts = {
                numericInput: getValue($this, 'numeric', false),
                radixPoint: getValue($this, 'radixPoint', ''),
                rightAlign: getValue($this, 'numericAlign', 'left') == 'right'
            };
        var placeholder = getValue($this, 'placeholder', '');
        var is_regex = getValue($this, 'isRegex', '');
		if (mask.toLowerCase() == "phone") {
            mask = "+7-(999)-999-99-99";
            if (is_regex) {
                opts.regex = mask;
                mask = 'Regex';
            }

            $this.inputmask(mask, opts);
        }
    });
	newRow.show();
}

//season control
function InitSeasonControl(control)
{
    control.find(".custom-control-season-row").hide();
    control.find(".add-row").click(function(){
        AddSeasonRow(control, "");
        return false;
    });
}
function AddSeasonRow(control, season)
{
    var ind = 1;

    while(control.find(".custom-control-season-row#row"+ind).length > 0){
        ind++;
    }

    var namePrefix = control.attr("id")+ind;
    var newRow =  control.find(".custom-control-season-row:first").clone();
    newRow.attr("id", "row"+ind);
    newRow.find(".custom-control-season-row-type").attr("name", namePrefix+"Type").children("option[value='"+season+"']").attr("selected", "selected");
    newRow.find(".remove-row").click(function(){
        $(this).parent().parent().parent().remove();
        return false;
    });

    newRow.find(".custom-control-season-row-type").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});
    newRow.find(".custom-control-season-row-type").removeClass("form-control");

    control.children(".custom-control-season-rows").append(newRow);
    newRow.show();
}

//staff control
function InitStaffControl(control)
{
    control.find(".custom-control-staff-row").hide();
    control.find(".add-row").click(function(){
        AddStaffRow(control, "");
        return false;
    });
}
function AddStaffRow(control, staff)
{
    var ind = 1;

    while(control.find(".custom-control-staff-row#row"+ind).length > 0){
        ind++;
    }

    var namePrefix = control.attr("id")+ind;
    var newRow =  control.find(".custom-control-staff-row:first").clone();
    newRow.attr("id", "row"+ind);
    newRow.find(".custom-control-staff-row-type").attr("name", namePrefix+"Type").children("option[value='"+staff+"']").attr("selected", "selected");
    newRow.find(".remove-row").click(function(){
        $(this).parent().parent().parent().remove();
        return false;
    });

    newRow.find(".custom-control-staff-row-type").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});
    newRow.find(".custom-control-staff-row-type").removeClass("form-control");
    control.children(".custom-control-staff-rows").append(newRow);
    newRow.show();
}

//friends control
function InitFriendsControl(control)
{
    control.find(".custom-control-friends-row").hide();
    control.find(".add-row").click(function(){
        AddFriendsRow(control, "");
        return false;
    });
}
function AddFriendsRow(control, staff)
{
    var ind = 1;

    while(control.find(".custom-control-friends-row#row"+ind).length > 0){
        ind++;
    }

    var namePrefix = control.attr("id")+ind;
    var newRow =  control.find(".custom-control-friends-row:first").clone();
    newRow.attr("id", "row"+ind);
    newRow.find(".custom-control-friends-row-type").attr("name", namePrefix+"Type").children("option[value='"+staff+"']").attr("selected", "selected");
    newRow.find(".remove-row").click(function(){
        $(this).parent().parent().parent().remove();
        return false;
    });

    newRow.find(".custom-control-friends-row-type").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});
    newRow.find(".custom-control-friends-row-type").removeClass("form-control");

    control.children(".custom-control-friends-rows").append(newRow);
    newRow.show();
}

//document control
function InitDocumentControl(control)
{
    control.find(".custom-control-document-row").hide();
    control.find(".add-row").click(function(){
        AddDocumentRow(control, false, "passport", "", 0);
        return  false;
    });
}
function AddDocumentRow(control, IsMain, type, number, documentId)
{
    var ind = 1;

    while(control.find(".custom-control-document-row#row"+ind).length > 0){
        ind++;
    }

    var namePrefix = control.attr("id")+ind;
    var newRow =  control.find(".custom-control-document-row:first").clone();
    var documentMain = newRow.find(".custom-control-document-main");

    newRow.attr("id", "row"+ind);
    newRow.find(".custom-control-document-row-type").attr("name", namePrefix+"Type").children("option[value="+type+"]").attr("selected", "selected");
    if (IsMain)
        documentMain.prop("checked", true);
    documentMain.val(ind);
    documentMain.attr("id","Document"+ind);
    documentMain.iCheck({
        checkboxClass: 'icheckbox_minimal-green',
        radioClass: 'iradio_minimal-green',
        increaseArea: '20%'
    });

    newRow.find("label").attr("for","Document"+ind);
    newRow.find(".custom-control-document-row-number").attr("name", namePrefix+"Number").attr("value", number);
    newRow.find(".custom-control-document-id").attr("name", namePrefix+"Id").attr("value", documentId);
    newRow.find(".remove-row").click(function(){
        $(this).closest(".custom-control-document-row").remove();
        return false;
    });
    control.children(".custom-control-document-rows").append(newRow);
    DataMask(newRow);
    newRow.show();
}
//phone control
function InitDirectoryControl(control)
{
	control.find(".custom-control-phone-row:first").hide();
	control.find(".add-row").click(function(){
		AddDirectoryRow(control, "mobile", "", "");
		return  false;
	});
}
function AddDirectoryRow(control,type,number)
{
	var ind = 1;

	while(control.find(".custom-control-phone-row#row"+ind).length > 0){
		ind++;
	}
	
	var namePrefix = control.attr("id")+ind;
	var newRow =  control.find(".custom-control-phone-row:first").clone();
	newRow.attr("id", "row"+ind);
	newRow.find(".custom-control-phone-row-type").attr("name", "Color[]").children("option[value="+type+"]").attr("selected", "selected");
	//newRow.find(".custom-control-phone-row-prefix").attr("name", namePrefix+"Prefix").attr("value", prefix);
	//newRow.find(".custom-control-phone-row-number").attr("name", namePrefix+"Number").attr("value", number);
	newRow.find(".custom-control-phone-row-number").attr("name", "Number[]").attr("value", number);
	newRow.find(".remove-row").click(function(){
		$(this).parent().parent().parent().remove();
		return false;
	});
	control.children(".custom-control-phone-rows").append(newRow);
	newRow.show();
}

function getValue($el, data_var, default_val) {
    if (typeof $el.data(data_var) != 'undefined') {
        return $el.data(data_var);
    }

    return default_val;
}

function GetPhoneData(control)
{
	var id = control.attr('id');
	var componentFormData = new Object();
	componentFormData['ID'] = id;
	componentFormData['Type'] = 'component-phone';
	componentFormData['Data'] = new Array();
	control.find('.custom-control-phone-row:visible').each(function(inputIndex, el){
		componentFormData['Data'][inputIndex] = new Object();
		$(this).find('select, input').each(function(i, el){
			var inputData = GetInputData(el);
			if(inputData !== null)
			{	
				inputData["Name"] = inputData["Name"].replace(new RegExp("^"+id+"[0-9]+",'g'), "");
				componentFormData['Data'][inputIndex][inputData["Name"]] = inputData["Value"];
			}
		});
	});
	return componentFormData;
}

function GetEmailData(control)
{
	var id = control.attr('id');
	var componentFormData = new Object();
	componentFormData['ID'] = id;
	componentFormData['Type'] = 'component-email';
	componentFormData['Data'] = new Array();
	control.find('.custom-control-email-row:visible').each(function(inputIndex, el){
		componentFormData['Data'][inputIndex] = new Object();
		$(this).find('select, input').each(function(i, el){
			var inputData = GetInputData(el);
			if(inputData !== null)
			{	
				inputData["Name"] = inputData["Name"].replace(new RegExp("^"+id+"[0-9]+",'g'), "");
				componentFormData['Data'][inputIndex][inputData["Name"]] = inputData["Value"];
			}
		});
	});
	return componentFormData;
}

function GetSeasonData(control)
{
    var id = control.attr('id');
    var componentFormData = new Object();
    componentFormData['ID'] = id;
    componentFormData['Type'] = 'component-season';
    componentFormData['Data'] = new Array();
    control.find('.custom-control-season-row:visible').each(function(inputIndex, el){
        componentFormData['Data'][inputIndex] = new Object();
        $(this).find('select, input').each(function(i, el){
            var inputData = GetInputData(el);
            if(inputData !== null)
            {
                inputData["Name"] = inputData["Name"].replace(new RegExp("^"+id+"[0-9]+",'g'), "");
                componentFormData['Data'][inputIndex][inputData["Name"]] = inputData["Value"];
            }
        });
    });
    return componentFormData;
}

function GetStaffData(control)
{
    var id = control.attr('id');
    var componentFormData = new Object();
    componentFormData['ID'] = id;
    componentFormData['Type'] = 'component-staff';
    componentFormData['Data'] = new Array();
    control.find('.custom-control-staff-row:visible').each(function(inputIndex, el){
        componentFormData['Data'][inputIndex] = new Object();
        $(this).find('select, input').each(function(i, el){
            var inputData = GetInputData(el);
            if(inputData !== null)
            {
                inputData["Name"] = inputData["Name"].replace(new RegExp("^"+id+"[0-9]+",'g'), "");
                componentFormData['Data'][inputIndex][inputData["Name"]] = inputData["Value"];
            }
        });
    });
    return componentFormData;
}

function GetFriendsData(control)
{
    var id = control.attr('id');
    var componentFormData = new Object();
    componentFormData['ID'] = id;
    componentFormData['Type'] = 'component-friends';
    componentFormData['Data'] = new Array();
    control.find('.custom-control-friends-row:visible').each(function(inputIndex, el){
        componentFormData['Data'][inputIndex] = new Object();
        $(this).find('select, input').each(function(i, el){
            var inputData = GetInputData(el);
            if(inputData !== null)
            {
                inputData["Name"] = inputData["Name"].replace(new RegExp("^"+id+"[0-9]+",'g'), "");
                componentFormData['Data'][inputIndex][inputData["Name"]] = inputData["Value"];
            }
        });
    });
    return componentFormData;
}

function SetPhoneData(form, data)
{
	var control = form.find('#'+data['ID']);
	for(var i = 0; i < data['Data'].length; i++)
	{
		if(i+1 > control.find('.custom-control-phone-row:visible').size())
			AddPhoneRow(control,data['Data'][i]['Type'],data['Data'][i]['Number']);	
	}
}

//status control
function InitStatusControl(control)
{
	/*
	var radioValue = control.find("input[name='"+control.attr("id")+"']:checked").val();
	if(radioValue != 3){
		//control.find(".status-additional-3").hide();
	}
	if(radioValue != 6){
		//control.find(".status-additional-6").hide();
	}
	control.find("input[name='"+control.attr("id")+"']").change(function(){
		control.submit();
	});
	control.find('.status-additional-3, .status-additional-6').change(function(){
		control.find('input[name=Status]').prop('checked', false);
	});
	control.find('.remove-status-button').click(function() {

		var entityID = $("#entity-id-field").val();
		var entity = $("#entity-field").val();

		$.ajax({
			url: PROJECT_PATH+'module/crm/ajax.php',
			method: 'POST',
			dataType: 'JSON',
			data:{
				Action: 'ClearPanel',
				Entity: entity, 
				EntityID: entityID
			},
			success:function(data){
				if (data.HTML == 1) {
					$("#status-field-7").attr("checked", true);
					window.location.replace("/module.php?load=crm&entity="+entity+"&&EntityViewID="+entityID);
				} else {
					alert("Ошибка! Не удалось очистить панель!");
				}
			}
		});
	});
	*/
}

//itemlist control
function InitItemListControl(control){
	
	control.find(".custom-control-itemlist-row").hide();
	control.find(".add-row").click(function(){
		AddItemListRow(control, null);
		return  false;
	});
}
function AddItemListRow(control,params){
	
	var ind = 1;
	while(control.find(".custom-control-itemlist-row#row"+ind).length > 0){
		ind++;
	}
	var namePrefix = control.attr("id")+ind;
	var newRow =  control.find(".custom-control-itemlist-row:first").clone();
	newRow.attr("id", "row"+ind);
	newRow.find(".custom-control-itemlist-component").each(function(){
		var componentName = $(this).attr("data-name");
		var componentType = $(this).attr("data-type");
		if(componentType == "input-text"){
			$(this).attr("name", namePrefix+componentName);
			if(params) $(this).val(params[componentName]);
		}
		else if(componentType == "input-date"){
            $(this).attr("name", namePrefix+componentName);
            if(params) {
            	var date = new Date(params[componentName]);
            	if (date != "Invalid Date" && params[componentName] != null)
					$(this).val(date.toLocaleDateString("ru-RU"));
			}
		}
		else if(componentType == "input-select"){
			$(this).attr("name", namePrefix+componentName);
			if(params) $(this).children("option[value='"+params[componentName]+"']").attr("selected", "selected");
		}
		else if(componentType == "custom-phone"){
			$(this).attr("id", namePrefix+componentName);
			InitPhoneControl($(this));

			if(params && params[componentName+"List"]){
				for(var i=0; i<params[componentName+"List"].length; i++){
					var phone = params[componentName+"List"][i];
					AddPhoneRow($(this), phone["Type"], phone["Prefix"]+phone["Number"]);
				}
			}
		}
		else if(componentType == "custom-email"){
			$(this).attr("id", namePrefix+componentName);
			InitEmailControl($(this));
			
			if(params && params[componentName+"List"]){
				for(var i=0; i<params[componentName+"List"].length; i++){
					var email = params[componentName+"List"][i];
					AddEmailRow($(this), email["Email"]);
				}
			}
		}
	});
	newRow.find(".remove-row").click(function(){
		$(this).parent().parent().remove();
		return false;
	});
	control.children(".custom-control-itemlist-rows").append(newRow);
	DataMask(newRow);
	newRow.show();
}

function GetItemListData(control)
{
	var id = control.attr('id');
	var componentFormData = new Object();
	componentFormData['ID'] = id;
	componentFormData['Type'] = 'component-itemlist';
	componentFormData['Data'] = new Array();
	control.find('.custom-control-itemlist-row:visible').each(function(itemlistIndex, itemlistElement){
		componentFormData['Data'][itemlistIndex] = new Object();
		$(this).find('select, input').each(function(inputIndex, inputElement){
			if($(this).closest('.custom-control-phone').length == 0 && typeof $(this).attr('name') != 'undefined')
			{
				var inputData = GetInputData(inputElement);
				if(inputData !== null)
				{
					inputData["Name"] = inputData["Name"].replace(new RegExp(id+"[0-9]+",'g'), "");
					componentFormData['Data'][itemlistIndex][inputData["Name"]] = inputData["Value"];
				}
			}
		});
		$(this).find('.custom-control-phone:visible').each(function(phoneIndex, phoneElement){
			var phoneData = GetPhoneData($(phoneElement));
			componentFormData['Data'][itemlistIndex][$(this).attr('data-name')+'List'] = phoneData['Data'];
		});
		$(this).find('.custom-control-email:visible').each(function(emailIndex, emailElement){
			var emailData = GetEmailData($(emailElement));
			componentFormData['Data'][itemlistIndex][$(this).attr('data-name')+'List'] = emailData['Data'];
		});
        $(this).find('.custom-control-season:visible').each(function(seasonIndex, seasonElement){
            var seasonData = GetSeasonData($(seasonElement));
            componentFormData['Data'][itemlistIndex][$(this).attr('data-name')+'List'] = seasonData['Data'];
        });
        $(this).find('.custom-control-staff:visible').each(function(staffIndex, staffElement){
            var staffData = GetStaffData($(staffElement));
            componentFormData['Data'][itemlistIndex][$(this).attr('data-name')+'List'] = staffData['Data'];
        });
        $(this).find('.custom-control-friends:visible').each(function(friendsIndex, friendsElement){
            var friendsData = GetFriendsData($(friendsElement));
            componentFormData['Data'][itemlistIndex][$(this).attr('data-name')+'List'] = friendsData['Data'];
        });
	});
	return componentFormData;
}

function SetItemListData(form, data)
{
	var control = form.find('#'+data['ID']);
	for(var i = 0; i < data['Data'].length; i++)
	{
		if(i+1 > control.find('.custom-control-itemlist-row:visible').size())
		{
			AddItemListRow(control,data['Data'][i]);	
		}
	}
}

//file control
function InitFileControl(control)
{
	control.find(".custom-control-file-row").each(function(){
		$(this).find(".remove-row").click(function(){
			var row = $(this).parent().parent();
			ModalConfirm('Вы действительно хотите удалить вложение', function(){
				var removeInput = control.find("input[name='"+control.attr("id")+"Remove']");
				var oldRemove = removeInput.val();
				if(oldRemove.length == 0){
					removeInput.val(row.attr("id"));
				}
				else {
					removeInput.val(oldRemove+","+row.attr("id"));
				}
				row.remove();
			});
			return false;
		});
	});
}

function SaveFormData(form, entity, entityID)
{
	formData = new Array();
	//collect main entity fields
	form.find('select, input, textarea').each(function(index, el){
		if($(this).closest('.custom-control-phone, .custom-control-itemlist').length == 0 && typeof $(this).attr('name') != 'undefined')
		{
			data = GetInputData(el);
			if(data !== null)
				formData.push(data);
		}
	});
	//collect main entity phones
	form.find('.custom-control-phone:visible').each(function(index, el){
		if($(this).closest('.custom-control-itemlist').length == 0){
			data = GetPhoneData($(this));
			formData.push(data);
		}
	});
	//collect main entity email
	form.find('.custom-control-email:visible').each(function(index, el){
		if($(this).closest('.custom-control-itemlist').length == 0){
			data = GetEmailData($(this));
			formData.push(data);
		}
	});

	//collect itemlists
	form.find('.custom-control-itemlist:visible').each(function(index, el){
		data = GetItemListData($(this));
		formData.push(data);
	});

	//save to local storage
	var string = window.JSON.stringify(formData);
	if(typeof localStorage != 'undefined')
	{
		localStorage.setItem('form-data-'+entity+'-'+entityID, string);
		dateObj = new Date();
		$('#autosave-info').html('Автоматически сохранено в браузере '+dateObj.toLocaleString());
	}
	else
	{
		CreateMessage('Для автосохранения необходима поддержка браузером localStorage', 'error');
		return;
	}
}

function LoadFormData(form, entity, entityID)
{
	if(typeof localStorage != 'undefined')
	{
		if(typeof localStorage['form-data-'+entity+'-'+entityID] != 'undefined')
		{
			var formData = window.JSON.parse(localStorage['form-data-'+entity+'-'+entityID]);

			for(var i = 0; i < formData.length; i++)
			{
				data = formData[i];
				if(data['Type'] == 'component-itemlist')
				{
					SetItemListData(form, data);
				}
				else if(data['Type'] == 'component-phone')
				{
					SetPhoneData(form, data);
				}
				else
				{
					SetInputData(form, data);
				}
			}
		}
		else
		{
			//console.log('not found');
		}
	}
	else
	{
		CreateMessage('Для автосохранения необходима поддержка браузером localStorage', 'error');
	}
}

function ClearFormData(entity, entityID)
{
	if(typeof localStorage != 'undefined')
	{
		if(typeof localStorage['form-data-'+entity+'-'+entityID] != 'undefined')
		{
			localStorage.removeItem('form-data-'+entity+'-'+entityID);
		}
	}
	else
	{
		alert('localStorage required');
	}
}

function GetInputData(el)
{
	if($(el).is('select'))
	{
		var selected = new Array();
		$(el).find('option:selected').each(function(index, el){
			selected.push($(this).val());
		});
		if(selected.length == 1 && typeof selected[0] != 'undefined')
		{
			selected = selected[0];
		}
		return {'Type':'select', 'Name': $(el).attr('name'), 'Value': selected};
	}
	else if($(el).is('textarea'))
	{
		if($(el).siblings('.cke').size() > 0)
			return {'Type':'textarea', 'Name': $(el).attr('name'), 'Value': CKEDITOR.instances[$(el).attr('name')].getData()};
		else
			return {'Type':'textarea', 'Name': $(el).attr('name'), 'Value': $(el).val()};
	}
	else
	{
		switch($(el).attr('type'))
		{
			case 'text':
			case 'email':
			case 'tel':
			case 'password':
			case 'hidden':
				//don't save generated text fields
				if($(el).attr('name').indexOf('[]') == -1)
				{
					return {'Type':'text', 'Name': $(el).attr('name'), 'Value': $(el).val()};
				}
				break;
			case 'radio':
				if($(el).is(':checked'))
				{
					return {'Type':$(el).attr('type'), 'Name': $(el).attr('name'), 'Value': $(el).val()};
				}
				break;
			case 'checkbox':
				val = new Array();
				if($(el).is(':checked'))
				{
					val.push($(el).val());
				}
				return {'Type':$(el).attr('type'), 'Name': $(el).attr('name'), 'Value': val};
				break;
		}
	}
	return null;
}

function SetInputData(form, data)
{
	switch(data['Type'])
	{
		case 'select':
			if(data['Value'] instanceof Array)
			{
				for(var i = 0; i < data['Value'].length; i++)
				{
					form.find('select[name=\"'+PrepareForSelector(data['Name'])+'\"] option[value=\"'+PrepareForSelector(data['Value'][i])+'\"]').attr('selected', 'selected');
				}
			}
			else
			{
				form.find('select[name=\"'+PrepareForSelector(data['Name'])+'\"] option[value=\"'+PrepareForSelector(data['Value'])+'\"]').attr('selected', 'selected');
			}
			form.find('select[name=\"'+PrepareForSelector(data['Name'])+'\"]').trigger('change');
			break;
		case 'text':
			form.find('input[name=\"'+PrepareForSelector(data['Name'])+'\"]').val(data['Value']).attr('value', data['Value']).trigger('change');
			break;
		case 'textarea':
			form.find('textarea[name=\"'+PrepareForSelector(data['Name'])+'\"]').val(data['Value']).html(data['Value']).trigger('change');
			break;
		case 'radio':
			form.find('input[name=\"'+PrepareForSelector(data['Name'])+'\"][value=\"'+PrepareForSelector(data['Value'])+'\"]').prop('checked', true).attr('checked', true).trigger('change');
			break;
		case 'checkbox':
			if(data['Value'] instanceof Array)
			{
				for(var i = 0; i < data['Value'].length; i++)
				{
					form.find('input[name=\"'+PrepareForSelector(data['Name'])+'\"][value=\"'+PrepareForSelector(data['Value'][i])+'\"]').prop('checked', true).attr('checked', true);
				}
			}
			else
			{
				form.find('input[name=\"'+PrepareForSelector(data['Name'])+'\"][value=\"'+PrepareForSelector(data['Value'])+'\"]').prop('checked', true).attr('checked', true);;
			}
			form.find('input[name=\"'+PrepareForSelector(data['Name'])+'\"]').trigger('change');
			break;
	}
}

function PrepareForSelector(value)
{
	return value.replace(/[!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~]/g, "\\$&");
}

function InitContractTableControl(control)
{
	control.find('.contract-toggle-link').each(function(){
		var id = $(this).attr('ContractID') ? $(this).attr('ContractID') : 0;
		if(control.find('.contract-row-inner[ContractID='+id+']').size() == 0){
			$(this).remove();
		}
	});
	control.find('.contract-toggle-link').unbind('click');
	control.find('.contract-toggle-link').click(function(e){
		control.find('.contract-row-inner[ContractID='+$(this).attr('ContractID')+']').toggle();
		$(this).find('i').toggleClass('fa-chevron-circle-right fa-chevron-circle-down');
		e.preventDefault();
	});
}

function InitStatusTableControl(control)
{
    control.find('.status-toggle-link').unbind('click');
}

function InitDuplicateControl(params, entity)
{
	var inputSelector = new Array();
	for(i = 0; i < params.length; i++)
	{
		inputSelector.push("input[name='"+params[i]["Field"]+"']");
	}
	inputSelector = inputSelector.join(", ");
	
	var typingTimer;				//timer identifier
	var lastInput = null;
	var doneTypingInterval = 500;	//time in ms, 1 second for example
	var $input = $(inputSelector);

	//on keyup, start the countdown
	$input.on('keyup', function () {
		lastInput = $(this);
		lastInput.popover('destroy');
		clearTimeout(typingTimer);
		if($.trim($(this).val()).length > 0)
			typingTimer = setTimeout(doneTyping, doneTypingInterval);
	});

	//on keydown, clear the countdown 
	$input.on('keydown', function () {
		clearTimeout(typingTimer);
		lastInput = $(this);
		lastInput.popover('destroy');
	});

	//user is "finished typing," do something
	function doneTyping () 
	{
		data = new Object();
		data.Action = "GetDuplicateListHTML";
		data.Entity = entity;
		data.FullList = 1;
		data.DuplicateParams = new Array();
		
		for(var i=0; i < params.length; i++)
		{
			data[params[i]["Filter"]] = $('input[name='+params[i]["Field"]+']').val();
		}
		$.ajax({
			url: PROJECT_PATH+'module/crm/ajax.php',
			method: 'POST',
			dataType: 'JSON',
			data:data,
			success:function(data){
				if(data.HTML)
				{
					html = data.HTML;
					lastInput.popover({
						html: true, 
						title: 'Подозрение на дубликат',
						content: function(){return html;},
						trigger: 'manual',
						template: '<div class="popover danger" style="max-width:360px;width:360px;"><div class="arrow"></div><span class="close ignore">&times;</span><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
						placement: 'bottom'
					}).on('shown.bs.popover', function(e){
						var popover = $(this);
						$(this).parent().find('div.popover .ignore').on('click', function(e){
							popover.popover('hide');
							e.preventDefault();
						});
					});
					lastInput.popover('show');
				}
			},
		});
	}
}

function InitUnreadTaskChecker()
{
	var taskChecker = setInterval(function(){
		$.ajax({
			url: PROJECT_PATH+'module/crm/ajax.php',
			method: 'POST',
			dataType: 'JSON',
			data:{
				Action: 'GetUnreadTaskCount'
			},
			success:function(data){
				currentUnreadTaskCount = parseInt($('.unread-task-count:first').html());
				if(isNaN(currentUnreadTaskCount))
					currentUnreadTaskCount = 0;
				if(data.UnreadTaskCount > 0)
					$('.unread-task-count').html(data.UnreadTaskCount).removeClass('hidden');
				else
					$('.unread-task-count').html(data.UnreadTaskCount).addClass('hidden');
				if(data.UnreadTaskCount > currentUnreadTaskCount && IS_MOBILE == true)
				{
					var audio = new Audio(PROJECT_PATH+'module/crm/template/sound/new_task.mp3');
					audio.play();
				}
			},
		});
	}, 5000);
}

function LoadPopupItemList(data, container)
{
	$.ajax({
		url: PROJECT_PATH+'module/crm/ajax.php?Action=GetEventList',
		method: 'POST',
		dataType: 'JSON',
		data:data,
		success:function(data){
			container.html(data.HTML);
		},
	});
}

function LoadPopupEditForm(entity, entityID, afterSave, afterRemove)
{
	$.ajax({
		url: PROJECT_PATH+'module/crm/ajax.php',
		method: 'POST',
		dataType: 'JSON',
		data:{
			Action: 'GetEntityEditPopupForm',
			Entity: entity, 
			EntityID: entityID
		},
		success:function(data){
			if(data.HTML.length > 0)
			{
				modal = $(data.HTML);
				modal.find('form').attr('AfterSave', afterSave);
				modal.find('form').attr('AfterRemove', afterRemove);
				modal.modal({backdrop: 'static',
				    		keyboard: false});
				modal.modal('show');
				modal.find('.close, .modal-close').click(function(){
					modal.modal('hide');
					modal.remove();
				});
			}
		}
	});
}

$(document).on('submit', '.ajax-form', function(e){
	var form = $(this);
	var formData = form.serialize();
	form.find('.form-error, .form-message').addClass('hidden');
	$.ajax({
		url: form.attr('action'),
		method: 'POST',
		dataType: 'JSON',
		data:formData,
		success:function(data){
			if(typeof data.Error != 'undefined' && data.Error.length > 0)
			{
				form.find('.form-error').html(data.Error).removeClass('hidden');
			}
			if(typeof data.Message != 'undefined' && data.Message.length > 0)
			{
				form.find('.form-message').html(data.Message).removeClass('hidden');
				if(form.attr('AfterSave'))
				{
					window[form.attr('AfterSave')]();
				}
				setTimeout(function(){
					form.closest('.modal').modal('hide');
					form.closest('.modal').remove();
				}, 750);
			}
		}
	});
	e.preventDefault();
});

$(document).on('click', '.ajax-form .popup-entity-remove', function(e){
	var form = $(this).closest('form');
	var entity = form.find('input[name=Entity]').val();
	var entityID = form.find('input[name=EntityID]').val();
	$.ajax({
		url: PROJECT_PATH+'module/crm/ajax.php',
		method: 'POST',
		dataType: 'JSON',
		data:{
			Action: 'RemoveEntity',
			Entity: entity, 
			EntityIDs: [entityID]
		},
		success:function(data){
			if(form.attr('AfterRemove'))
			{
				window[form.attr('AfterRemove')]();
			}
			form.closest('.modal').modal('hide');
			form.closest('.modal').remove();
		}
	});
	e.preventDefault();
});

function removeCommentRow(e, str, commentId, fr){
	e.preventDefault();
	var form = $("#"+fr).find("form")[0];
	ModalConfirm(str, function(){
		form.elements['Action'].value = 'Remove'+fr;
		form.elements['CommentID'].value = commentId;
		form.submit();
	});
}

function editCommentRow(e, str, commentId, fr, SeasonID) {
	e.preventDefault();

	var form = $("#"+fr).find("form")[0];
	$(form).slideDown();
	var filelist = $(e.target).closest(".comment-row").find(".comment-filelist a");
	var editFilelist = $(form).find("p");
	editFilelist.empty();
	form.elements['Action'].value = 'Update'+fr;
	form.elements['CommentID'].value = commentId;

	$("#CharacteristicSeasonID").val(SeasonID).trigger('change.select2');
	$("#CharacteristicSeasonID").on("click", function() {
		form.elements['SeasonID'].value = $(this).val();
	});

	console.log(SeasonID);

	$("#"+fr).slideDown(600);
	form.elements['Text'].value = $(e.target).closest(".comment-row").find("."+fr+"Text").text();
	$.each(filelist, function(k ,v){
		var clone = $("#FileControlRow").clone();
		clone.removeAttr("hidden");
		clone.removeAttr("id");
		clone.find("[href=FilePath]").replaceWith(v.cloneNode(true));
		clone.find(".remove-file").data("value", v.getAttribute("data-value"));
		editFilelist.append(clone);
	});

	$(form).find("[type=submit]").text(str);
	$(form).find(".cancel-edit").removeClass("hidden");

}

function cancelEditComment(e, str, fr) {
	e.preventDefault();
	var form = $(e.target).closest("form")[0];
	var editFilelist = $(form).find("p");
	editFilelist.empty();

	form.elements['Action'].value = 'Add'+fr;
	form.elements['CommentID'].value = "";

	$("#"+fr).slideUp(600);
	form.elements['Text'].value = "";
	$(form).find("[type=submit]").text(str);
	$(form).find(".cancel-edit").addClass("hidden");
	$(form).slideUp();
}

function InitDateTimePickers()
{
	$('.form_datetime').datetimepicker({
        language:  'ru',
        format: "dd.mm.yyyy hh:ii",
        linkFormat: "yyyy-mm-dd hh:ii",
        weekStart: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 1,
        minuteStep: 30,
        showMeridian: 0,
        startDate: new Date()
    });
}

Date.prototype.yyyymmdd = function() {
	var yyyy = this.getFullYear().toString();
	var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
	var dd = this.getDate().toString();
	return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
};

function RuDateStringToDate(ru)
{
	ruArr = ru.split(".");
	date = new Date(ruArr[2], ruArr[1]-1, ruArr[0]);
	return date;
}

$(document).on('click', '.open-finance-attachment-popup', function(e){
	var entity = $(this).attr('Entity');
	var entityID = $(this).attr('EntityID');
	$.ajax({
		url: PROJECT_PATH+'module/crm/ajax.php',
		method: 'POST',
		dataType: 'JSON',
		data:{
			Action: 'GetFinanceAttachmentPopup',
			Entity: entity,
			EntityID: entityID
		},
		success:function(data){
			var modal = GetSimpleModal("Прикрепить финансовый документ", data.HTML);
			modal.modal('show');
			modal.find('.close, .modal-backdrop').click(function(){
				modal.modal('hide');
				modal.remove();
			});
		},
	});
});

$(document).on('click', '.finance-attachment-add', function(e){
	var modal = $(this).closest('.modal');
	var url = $(this).attr('href');
	var filepath = PROJECT_DIR + 'var/data/mailing/attachment/' + $(this).attr('filename');
	$.ajax({
		url: url,
		method: 'POST',
		success:function(data){
			$('.finance-attachment-list').append('<div class="row top15"><div class="col-md-6 col-xs-9"><input type="text" name="AttachmentList[]" class="form-control" value="'+filepath+'" readonly> </div><div class="col-md-6 col-xs-3"><div class="form-control simple"><a href="#" class="remove-finance-attachment"><i class="box_close fa fa-times"></i></a></div></div></div>')
			modal.modal('hide');
			modal.remove();
		},
	});
	e.preventDefault();
});

$(document).on('click', '.add-attachment', function(e){
	var html = '<div class="row form-inline bottom5 hidden"><div class="col-md-12"><input type="file" name="AttachmentFileList[]" class="form-control" /> <a href="#" class="form-control simple remove-attachment"><i class="fa fa-close"></i></a></div></div>'
	$(html).appendTo('.attachment-list').find('input[type=file]').trigger('click');
	e.preventDefault();
});

$(document).on('click', '.remove-attachment', function(e){
	$(this).closest('.row').remove();
	e.preventDefault();
});
$(document).on('click', '.remove-finance-attachment', function(e){
	$(this).closest('.row').remove();
	e.preventDefault();
});

$(document).on('change', 'input[name="AttachmentFileList[]"]', function(e){
	if($(this).val())
	{
		$(this).closest('.row').removeClass('hidden');
	}
	else
	{
		$(this).closest('.row').remove();
	}
});

$(document).on('click', '.submit-with-param', function(e){
	var form = $(this).closest('form');
	form.find('input[name='+PrepareForSelector($(this).attr('param'))+']').val($(this).attr('value'));
	form.submit();
	e.preventDefault();
});

$(document).on("click", ".show-all-comments", function(e){
    $(this).siblings('.comment-row').removeClass('hidden');
    e.preventDefault();
    $(this).remove();
});



function ToggleStaffContractType(contractID)
{
    $.ajax({
        url: PROJECT_PATH+'module/crm/ajax.php',
        type: 'post',
        dataType: 'JSON',
        data:{
            Action: 'StaffContractSelectChange',
            ContractID: contractID
        },
        success: function(data) {
            if (data == "employment") {
                $('#ArticleType').val(2);
                $('#IncomeType').hide();
                $('#OutcomeType').show();
                $('#ArticleID').val($('#OutcomeSelect').val());
                $('#ActionType').val("AddPayback");
			}
            else {
                $('#ArticleType').val(1);
                $('#IncomeType').show();
                $('#OutcomeType').hide();
                $('#ArticleID').val($('#IncomeSelect').val());
                $('#ActionType').val("AddPayment");
			}
        },
    });
}

$(document).on("click", '.open-link', function (event) {
    event.preventDefault();
    event.stopPropagation();
    var fr = $("#"+$(this).attr("for"));
    fr.find("[type=submit]").text("Добавить");
    fr.find("p").empty();
    fr.find("textarea").val("");
    fr.find("[name=Action]").val('Add'+$(this).attr("for"));
    fr.find('[name=CommentID]').val("");
    fr.slideDown(600);
    $(this).hide();
});

$(document).ready(function(){
	if ($.isFunction($.fn.dataTable)) {
		$('.data-table').dataTable({
			paging: false,
			info: false,
			searching: false,
		});

		//send child to archive
        if (document.getElementById("ChildArchive") != null) {
            $('#Category').on('change', function() {
                if ($('#Category').val() == 7)
                    $('#ChildArchive').val("Y");
                else
                    $('#ChildArchive').val("N");
            });
		}

        //staff contract
        if (document.getElementById("ContractID") != null)
            ToggleStaffContractType($('#ContractID').val());
        $('#ContractID').on('change', function() {
            ToggleStaffContractType(this.value);
        });
        $('.ArticleID').on('click', function() {
            $('#ArticleID').val(this.value);
        });
	}
});

$(document).on("click", '.toggle-link', function (event) {
	event.preventDefault();
	event.stopPropagation();
    var fr = $(this).attr("for");

	$("#"+fr).slideToggle(600);
	$(".toggle-link[for="+fr+"]").find("i").toggleClass("fa-chevron-circle-right fa-chevron-circle-down");
});

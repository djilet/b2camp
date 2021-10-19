$(document).on('click', '#calendar-tab-link', function(){
	ReloadAllEventLists();
});

$(document).on('change', '#my-event-date', function(){
	LoadMyEventList();
});

$(document).on('click', '#my-event-date-next', function(e){
	currentDate = $('#my-event-date').datepicker('getDate');
	var date = new Date();
	date.setTime(parseInt(Date.parse(currentDate))+(24*60*60*1000));
	$('#my-event-date').datepicker('setDate', date);
	e.preventDefault();
});

$(document).on('click', '#my-event-date-prev', function(e){
	currentDate = $('#my-event-date').datepicker('getDate');
	var date = new Date();
	date.setTime(parseInt(Date.parse(currentDate))-(24*60*60*1000));
	$('#my-event-date').datepicker('setDate', date);
	e.preventDefault();
});

$(document).on('click', '#my-event-add', function(e){
	LoadPopupEditForm("event", "", "ReloadAllEventLists");
	e.preventDefault();
});

$(document).on('click', '.my-event-edit', function(e){
	LoadPopupEditForm("event", $(this).attr('EntityID'), "ReloadAllEventLists", "ReloadAllEventLists");
	e.preventDefault();
});

$(document).on('click', '.my-event-remove', function(e){
	var entity = "event";
	var entityID = $(this).attr("EntityID");
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
			ReloadAllEventLists();
		}
	});
	e.preventDefault();
});

function ReloadAllEventLists(){
	LoadMyEventList();
	ReloadPublicEventList();
}

function LoadMyEventList()
{
	data = new Object();
	var eventDate = $('#my-event-date').val();
	eventDate = RuDateStringToDate(eventDate).yyyymmdd();
	data.FilterEventDate = eventDate;
	data.Entity = "event";
	data.FilterManagerID = USER_ID;
	data.Page = 1;
	data.FullList = true;
	LoadPopupItemList(data, $('#my-event-list'));
}

function ReloadPublicEventList()
{
	$('#calendar').fullCalendar( 'refetchEvents' )
}

function initDatePickers()
{
	$('.manager-date').datepicker({
        language:  'ru',
        format: "dd.mm.yyyy",
        linkFormat: "yyyy-mm-dd",
        //startDate: "01-04-2000",
    });
}

$(document).ready(function(){
	if($('#dashboard').size() != 0)
	{
		LoadMyEventList();
		
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();

		$('#calendar').fullCalendar({
			lang: 'ru',
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek,basicDay'
			},
			editable: false,
			eventLimit: false, // allow "more" link when too many events
			droppable: false,
			eventSources: [
			    PROJECT_PATH+'module/crm/ajax.php?Action=GetCalendarEventListJSON&Entity=event&FilterEventType=public&FullList=1',
			    PROJECT_PATH+'module/crm/ajax.php?Action=GetCalendarEventListJSON&Entity=event&FilterEventType=private&FilterManagerID='+USER_ID+'&FullList=1'
			],
			eventClick: function(calEvent, jsEvent, view) {
				LoadPopupEditForm("event", calEvent.id, "ReloadPublicEventList", "ReloadPublicEventList");
				jsEvent.preventDefault();
		    },
		    timeFormat: 'H:mm',
		    aspectRatio: 1.75,
		});
	}
});

$('document').ready(function() {

	$('input[name="TourPrice"], input[name="TourCount"], input[name="CoursePrice"], input[name="CourseCount"], input[name="Commission"]').change(function(){
		var tourPrice = $('input[name="TourPrice"]').val();
		var tourCount = $('input[name="TourCount"]').val();
		var coursePrice = $('input[name="CoursePrice"]').val();
		var courseCount = $('input[name="CourseCount"]').val();
		var commission = $('input[name="Commission"]').val();

		if (tourPrice == '') tourPrice = 0;
		if (tourCount == '') tourCount = 0;
		if (coursePrice == '') coursePrice = 0;
		if (courseCount == '') courseCount = 0;
		if (commission == '') commission = 0;

		var amount = tourPrice * tourCount + coursePrice * courseCount;
		var percentCommission = commission/100;
		var comissionAmount = Math.round(amount * percentCommission);

		$('input[name="Amount"]').val(amount);
		$('#comission-amount').html('<b>'+comissionAmount+' руб.</b>');
		$('#contract-amount').html('<b>'+amount+' руб.</b>');
	});

	initDatePickers();

	$('.only').change(function(){
		if($(this).is(':checked'))
		{
			$('.only').not(this).prop('checked', false);
		}
		else
		{
			
		}
	});
	$('.only').each(function(){
		if($(this).is(':checked'))
			$(this).trigger('change');
	});

});
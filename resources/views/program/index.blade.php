<link href='{{asset('fullcalendar/fullcalendar.min.css')}}' rel='stylesheet' />
<link href='{{asset('fullcalendar/fullcalendar.print.css')}}' rel='stylesheet' media='print' />
<style>
	#calendar {
		max-width: 100%;
		margin: 10px auto;
		padding: 0 10px;
		background-color: #fff;
	}
	.fc-title{
    font-size: 1.3em;
    font-weight:900;
}
    a.fc-event{
        border-radius:0;
         border: none;
         
     } 
     a.fc-event .fc-bg {
     z-index: 1; 
     background: #fff; 
    opacity: 0;
}

</style>
<script type='text/javascript' src='{{asset('fullcalendar/lib/moment.min.js')}}'></script>
<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.13/moment-timezone.min.js'></script>
<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.13/moment-timezone-with-data.min.js'></script>
<script type='text/javascript' src='{{asset('fullcalendar/fullcalendar.min.js')}}'></script>
<script type='text/javascript' src='{{asset('fullcalendar/locale/zh-tw.js')}}'></script>
<script type='text/javascript'>
    var flag=window.location.pathname.indexOf('cti_program_A1')>-1;
    var now=flag?moment().tz('America/Los_Angeles').format('Y-MM-DD HH:mm:ss'):moment().format('Y-MM-DD HH:mm:ss');
    console.log(now);
    var baseUrl="{{url('/')}}";
    function deleteEvent(id){
        var url=baseUrl+'/{{Request::segment(1)}}/delete/'+id;
        console.log('call '+url);
        $.get(url);
        $('#calendar').fullCalendar( 'refetchEvents' );
    }
	$(function() {
		var header = {
			left : 'prev,next today',
			center : 'title',
			right : 'month,agendaWeek,agendaDay,listMonth'
		};
		$('#calendar').fullCalendar({
		scrollTime:'12:00:00',
		customButtons: {
		addButton: {
    		text: '新增',
    		click: function() {
    		  window.location.href='{{url(Request::segment(1).'/add')}}';
    		}
		},
		copyButton: {
            text: '周複製',
            click: function() {
              window.location.href='{{url(Request::segment(1).'/copy')}}';
            }
        },deleteButton: {
            text: '刪除',
            click: function() {
              window.location.href='{{url(Request::segment(1).'/delete')}}';
            }
        }
		},buttonText: {
            prev: '上週',
            today: '本週',
            next: '下週',
        },
        now:now,
        height:'auto',
        firstDay:1,
		header : {left:'addButton,copyButton', center : 'title',right:'prev,today,next'},
		events: '{{url(Request::segment(1).'/info')}}',
		defaultView:'agendaWeek',
		defaultDate : '{{date('Y-m-d')}}',
		locale : 'zh-tw',
		buttonIcons : false,
		weekNumbers : true,
		navLinks : false,
		editable : false,
		allDaySlot:false,
		eventLimit : false,
		 selectable: false,
		 allDay:true,
		 displayEventTime: true,
		   select: function(start, end, ev) {
                console.log(start);
                console.log(end);
                console.log(ev.data); // resources
            },
            eventClick: function(event) {
                console.log(event);
            },
            eventDrop: function(event, delta, revertFunc) {
                console.log(event);
            },
		 dayClick: function(date, allDay, jsEvent, view) {
		 },
		 slotMinutes :60,
		 aspectRatio: 3,
		 eventBackgroundColor:'#fff',
		 eventTextColor:'#000',
		 eventBorderColor:'#fff',
		 eventClick: function(event) {
    		 console.log(event);
    		 return false;
		 }, eventRender: function(event, eventElement) {
		     console.log(event.start);
		     console.log(eventElement);
		     /*
		     eventElement.find("div.fc-content").css('border-left-width','3px');
		     eventElement.find("div.fc-content").css('border-left-style','solid');
		     eventElement.find("div.fc-content").css('border-left-color',event.bcolor);
		     */
		//    eventElement.css('background-color', '#fff');
		    var color=event.color;
		    var ele=eventElement.find("div.fc-content");
		    var title=eventElement.find("div.fc-content .fc-title");
		    var circle='<i class="fa fa-circle" style="color:'+color+'" aria-hidden="true"></i>';
		  //  title.prepend(circle);
			eventElement.find("div.fc-content").prepend('<a class="delete" href="" onclick="deleteEvent('+event.id+')" style="color:#ff0000;font-size:1.2em;position:absolute;right:1px;top:1px;"><i class="fa fa-times" aria-hidden="true"></i></a>');
		}
	});
	//.fullCalendar( 'refetchEvents' )
	$('.fc-event').on('click', function(event) {
		alert('delete');
	});
	});
</script>
<div id='calendar'></div>

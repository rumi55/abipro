// // request permission on page load
// document.addEventListener('DOMContentLoaded', function () {
//     if (!Notification) {
//       alert('Desktop notifications not available in your browser. Try Chromium.'); 
//       return;
//     }

//     if (Notification.permission !== "granted")
//       Notification.requestPermission();
//   });

//   Number.prototype.number_format = function(n, x) {
//       var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
//       return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
//   };

//   function beep() {
      
//       $("#sound_beep").remove();
//       $('body').append('<audio id="sound_beep" style="display:none" autoplay>'+
//         +'<source src="'+BASE_URL+'/vendor/crudbooster/assets/sound/bell_ring.ogg" type="audio/ogg">'
//         +'<source src="'+BASE_URL+'/vendor/crudbooster/assets/sound/bell_ring.mp3" type="audio/mpeg">'
//       +'Your browser does not support the audio element.</audio>');
//   }

//   function send_notification(text,url) {
//       if (Notification.permission !== "granted")
//       {
//           console.log("Request a permission for Chrome Notification");
//           Notification.requestPermission();
//       }else{
//           var notification = new Notification(APP_NAME+' Notification', {
//           icon:'https://cdn1.iconfinder.com/data/icons/CrystalClear/32x32/actions/agt_announcements.png',
//           body: text,
//           'tag' : text
//           });
//           console.log("Send a notification");
//           beep();

//           notification.onclick = function () {
//             location.href = url;    
//           };
//       }
//   }

//   $(function() {		

//       jQuery.fn.outerHTML = function(s) {
//           return s
//               ? this.before(s).remove()
//               : jQuery("<p>").append(this.eq(0).clone()).html();
//       };



//       $.ajaxSetup({
//           headers: {
//               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//           }
//       });
      
//   });	


var total_notification = 0;
function loader_notification() {       

$.get(BASE_URL+'/notifications',function(resp) {
  var total = resp.data.length;
    // if(total > total_notification) {
    //   send_notification(NOTIFICATION_NEW,NOTIFICATION_INDEX);
    // }

    $('.notifications .notification-count').text(total);
    if(total>0) {
      $('.notifications .notification-count').fadeIn();            
    }else{
      $('.notifications .notification-count').hide();
    }          

    $('.notifications #list-notifications').empty();
    $('.notifications .header').text('You have '+(total==0?'no':total)+' notification'+(total>1?'s':''));
    var htm = '';
    $.each(resp.data,function(i,obj) {
      htm+=`
      <a href="${BASE_URL}/notifications/${obj.id}" class="dropdown-item">
      <div class="media">
              <div class="media-body">
                <p class="text-sm">${obj.message}</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> ${obj.time}</p>
              </div>
            </div>
      </a>
      `
    })  
    $('.notifications #list-notifications').html(htm);
   
    total_notification = total;
})
}
$(function() {
loader_notification();
setInterval(function() {
    loader_notification();
},10000);
});	
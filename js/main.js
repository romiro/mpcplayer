$(function(){
    $.extend({
        Player: {
            updateAll: function(callback) {
                $.Controls.updateWindow(function(){ $.Playlist.update(); });
                if (callback) callback();
            }
        }
    });

  //Notice Message
    $('#Notice').click(function(){ $(this).hide(); });

    $('.floatBoxHeader').prepend('<img src="/img/icons/bullet_toggle_minus.png" class="toggle" />');
    $('.floatBoxHeader .toggle').click(function(){
        var toggleBox = $(this).parent().siblings('.floatBoxContent').toggle();
    });

  //Reposition the target floating box to have a z-index above the rest
    floatBoxPosition = function() { $('.floatBox').css('z-index', 10); $(this).css('z-index', 500); }

  //Init draggability of float boxes
    $('.floatBox').draggable({
        handle: '.floatBoxHeader',
        start: floatBoxPosition,
        stop: function(event, ui) {
            var id = $(ui.helper).attr('id');
            $.post('/saveSetting', {
                'name':         id+'Pos',
                'params[left]': ui.position.left,
                'params[top]':  ui.position.top
            }, function(){

            });
        }
    });

    $('.floatBox').click(floatBoxPosition);

  //"Smart" updater
    function SmartUpdate(){
        var updateWaitTime = 30;
        var now = new Date();
        var timeDiff = {
            controls: (now.getTime() - $.Controls.lastUpdated) / 1000,
            playlist: (now.getTime() - $.Playlist.lastUpdated) / 1000
        }
        if (timeDiff.controls > updateWaitTime && timeDiff.playlist > updateWaitTime) {
            $.Player.updateAll();
        }
        else if (timeDiff.controls > updateWaitTime) {
            $.Controls.updateWindow();
        }
        else if (timeDiff.playlist > updateWaitTime) {
            $.Playlist.update();
        }
    }
    $('body').click(SmartUpdate);
    $(window).keyup(SmartUpdate);
});


function getSeconds(time) {
    var time = time.split(':', 2);
    var mins = Number(time[0]);
    var secs = Number(time[1]);
    return (mins * 60) + secs;
}
function getMinSecs(seconds) {
    var mins = Math.floor(seconds / 60);
    var secs = seconds - (mins*60);
    if (mins < 10) mins = "0"+mins;
    if (secs < 10) secs = "0"+secs;
    return mins+':'+secs;
}
function pr(data) {
    console.log(data);
}


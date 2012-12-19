$(function(){
    $.extend({Controls:{}});

    $.Controls.getStatus = function(onSuccess) {
        if (typeof onSuccess != 'function') {
            var onSuccess = function(data) { $.Controls.statusData = data; }
        }

        $.ajax({
            url:        '/playerStatus',
            type:       'get',
            dataType:   'json',
            success:    onSuccess
        });
    }

    $.Controls.updateWindow = function(callback) {
        $.Controls.getStatus(function(data) {
            var now = new Date();
            $.Controls.lastUpdated = now.getTime();

            $.Controls.statusData = data; //store data for use by the other functions

          //Regardless of player being stopped, do:
            $.Controls.updateVolume();
            $.Controls.updateRepeatStatus();
            $('#PlayStatus').text(data.playStatus);

            if (data.playStatus == 'stopped' || data.playStatus == 'paused') {
                if ($.Controls.seekBarTimer) clearInterval($.Controls.seekBarTimer);
            }

            if (data.playStatus != 'stopped') {
              //If the player isn't stopped, do:
                $.Controls.updateNowPlaying();
                $.Controls.initSeekBar();
            }
            if (callback) callback();
        });
    }

    $.Controls.updateNowPlaying = function() {
        var data = $.Controls.statusData;

        $('#NowPlaying .artist').text(data.currentTrack.artist);
        $('#NowPlaying .title').text(data.currentTrack.title);
        $('#NowPlaying .album').text(data.currentTrack.album);
    }

    $.Controls.updateRepeatStatus = function(status) {
        if (typeof status == 'undefined') {
            var status = $.Controls.statusData.repeat;
        }
        $('#RepeatControl').attr('title', 'Repeat: '+status);
    }

    $.Controls.updateVolume = function() {
        var volume = $.Controls.statusData.volume;
        $('#VolumeLabel').text(volume+'%');
        $('#VolumeSlider').sliderInstance().moveTo(volume);
    }

    $.Controls.initSeekBar = function() {
        var elapsed = getSeconds($.Controls.statusData.timeElapsed);
        var total   = getSeconds($.Controls.statusData.timeTotal);
        var percent = (elapsed / total);
        var SeekBar = $('#SeekBarMeter');
        var maxWidth = SeekBar.attr('maxWidth');
        var totalElem = $('#TrackTime .total');
        var elapsedElem = $('#TrackTime .elapsed');

        SeekBar.css('width', (maxWidth * percent).toFixed() + 'px');

        if ($.Controls.seekBarTimer) clearInterval($.Controls.seekBarTimer);
        totalElem.text(getMinSecs(total));
        elapsedElem.text(getMinSecs(elapsed));

        $.Controls.seekBarTimer = setInterval(function(){
            elapsed++; //add one second to timer
            if (elapsed >= total) {
                $.Controls.updateWindow(function(){ $.Playlist.update(); });
                return;
            }
            percent = (elapsed / total);
            SeekBar.css('width', (maxWidth * percent).toFixed() + 'px');
            elapsedElem.text(getMinSecs(elapsed));
        }, 1000);
    }

    $('#SeekBar').click(function(event){
        var offset = $('#SeekBar').offset();
        var posPercent = ((event.pageX - offset.left) / 2 ).toFixed(); //subtract 5 to make up for #SeekBar's padding
        $.get('/seekPlayback', {percent:posPercent}, function(){
            $.Player.updateAll();
        });
    });

    $('#StopControl').click(function(){
        $.get('/stopPlayback', function(){
            $.Player.updateAll();
        });
    });

    $('#PlayControl').click(function(){
        $.get('/startPlayback', function(){
            $.Player.updateAll();
        });
    });

    $('#PauseControl').click(function(){
        $.get('/pausePlayback', function(){
            $.Player.updateAll();
        });
    });

    $('#PrevControl').click(function(){
        $.get('/previousTrack', function(){
            $.Player.updateAll();
        });
    });

    $('#NextControl').click(function(){
        $.get('/nextTrack', function(){
            $.Player.updateAll();
        });
    });

  //Volume control slider init
    $('#VolumeSlider').slider({
        handle: '#VolumeHandle',
        minValue: 0,
        maxValue: 100,
        startValue: 100,
        steps: 10,
        change: function(event, ui){
            var volume = ui.value;
            $.get('/setVolume', {volume:volume}, function(){
                $.Controls.updateWindow();
            });
        }
    });

    $('#RepeatControl').click(function(){
        $.get('/toggleRepeat', function(result){
            if (result == 'on') {
                $.Controls.updateRepeatStatus('on');
            }
            else if (result == 'off') {
                $.Controls.updateRepeatStatus('off');
            }
        });
    });

    $('#RefreshControl').click(function(){
        $.Controls.updateWindow();
    });

    $.Controls.updateWindow();
});
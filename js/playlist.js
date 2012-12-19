function selectBox(name, className, options, selected) {
    var out = '<select name="'+ name +'" class="'+ className +'">';
    for (i=0; i<options.length; i++) {
        var selectAttr = (options[i] == selected) ? ' selected="selected" ' : '';

        out += '<option value="'+ options[i] +'"'+ selectAttr +'>'+ options[i] +'</option>';
    }
    out += '</select>';
    return out;
}

$(function(){
    $.extend({Playlist:{}});

    $.Playlist.update = function(callback)
    {
        $.ajax({
            url: '/getPlaylist',
            type: 'get',
            dataType: 'html',
            success: function(data) {
                var now = new Date();
                $.Playlist.lastUpdated = now.getTime();

                $('#Playlist table tbody').empty().append(data);

                if ($.Controls.statusData.trackCurrent) {
                    $.Playlist.highlight($.Controls.statusData.trackCurrent);
                }
                $.Playlist.initTrackEvents();
                if (callback) callback();
            }
        });
    }

    $.Playlist.updateOptions = function()
    {
        $.ajax({
            url: '/getPlaylistOptions',
            type: 'get',
            dataType: 'html',
            success: function(data) {
                $('#PlaylistOptions div.select').empty().append(data);
            }
        });
    }

    $.Playlist.initTrackEvents = function()
    {
        $('#Playlist .RemoveFromPlaylist').click(function(){
            var id = $(this).siblings('input.trackID').val();
            $(this).parents('tr').addClass('deleting');
            $.Playlist.removeTrackID(id, function(){ $(this).parents('tr').removeClass('deleting'); });
        });

        $('#Playlist .PlayPlaylistTrack').click(function() {
            var trackID = $(this).parents('tr').find('input.trackID').val();
            $.Playlist.playTrackID(trackID);
        });

      //Track position changer
      //First the user clicks on a td.id element and its contents are replaced by a select box.
      //The click method for the td element is unbound and a new event for the select box is created.
        $('#Playlist td.id').click(function(){
            var trackID = $(this).parents('tr').find('input.trackID').val();
            var ids = new Array();

            for ( i=1; i <= $('#TrackCount').val(); i++ ) ids[i] = i;
            ids.shift();

            $(this).empty().append(selectBox('trackIDs['+ trackID +']', 'trackChange', ids, trackID));
            $(this).unbind('click');
            $(this).find('.trackChange').change(function(){
                $.ajax({
                    url: '/trackChange',
                    type: 'post',
                    data: $(this).serialize(),
                    success: function(){
                        $.Player.updateAll();
                    }
                });
            });
        });
    }

    $.Playlist.highlight = function(id)
    {
        $('#Playlist .trackRow').removeClass('playing');
        $('input.trackID[value='+id+']').parents('tr').addClass('playing');
    }

    $.Playlist.addFiles = function(input)
    {
        if (typeof input == 'string') { //probably a whole-directory add
            ajaxData = { dir: input };
        }
        else { //probably a single file add
            ajaxData = {file: input.val() };
        }
        $.ajax({
            url:    '/addToPlaylist',
            type:   'post',
            data:   ajaxData,
            success: function(){ $.Playlist.update(); }
        });
    }

    $.Playlist.playTrackID = function(id)
    {
        $.get('/playSingle', {trackID:id}, function(){
            $.Controls.updateWindow(function(){ $.Playlist.update(); });
        });
    }

    $.Playlist.removeTrackID = function(id, callback)
    {
        $.get('/removeFromPlaylist', { trackID:id }, function(){
            $.Player.updateAll();
            if (callback) callback();
        });
    }

  //Handles playlist buttons "Append", "Replace", "Delete", "Save", and "Save New"
    $('#PlaylistOptions button').click(function(){
        var action = $(this).val();
        var selected = $('#PlaylistSelect').val();

        if (selected == '' && action != 'saveNew') {
            alert('Select a playlist before continuing');
            return false;
        }
        if (action == 'delete') {
            if (!confirm('Really delete playlist "'+ selected +'"?')) return;
        }
        if (action == 'save') {
            if (!confirm('Overwrite existing playlist "'+ selected +'" with the active playlist?')) return;
        }
        if (action == 'saveNew') {
            var name = prompt('Enter a new playlist name (only alphanumerics and spaces):');
            var regex = new RegExp("[^a-zA-Z0-9 \\-_]");
            while (regex.test(name)) {
                name = prompt('Invalid playlist name. Try again (only alphanumerics and spaces):');
            }
            selected = name;
        }
        $.get('/'+ action +'Playlist', { playlistName: selected }, function(){
            if (action == 'append' || action == 'replace') {
                $.Playlist.update();
            }
            else if (action == 'delete' || action == 'save' || action == 'saveNew') {
                $.Playlist.updateOptions();
            }
        });
    });

  //Add track to playlist by clicking FileList div
    $('#FileList div.file').click(function(){
        var file = $(this).find('input.location').val();
        $(this).addClass('active');

        $.ajax({
            url:    '/addToPlaylist',
            type:   'post',
            data:   { file: file },
            activeFile: $(this),
            success: function(x,y){
                $.Playlist.update();
                this.activeFile.removeClass('active');
            }
        });
    });

    $('#AddDirToPlaylist').click(function(){
        var files = $('#FileList .file .location').serialize();
        $.ajax({
            url:    '/addToPlaylist',
            type:   'post',
            data:   files,
            success: function(){ $.Playlist.update(); }
        });
    });

    $('#AddDirToPlaylistRecursive').click(function(){
        var dir = $('#currentDir').val();
        $.ajax({
            url:    '/addToPlaylist',
            type:   'post',
            data:   { dir: dir },
            success: function(){ $.Playlist.update(); }
        });
    });

    $('#ClearPlaylist').click(function(){
        $.get('/clearPlaylist', function(){ $.Player.updateAll(); });
    });

    $('#ShufflePlaylist').click(function(){
        $.get('/shufflePlaylist', function(){ $.Player.updateAll(); });
    });

    $('#PlaylistOptionsToggle').click(function(){
        var slideHeight = $('#PlaylistOptions').attr('slideHeight');

        if ($('#PlaylistOptions').height() == slideHeight) {
            $('#PlaylistOptions').animate({
                height:"0px",
                bottom:"0px"
            }, { complete: function(){ $(this).hide(); } });
        }
        else {
            $.Playlist.updateOptions();
            $('#PlaylistOptions')
            .css({ width: $('#Playlist').width() })
            .animate({
                height: slideHeight,
                bottom: (Number(slideHeight)+4) * -1
            });
        }
    });



//    $('#SaveNewPlaylist').unbind('click').click(function(){
//
//
//
//        $.get('/saveNewPlaylist', { playlistName: name }, function(){
//            window.location = '';
//        });
//    });

});
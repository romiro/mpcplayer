<?=$this->addJs('playlist')?>
<div id="Playlist" class="floatBox" <?=floatBoxPosition('Playlist')?>>
    <h3 class="floatBoxHeader">Playlist</h3>

    <div class="floatBoxContent">
        <table>
            <thead><tr>
                <th>#</th>
                <th></th>
                <th>Title</th>
                <th>Artist</th>
                <!--<th>Album</th>-->
                <th>Time</th>
                <th></th>
            </tr></thead>
            <tbody></tbody>
        </table>
    </div>


    <div id="PlaylistActions">
        <button id="ClearPlaylist" type="button" class="button" title="Clear Playlist"><?=icon('page_white_cross')?> Clear</button>
        <button id="ShufflePlaylist" type="button" title="Shuffle Playlist" class="button"><img src="/img/icons/page_white_refresh.png" /> Shuffle</button>
        <button id="PlaylistOptionsToggle" type="button" title="Options (Save/Load)" class="button"><img src="/img/icons/page_white_gear.png" /> Options</button>

    </div>


    <div id="PlaylistOptions" style="display:none;height:0;" slideHeight="90">
    <div class="content">

        <div class="select"></div> <?//To be filled by ajax requests?>

        <div class="buttons">
            <button type="button" class="button" id="AppendPlaylist" value="append" title="Append selected to current playlist">Append</button>
            <button type="button" class="button" id="ReplacePlaylist" value="replace" title="Replace current playlist with selected">Replace</button>
            <button type="button" class="button" id="DeletePlaylist" value="delete" title="Delete selected playlist"><?=icon('page_white_delete')?> Delete</button><br />
            <button type="button" class="button" id="SaveAsPlaylist" value="save" title="Save current playlist as selected name"><?=icon('page_white_save')?> Save</button>
            <button type="button" class="button" id="SaveNewPlaylist" value="saveNew" title="Save current playlist with a new name"><?=icon('page_white_savenew')?> Save New...</button>
        </div>

    </div>
    </div>

</div>

<script type="text/javascript">
$(function(){
    $.Playlist.update();
});
</script>
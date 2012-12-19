<select id="PlaylistSelect">
    <option value="">Select a Playlist...</option>
    <?foreach($playlists as $playlist):?>
        <option value="<?=$playlist?>"><?=$playlist?></option>
    <?endforeach?>
</select>
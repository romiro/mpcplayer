
<?
$i=0;
ob_start();
?>
<tr class="hidden"><input id="TrackCount" value="<?=count($playlist)?>" /></tr>
<?foreach($playlist as $key=>$track):?>
    <tr class="trackRow <?=($i % 2 == 0) ? 'even':'odd'?>">
        <td class="id"><?=$track['id']?></td>

            <?/*<select name="trackIDs[<?=$track['id']?>]" class="trackChange">
                <?for($j=1; $j<=count($playlist); $j++):?>
                    <option value="<?=$j?>"<?=$j == $track['id'] ? ' selected="selected"':''?>><?=$j?></option>
                <?endfor?>
            </select>*/?>

        <td class="action"><img class="PlayPlaylistTrack" src="/img/icons/play.png" /></td>
        <td class="title"><?=$track['title']?></td>
        <td class="artist"><?=$track['artist']?></td>
        <td class="time"><?=$track['time']?></td>
        <td class="action">
            <input type="hidden" class="trackID" value="<?=$track['id']?>" />
            <img class="RemoveFromPlaylist" src="/img/icons/delete.png" />
        </td>
    </tr>
    <?$i++?>
<?
endforeach;
$response = ob_get_clean();
$response = preg_replace('/    |\\n/', '', $response);
echo $response;

?>
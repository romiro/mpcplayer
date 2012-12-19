<div id="FileList" class="floatBox" <?=floatBoxPosition('FileList')?>>

    <h3 class="floatBoxHeader">Music Browser</h3>

    <div class="floatBoxContent">
        <?foreach($directories as $label=>$dir):?>
            <div class="directory item">
                <a href="?dir=<?=urlencode($dir)?>"><img src="/img/icons/folder<?=$label == 'up' ? '_up' : ''?>.png" />
                    <?=$label == 'up' ? '':$label ?>
                </a>
            </div>
        <?endforeach?>


        <?foreach($files as $file):?>
            <div class="file item">
                <input type="hidden" class="location" value="<?="$currentDir/$file"?>" name="files[]" />
                <img src="/img/icons/mp3.png" />
                <span class="fileName"><?=$file?></span>
                <img class="AddFileToPlaylist button" src="/img/icons/add.png" />
            </div>
        <?endforeach?>

    </div>

    <div id="FileListActions">

        <?if(!empty($files)):?><a href="javascript:void(0)" id="AddDirToPlaylist">Add all visible MP3s</a><br /><?endif?>
        <?if(!empty($directories)):?><a href="javascript:void(0)" id="AddDirToPlaylistRecursive">Add all visible MP3s and subdirectories</a><?endif?>

        <input type="hidden" id="currentDir" value="<?=$currentDir?>" />
    </div>
</div>


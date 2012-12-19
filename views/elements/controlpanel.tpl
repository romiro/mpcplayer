<?$this->addJs('controlpanel')?>
<div id="ControlPanel" class="floatBox" <?=floatBoxPosition('ControlPanel')?>>

    <h3 class="floatBoxHeader">Controls</h3>

    <div class="floatBoxContent">


        <div id="NowPlaying">

            <div class="currentTrack">
                <label>Artist:</label>
                <span class="artist" title="Artist"></span>
            </div>

            <div class="currentTrack">
                <label>Title:</label>
                <span class="title currentTrack" title="Title"></span>
            </div>

            <div class="currentTrack">
                <label>Album:</label>
                <span class="album currentTrack" title="Album"></span>
            </div>

        </div>

        <div id="SeekBar">
            <div id="TrackTime">
                <span class="elapsed"></span> <span class="sep">/</span> <span class="total"></span>
            </div>
            <div id="SeekBarMeter" maxWidth="200"></div>
        </div>

        <div id="ControlButtons">
            <img src="/img/icons/control_play_blue.png" id="PlayControl" title="Play" />
            <img src="/img/icons/control_pause_blue.png" id="PauseControl" title="Pause" />
            <img src="/img/icons/control_stop_blue.png" id="StopControl" title="Stop" />
            <img src="/img/icons/control_start_blue.png" id="PrevControl" title="Previous" />
            <img src="/img/icons/control_end_blue.png" id="NextControl" title="Next" />
            <img src="/img/icons/control_repeat_blue.png" id="RepeatControl" title="Repeat" />
        </div>

        <div id="PlayStatus"></div>

        <div id="Volume">
            <div class="icon"><?=icon('sound')?></div>

            <div id="VolumeBar" title="Volume">
                <div id="VolumeSlider">
                    <div id="VolumeHandle"></div>
                </div>
            </div>
            <div id="VolumeLabel"></div>
        </div>

    </div>
</div>


<?php
class mpc
{
    public static function ls($dir = '')
    {
        if (empty($dir)) {
            $listing = self::run('ls');
        }
        else {
            $dir = self::escapePath($dir);
            $listing = self::run("ls $dir");
        }

        $listing = self::outputToArray($listing);
        return $listing;
    }

    public static function play($id = '')
    {
        return self::run("play $id");
    }

    public static function stop()
    {
        return self::run('stop');
    }

    public static function pause()
    {
        return self::run('toggle');
    }

    public static function seek($percent)
    {
        return self::run("seek $percent%");
    }

    public static function next()
    {
        return self::run('next');
    }

    public static function previous()
    {
        return self::run('prev');
    }

    public static function move($oldID, $newID)
    {
        return self::run("move $oldID $newID");
    }

    public static function volume($percent)
    {
        if ($percent < 1) $percent *= 100;
        return self::run("volume $percent");
    }

    public static function repeat($setting = '')
    {
        if ($setting === true) {
            $setting = 'on';
        }
        elseif ($setting === false) {
            $setting = 'off';
        }
        else {
            $setting = '';
        }
        $output = self::run("repeat $setting");
        preg_match('/repeat: (.*?) /', $output, $matches);
        if (!empty($matches[1])) {
            return $matches[1];
        }
        return false;
    }

    public static function shuffle()
    {
        return self::run('shuffle');
    }

    public static function addToPlaylist(Array $files, $currentDir)
    {
        foreach($files as $key=>$value) {
            if (substr($value, 0, 1) == '/') { //remove starting fslash
                $value = substr($value, 1);
            }
            $files[$key] = "'$value'";
        }
        $fileString = implode(" ", $files);

        $output = self::run("ls $currentDir");

        if (strpos($output, 'adding:') === false) {
            return false;
        }
        else {
            return true;
        }
    }

    public static function addFileToPlaylist($file)
    {
        if (substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }
        $output = self::run("add '$file'");
        if (strpos($output, 'adding:') === false) {
            return false;
        }
        return true;
    }

    public static function addFilesToPlaylist($files)
    {
        foreach($files as $key=>$file) {
            if (substr($file, 0, 1) == '/') {
                $file = substr($file, 1);
            }
            $file = escapeshellarg($file);
            $files[$key] = $file;
        }
        $files = implode(' ', $files);
        $output = self::run("add $files");

        if (strpos($output, 'adding:') === false) {
            return false;
        }
        return true;
    }

    public static function addDirectoryToPlaylist($dir)
    {
        if (substr($dir, 0, 1) == '/') {
            $dir = substr($dir, 1);
        }
        $output = self::run("ls '$dir' | mpc add");
        if (strpos($output, 'adding:') === false) {
            return false;
        }
        return true;
    }

    public static function removeFromPlaylist($id)
    {
        $output = self::run("del $id");
        if (strpos($output, 'volume') !== false) {
            return false;
        }
        else {
            return true;
        }
    }

    public static function clearPlaylist()
    {
        return self::run('clear');
    }

    public static function getAllPlaylists()
    {
        return self::outputToArray(self::run("lsplaylists"));
    }

    public static function savePlaylist($playlistName)
    {
        $output = self::run("save $playlistName");
        return true;
    }

    public static function loadPlaylist($playlistName)
    {
        $output = self::run("load $playlistName");
        return true;
    }

    public static function deletePlaylist($playlistName)
    {
        $output = self::run("rm $playlistName");
    }

    public static function getActivePlaylistTracks()
    {
        $command = "--format ".self::getFormat()." playlist";
        $output = self::run($command);
        $playlist = self::outputToArray($output);

        foreach($playlist as $key=>$track)
        {
            $id    = substr($track, 1, strpos($track, ')')-1);
            $track = substr($track, strpos($track, ") ")+2 );
            $track = json_decode($track,true);

            $track['id'] = $id;
            self::cleanTitle($track['title']);
            $playlist[$key] = $track;
        }
        return $playlist;
    }

    public static function getStatus()
    {
        $output = self::run("--format ".self::getFormat());
        $output = self::outputToArray($output);

        if (count($output) > 1) {
          //grab the current track status
            $status['currentTrack'] = json_decode($output[0], true);
            self::cleanTitle($status['currentTrack']['title']);
            array_shift($output); //remove it from the original output

          //get the second line of output
            $success = preg_match('/^\\[(.*?)\\]\\s*?#(\\d*?)\/(\\d*?)\\s*?([\\d:]*?)\/([\\d:]*?) \\(([\\d%]*)\\)/', $output[0], $matches);
            if ($success === false) return false;

            $status['playStatus']       = $matches[1];
            $status['trackCurrent']     = $matches[2];
            $status['trackCount']       = $matches[3];
            $status['timeElapsed']      = $matches[4];
            $status['timeTotal']        = $matches[5];
            $status['percent']          = $matches[6];
            array_shift($output); //remove second line from output
            unset($matches, $success);
        }
        else {
            $status['playStatus']       = 'stopped';
        }
      //third and final line of output
        $success = preg_match('/volume:\\s*([\\d]*?)%\\s*repeat: (\\w*)\\s*random: (\\w*)$/', $output[0], $matches);
        if ($success === false) return false;

        $status['volume']   = $matches[1];
        $status['repeat']   = $matches[2];
        $status['random']   = $matches[3];
        return $status;
    }

    public static function update()
    {
        $out = shell_exec('mpd --create-db');
        $out .= self::run('update');
        return $out;
    }

    /*
        Private members
    */

    /**
     * Passes commands to MPC and returns the output, if any
     *
     * @param string $arguments
     * @return string
     */
    private function run($arguments = '')
    {
        $command = "mpc $arguments";
        if (isset($_GET['debug'])) {
            echo $command;
        }
        $output = shell_exec($command);
        return $output;
    }

    /**
     * Converts a string into an array by splitting by linebreaks, then removes
     * empty array elements.
     *
     * @param string $string
     * @return array
     */
    private function outputToArray($string)
    {
        $array = explode(chr(10), $string);
        foreach($array as $key=>$val) {
            if (empty($val)) unset($array[$key]);
        }
        return $array;
    }

    /**
     * Escapes file path strings for mpc
     *
     * @param string $path
     * @return string
     */
    private function escapePath($path)
    {
        //$path = preg_replace('/([ &()])/', '\\\\$1', $path);
        if (substr($path,0,1) == "/") {
            $path = substr($path, 1); //trim leading backslash since mpc doesn't like it
        }
        $path = escapeshellarg($path);
        return $path;
    }

    /**
     * Wrapper to return the "--format" parameter of an mpc command.
     * Used when listing playlist songs and getting Now Playing status
     *
     * @return string
     */
    private function getFormat()
    {
        $json = array('artist'  =>'%artist%',
                      'album'   =>'%album%',
                      'title'   =>'[%title%|%file%]',
                      'track'   =>'%track%',
                      'time'    =>'%time%');

        $format = "'".json_encode($json)."'";
        return $format;
    }

    /**
     * Wrapper to trim off the directory and extension of track title's which display as file names
     *
     * @param string $title
     * @return string
     */
    private function cleanTitle(&$title)
    {
        if (strpos($title, '/') !== false) {
            $title = substr($title, strrpos($title, '/')+1);
            if (substr($title, -4) == '.mp3') {
                $title = substr($title, 0, -4);
            }
        }
        return $title;
    }
}
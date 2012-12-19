<?php
class Controller
{
    /**
     * Application configuration vars
     */
    public $programDir       = '';
    public $programFile      = 'mpc';
    public $musicDir         = '/tcmusic';

    public $dbDSN   = 'mysql';
    public $dbHost  = 'localhost';
    public $dbName  = 'music';
    public $dbUser  = 'web';
    public $dbPass  = 'public';

    /**
     * mpc object
     *
     * @var mpc
     */
    public $mpc;

    public $vars = array();

    /**
     * Name of view, located in /views directory.  set to default view.
     *
     * @var View
     */
    public $View;

    /**
     * Starts up the mpc interface object and element controller (the view's elements' logic dealie)
     *
     * @return void
     */
    public function __construct()
    {
        $this->mpc = new mpc();
    }

    public function index()
    {
        $this->setView(); //The elements take care of the bulk of the logic
    }

    public function admin()
    {
        $this->setView('admin');
    }

    public function getPlaylist()
    {
        $this->setAjax('ajax/playlistUpdate');
        $playlist = $this->mpc->getActivePlaylistTracks();
        $this->vars['playlist'] = $playlist;
        //Look in views/ajax/playlistUpdate.tpl for the rest
    }

    public function getPlaylistOptions()
    {
        $this->setAjax('ajax/playlistOptions');
        $playlists = mpc::getAllPlaylists();
        foreach($playlists as $key=>$value) {
            $playlists[$key] = str_replace('_', ' ', $value);
        }
        $this->vars['playlists'] = $playlists;
    }

    public function startPlayback()
    {
        $this->setAjax();
        $this->mpc->play();
    }

    public function stopPlayback()
    {
        $this->setAjax();
        $this->mpc->stop();
    }

    public function pausePlayback()
    {
        $this->setAjax();
        $this->mpc->pause();
    }

    public function seekPlayback()
    {
        $this->setAjax();
        $percent = $_GET['percent'];
        mpc::seek($percent);
    }

    public function nextTrack()
    {
        $this->setAjax();
        $this->mpc->next();
    }

    public function previousTrack()
    {
        $this->setAjax();
        $this->mpc->previous();
    }

    public function playSingle()
    {
        $trackID = $_GET['trackID'];
        mpc::play($trackID);
    }

    public function setVolume()
    {
        $this->setAjax();
        $volume = preg_replace('[^0-9]', '', $_GET['volume']);
        $this->mpc->volume($volume);
    }

    public function toggleRepeat()
    {
        $this->setAjax();
        $output = $this->mpc->repeat();
        echo $output;
    }

    public function playerStatus()
    {
        $this->setAjax();
        $status = $this->mpc->getStatus();
        if ($status === false) $this->ajaxError();
        echo json_encode($status);
    }

    public function addToPlaylist()
    {
        $this->setAjax();

        if (isset($_POST['file']))
        {
            $file = $_POST['file'];
            $output = $this->mpc->addFileToPlaylist($file);
        }
        elseif (isset($_POST['files']))
        {
            $output = $this->mpc->addFilesToPlaylist($_POST['files']);
        }
        elseif (isset($_POST['dir']))
        {
            $dir = $_POST['dir'];
            $ouput = $this->mpc->addDirectoryToPlaylist($dir);
        }

        if ($output === false) {
            $this->ajaxError();
        }
    }

    public function removeFromPlaylist()
    {
        $this->setAjax();
        $trackID = $_GET['trackID'];
        if ( !$this->mpc->removeFromPlaylist($trackID) ) {
            $this->ajaxError();
        }
    }

    public function trackChange()
    {
        $this->setAjax();
        $oldID = key($_POST['trackIDs']);
        $newID = $_POST['trackIDs'][$oldID];
        mpc::move($oldID, $newID);
    }

    public function clearPlaylist()
    {
        $this->setAjax();
        $output = $this->mpc->clearPlaylist();
    }

    public function appendPlaylist()
    {
        $this->setAjax();
        $playlistName = str_replace(' ', '_', $_GET['playlistName']);
        $this->mpc->loadPlaylist($playlistName);
    }

    public function replacePlaylist()
    {
        $this->setAjax();
        $playlistName = str_replace(' ', '_', $_GET['playlistName']);
        $this->mpc->clearPlaylist();
        $this->mpc->loadPlaylist($playlistName);
    }

    public function deletePlaylist()
    {
        $this->setAjax();
        $playlistName = str_replace(' ', '_', $_GET['playlistName']);
        $this->mpc->deletePlaylist($playlistName);
        $this->notice("Deleted playlist <b>$playlistName</b>");
    }

    public function saveNewPlaylist()
    {
        $this->setAjax();
        $playlistName = $_GET['playlistName'];
        if (preg_match('[^a-zA-Z0-9 -_]', $playlistName)) {
            $this->notice("Playlist NOT saved. Please use only alphanumeric characters and/or spaces.");
        }
        //$playlistName = str_replace('[^a-zA-Z0-9 -_]', '', $playlistName); //only alphanumeric, spaces, -, and _
        $_playlistName = str_replace(' ', '_', $playlistName);

        if (!$this->mpc->savePlaylist($_playlistName)) {
            $this->ajaxError();
        }
    }

    public function savePlaylist()
    {
        $this->setAjax();
        $playlistName = str_replace(' ', '_', $_GET['playlistName']);
        $this->mpc->deletePlaylist($playlistName);
        if (!$this->mpc->savePlaylist($playlistName)) {
            $this->ajaxError();
        }
    }

    public function shufflePlaylist()
    {
        $this->setAjax();
        $this->mpc->shuffle();
    }

    public function updateDatabase()
    {
        $this->setView('admin');
        mpc::update();
        $this->notice("Updating databases...may take awhile", '/admin');
    }

    public function saveSetting()
    {
        $this->setAjax();
        $name = $_POST['name'];
        $params = $_POST['params'];
        foreach($params as $key=>$value) {
            $_SESSION[$name][$key] = $value;
        }
    }

    public function resetPositions()
    {
        $this->setAjax();
        unset($_SESSION['ControlPanelPos'], $_SESSION['PlaylistPos'], $_SESSION['FileListPos']);
        $this->notice('Positions reset');
    }

    private function setView($view = null, $layout = null)
    {
        if ($view == null) {
            $view = "index";
        }
        if ($layout == null) {
            $layout = "default";
        }
        $this->View->viewFile = $view;
        $this->View->layoutFile = $layout;
    }

    private function setAjax($viewFile = 'blank')
    {
        $this->View->viewFile = $viewFile;
        $this->View->layoutFile = 'ajax';
    }

    private function ajaxError($exit = true)
    {
        header('HTTP/1.x 404 Not Found');
        if ($exit === true) {
            exit('Bad Ajax Request');
        }
    }

    public function notice($text, $redirect = true, $exit = true)
    {
        $_SESSION['noticeMessage'] = $text;
        if ($redirect === true) {
            $url = $_SERVER['HTTP_REFERER'];
        }
        elseif (is_string($redirect)) {
            $url = $redirect;
        }
        if ($redirect !== false) {
            header("Location:$url");
        }
        if ($exit === true) {
            exit();
        }
    }

    public function setAmazonArt($artist = 'led zeppelin', $album = 'Early Days - The Best of Led Z')
    {
        $this->setAjax();

        if (empty($artist) && empty($album))
        {
            $image = '';
            $prep->execute();
            return;
        }

        $url = "http://ecs.amazonaws.com/onca/xml";
        $request = array(
            'Service'        => 'AWSECommerceService',
            'AWSAccessKeyId' => '0ZC8CXPB5MQVNRQYYNG2',
            'Version'        => '2006-09-11',
            'Operation'      => 'ItemSearch',
            'ResponseGroup'  => 'Images',
            'SearchIndex'    => 'Music',
            'Keywords'       => "$artist $album"
        );
        $querystring = http_build_query($request);
        $output    = file_get_contents($url . "?" . $querystring);
        $xml       = new SimpleXMLElement($output);
        $image_url = $xml->Items->Item[0]->MediumImage->URL;

        if (empty($image_url))
        {
            $request['Keywords'] = $album;
            $querystring = http_build_query($request);
            $output    = file_get_contents($url . "?" . $querystring);
            $xml       = new SimpleXMLElement($output);
            pr($xml);
            $image_url = $xml->Items->Item[0]->MediumImage->URL;
        }
        $image     = file_get_contents($image_url);

        //was a pdo::execute() on bound params
    }
}
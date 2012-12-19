<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
    <title>mpc Remote MP3 Player</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="/js/jquery.js"></script>
    <script type="text/javascript" src="/js/jquery/jquery.dimensions.js"></script>
    <script type="text/javascript" src="/js/jquery/ui.mouse.js"></script>
    <script type="text/javascript" src="/js/jquery/ui.draggable.js"></script>
    <script type="text/javascript" src="/js/jquery/ui.slider.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>
    <?=$this->layoutScripts()?>
    <link rel="stylesheet" href="/css/main.css" />
    <link rel="stylesheet" href="/js/jquery/themes/flora/flora.all.css" />
</head>

<body>
    <?if(isset($_SESSION['noticeMessage'])):?>
        <div id="Notice">
            <span class="text"><?=$_SESSION['noticeMessage']?></span>
        </div>
        <?unset($_SESSION['noticeMessage'])?>
    <?endif?>

    <div id="ResetPositions"><a href="/resetPositions">reset positions</a></div>

    <div id="container">
        <?=$content?>
    </div>

    <div style="clear:both"></div>
    <div id="footer">
        [ <a href="/">home</a> | <a href="/admin">admin</a> ]<br />

        mpc Remote MP3 Player<br />
        &copy;2007-2008 Robert Rogers<br />
        <a href="http://romiro.com"><img src="/img/romiro.png" /></a>
    </div>

</body>
</html>
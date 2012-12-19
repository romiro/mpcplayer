<?php
/**
 * FOOOONCTIONS
 *
 * @author rrogers
 * @package defaultPackage
 */

function __autoload($c)
{
    require_once(BASEDIR . "/lib/classes/$c.php");
}

/**
 * Debugging shortcut function for print_r that surrounds array output in <pre> tags
 *
 * @param mixed $var
 */
function pr($var)
{
    echo "<pre>";
    print_r($var);
    echo "</pre>";
}

/**
 * most complicated function ever
 *
 * @param string $url
 * @param boolean $exit
 */
function redirect($url, $exit = true)
{
    header("Location:$url");
    if ($exit === true) exit();
}

/**
 * Takes a 1D array and turns it into option elements where value=key and the option's text=value
 *
 * @param array $options
 * @return string
 */
function optionElements(array $options)
{
    $options = array(''=>'') + $options;
    $out = "\n";
    foreach($options as $key=>$value)
    {
        $out .= "<option value='$key'>$value</option>\n";
    }
    return $out;
}

function floatBoxPosition($type)
{
    $setting = $_SESSION[$type.'Pos'];
    if (empty($setting)) {
        return '';
    }
    return "style='position:absolute; left:{$setting['left']}px; top:{$setting['top']}px'";
}
function icon($name) {
    return '<img src="/img/icons/'. $name .'.png" />';
}
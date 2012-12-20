<?php
#
# PHP - Personal Home Page system
# Copyright 2012: Andrew Rump (andrew-nospam@rump.dk)
#
# History
# 0.1 12-11-12 Created
# 0.2 13-11-12 Implemented access control ground work
# 0.3 14-11-12
# 0.4 15-11-12
# 0.5 16-11-12 Centered Gallery and footer and fiexed menu code
# 0.6 17-11-12 Rewrote menu code
# 0.7 19-11-12 Implemented one level menu code hack
# 0.8 21-11-12 Renamed the project to PHP - Personal Home Page system :-D
# 0.9 02-12-12
# 1.0 02-12-12
# 1.1 04-12-12 Fixed a bug (by removing a comma in a Gallery conf)
# 1.2 05-12-12 
# 1.3 05-12-12 Implemented LOCAL_GALLERY
# 1.4
# 1.5 17-12-12 Implemented RECUSIVE_GALLERY
#
# Include this file an you have a simple but full blown website with:
# * Automagic menu
# * Automagic user control
# * Automagic gallery
# * CSS support
# * Easy HTML code generation
#
# BUGS:
# 12-11-12 *Menu code not working
# 19-11-12 +Files in the top folder are not made submenu to the top menu?!?
# 24-11-12 +Using the wrong charset in <HEAD>
# 14-12-12 RANDOM_GALLERY only randomize each directory and not all images
# TODO:
# 15-11-12 +Use htmlspecialchars() in HREF() and escape possible
# 15-11-12 +Cleanup $dirname usage
# 17-11-12 +Implement general global name, i.e., Weber Design (but not on home page)
# 17-11-12 +Implement image upload and image.alt edit
# 17-11-12 +Implement Form incl. spam
# 17-11-12 +Implement mail submit incl. spam
# 22-11-12 -Blog
# 22-11-12 -Mailingliste 
# 22-11-12 -Automagic 404 reporting
# 26-11-12 *Mindre bred tekst
# 26-11-12 +Baggrundsbillede
# 26-11-12 *Copyright med mere hele ned i bunden
# DONE:
# 13-11-12 -Remove newline from title from control file
# 12-11-12 +Center footer and gallery
# 15-11-12 +Put some code above gallery, e.g., header
# 13-11-12 *Implement access control
# 17-11-12 *Create random image playlist
# 18-11-12 *404.php not working
# 24-11-12 +Add favicon.ico
# <link rel="shortcut icon" href="http://yourdomain.com/favicon.ico" type="image/vnd.microsoft.icon">
# NONE:
# 17-11-12 -Find images recursively
#

#<FORM method="post" action="http://www.dit-domæne.dk/cgi-bin/FormMail.pl">
#<input type="hidden" name="recipient" value="mail@dit-domæne.dk">
#<input type="hidden" name="subject" value="Her kan du skrive en emne-tekst">
#<input type="hidden" name="redirect" value="http://www.dit-domæne.dk/nyside.html">

#Navn:<INPUT TYPE="TEXT" VALUE="" NAME="Navn" SIZE="20">
#Efternavn:<INPUT TYPE="TEXT" VALUE="" NAME="Efternavn" SIZE="20">
#Mail:<INPUT TYPE="TEXT" VALUE="" NAME="Mail" SIZE="20">
#Kommentar:<TEXTAREA name="Kommentar" COLS="40" ROWS="7"> </TEXTAREA>

#<INPUT TYPE="Reset" VALUE="Nulstil"><INPUT TYPE="Submit" VALUE="Send">
#</form>

$error_level = $_REQUEST["error_level"]; # BUG sanitize

#error_reporting(~0); # -1

#############################################################################################
#
# Default values

define('DEFAULT_LEVEL', 1);
define('DEFAULT_PAGE', 'index.php');
define('DEFAULT_TEST', 'index1.php');
define('IMG_ALT_FILE', 'images.alt');

#############################################################################################
#
# HTML helper functions

function fixtags($text) {
  $text = htmlspecialchars($text);
  $text = preg_replace("/=/", "=\"\"", $text);
  $text = preg_replace("/&quot;/", "&quot;\"", $text);
  $tags = "/&lt;(\/|)(\w*)(\ |)(\w*)([\\\=]*)(?|(\")\"&quot;\"|)(?|(.*)?&quot;(\")|)([\ ]?)(\/|)&gt;/i";
  $replacement = "<$1$2$3$4$5$6$7$8$9$10>";
  $text = preg_replace($tags, $replacement, $text);
  $text = preg_replace("/=\"\"/", "=", $text);
  return $text;
}

#########################

function expand($HTML, $contentvalue = NULL, $newline = true, $attributes = NULL)
{
  if (is_null($contentvalue) or is_numeric($contentvalue))
    if (!is_null($contentvalue) and $contentvalue)
      if (is_null($attributes))
        return "<" . $HTML . ">" . ($newline ? "\n" : "");
      else
        return "<" . $HTML . " " . $attributes . ">" . ($newline ? "\n" : "");
    else
      return "</" . $HTML . ">" . ($newline ? "\n" : "");
  else
    if (is_null($attributes))
      return "<" . $HTML . ">" . ($newline ? "\n" : "") . $contentvalue .
             "</" . $HTML . ">" . ($newline ? "\n" : "");
    else
      return "<" . $HTML . " " . $attributes . ">" . ($newline ? "\n" : "") . $contentvalue .
             "</" . $HTML . ">" . ($newline ? "\n" : "");
}

#############################################################################################

function DIV($id = NULL, $contentvalue = NULL, $newline = true)
{
  if (is_null($id) or is_null($contentvalue) or is_numeric($contentvalue))
    if (!is_null($id) and (is_null($contentvalue) or is_numeric($contentvalue) and $contentvalue))
      return "<DIV ID='" . $id. "'>" . ($newline ? "\n" : "");
    else
      return "</DIV>" . ($newline ? "\n" : "");
  else
    return "<DIV ID='" . $id. "'>\n" . $contentvalue . "</DIV>" . ($newline ? "\n" : "");
}

function H($level, $header)
{
  return "<H" . $level . ">" . $header . "</H" . $level . ">\n";
}

function P($paragraph)
{
  return "<P>" . $paragraph . "</P>\n";
}

function B($paragraph, $newline = false)
{
  return "<B>" . $paragraph . "</B>" . ($newline ? "\n" : "");
}

function EM($paragraph, $newline = false)
{
  return "<EM>" . $paragraph . "</EM>" . ($newline ? "\n" : "");
}

function BLOCKQUOTE($contentvalue = NULL)
{ # cite="http"
  return expand("BLOCKQUOTE", $contentvalue);
}

function HR()
{
  return "<HR>\n";
}

function BR()
{
  return "<BR>\n";
}

function HREF($text, $link, $target = NULL, $newline = true, $active = true)
{
  if ($active)
    if (!is_null($target) and strlen($target) > 0)
      return "<A HREF=\"" . $link . "\" TARGET=\"" . $target . "\">" . 
             $text . "</A>" . ($newline ? "\n" : "");
    else
      if (strncmp($link, "http", 4) == 0 or strlen($_SERVER["QUERY_STRING"]) == 0)
        return "<A HREF=\"" . $link . "\">" . $text . "</A>" .
               ($newline ? "\n" : "");
      else
        return "<A HREF=\"" . $link . "?" . $_SERVER["QUERY_STRING"] . "\">" .
                $text . "</A>" . ($newline ? "\n" : "");
  else
    return $text . ($newline ? "\n" : "");
}

function ADDRESS($contentvalue = NULL)
{
  return expand("ADDRESS", $contentvalue);
}

function UL($contentvalue = NULL)
{
  return expand("UL", $contentvalue);
}

function LI($contentvalue = NULL, $id = NULL, $class = NULL, $newline = true)
{
  $LI = "<LI". ($id ? " ID='" . $id . "'" : "") .
        ($class ? " CLASS='" . $class . "'" : "") . ">" . ($newline ? "\n" : "");
  if (is_null($contentvalue) or is_numeric($contentvalue))
    if (!is_null($contentvalue) and $contentvalue)
      return $LI;
    else
      return "</LI>" . ($newline ? "\n" : "");
  else
    return $LI . $contentvalue . "</LI>" . ($newline ? "\n" : "");
}

function IMG($path, $alternate = NULL, $newline = true)
{
  if (is_null($alternate))
    return "<IMG SRC='" . $path . "'>" . ($newline ? "\n" : "");
  else
    return "<IMG SRC='" . $path . "' ALT='" . htmlspecialchars($alternate) . "'>" .
           ($newline ? "\n" : "");
}

function SPAN($contentvalue, $newline = true, $attributes = NULL)
{
  return expand("SPAN", $contentvalue, $newline, $attributes);
}

function TABLE($contentvalue)
{
  return expand("TABLE", $contentvalue);
}

function TR($contentvalue)
{
  return expand("TR", $contentvalue);
}

function TH($contentvalue, $return = true)
{
  return expand("TH", $contentvalue, $newline);
}

function TD($contentvalue, $return = true)
{
  return expand("TD", $contentvalue, $newline);
}

#############################################################################################
#
# Check if the scriptfile is a file having the extension .php. If so get the first line
# from the file and extract the: order number, access control and header from the file.
# The access control has the following access levels:
# ! = invisible (e.g., 404), * = everyone, + = 2, - = 3, ...

function cms_control($scriptfilename, $access)
{
  #define(TITLE, "<?php #");
  $header = array(-1, "X", htmlspecialchars("(Undefined)"), -1);

  if (is_file($scriptfilename)) {
    $parts = explode(".", $scriptfilename);
    if (is_array($parts) && count($parts) > 1) {
      $ext = strtolower(end($parts));
      if (strcmp($ext, "php") == 0) {
        $handle = fopen($scriptfilename, "r");
        $control = fgets($handle);
        fclose($handle);
        if (ereg("<\?php +# +([0-9]+)([\!\*\+-])(.*)", $control, $regs)) {
          $level = strpos('!*+-', $regs[2]);
          if ($access >= $level) {
            $header = array($regs[1], $regs[2], htmlspecialchars(rtrim($regs[3])), $level);
          }
        }
      }
    }
  }
  return $header;
}

#############################################################################################
#
# http://cssmenumaker.com
#

#<div id='cssmenu'>
#<ul>
#   <li class='active '><a href='index.html'><span>Home</span></a></li>
#   <li class='has-sub '><a href='#'><span>Products</span></a>
#      <ul>
#         <li><a href='#'><span>Product 1</span></a></li>
#         <li><a href='#'><span>Product 2</span></a></li>
#      </ul>
#   </li>
#   <li><a href='#'><span>About</span></a></li>
#   <li><a href='#'><span>Contact</span></a></li>
#</ul>
#</div>

function create_menu($docroot, $scriptname, $access)
{
  if ($hDir = opendir($docroot)) {
    while (($entry = readdir($hDir)) !== false) {
      if (is_dir($docroot . '/' . $entry)) {
        if ($entry[0] != '.') {
          $control = cms_control($docroot . '/' . $entry . '/' . DEFAULT_PAGE, $access);
          if ($control[3] >= DEFAULT_LEVEL) {
            if ($hFiles = opendir($docroot . '/' . $entry)) {
              $files = NULL;
              while (($fil = readdir($hFiles)) !== false) {
                if (is_file($docroot . '/' . $entry . '/' . $fil)) {
                  $ctrl = cms_control($docroot . '/' . $entry . '/' . $fil, $access);
                  if (strcmp($fil, DEFAULT_PAGE) != 0 and strcmp($fil, DEFAULT_TEST) != 0)
                    if ($ctrl[3] >= DEFAULT_LEVEL) {
                      $files[$ctrl[0]] = HREF(SPAN($ctrl[2], false), '/' . $entry . '/' . $fil, NULL,
                                              false);
                    }
                }
              }
              closedir($hFiles);
            }
            $folder[$control[0]] = array(HREF(SPAN($control[2], false), '/' . $entry, NULL, false),
                                         $files);
          }
        }
      } else {
        if (is_file($docroot . '/' . $entry)) {
          $control = cms_control($docroot . '/' . $entry, $access);
          if (strcmp($entry, DEFAULT_PAGE) == 0 or strcmp($entry, DEFAULT_TEST) == 0)
            $entry = DEFAULT_PAGE;
          if ($control[3] >= DEFAULT_LEVEL)
            $file[$control[0]] = HREF(SPAN($control[2], false), '/' . $entry, NULL, false);
        }
      }
    }
    closedir($hDir);
  }
  $html = '';

  if (!is_null($folder))
  {
    ksort($folder);
    foreach ($folder as $X) {
      if (!is_null($X))
        ksort($X);
    }
  }
  if (!is_null($file))
    ksort($file);

  if (empty($folder))
    $menu = NULL;
  else
    $menu = reset($folder);
  if (empty($file))
    $item = NULL;
  else
    $item = reset($file);

  while (!is_null($menu) or !is_null($item)) {
    if (is_null($menu))
      $menu_pos = key($file) + 1; # One above, i.e., take the other one
    else
      $menu_pos = key($folder);
    if (is_null($item))
      $item_pos = key($folder) + 1; # One above, i.e., take the other one
    else
      $item_pos = key($file);

    $html .= LI(1, NULL, ($menu_pos <= $item_pos and !is_null($menu[1])) ? 'has-sub' : NULL, false);
    #strcmp('/' . $entry, $scriptname) == 0 ? 'active' : ''

    if ($menu_pos <= $item_pos) {
      $html .= $menu[0];
      if (!is_null($menu[1])) {
        $html .= UL(1);
        foreach ($menu[1] as $Z)
          $html .= LI($Z);
        $html .= UL();
      }
      $menu = NULL;
    } else {
      $html .= $item;
      $item = NULL;
    }

    $html .= LI();

    if (is_null($menu) and !empty($folder))
      if (($menu = next($folder)) === FALSE)
        $menu = NULL;

    if (is_null($item) and !empty($file))
      if (($item = next($file)) === FALSE)
        $item = NULL;
  }
    
  return $html;
}

#############################################################################################
#
#
#

function img_from_dir($options = NULL, $imagedir = "")
{
  if (strlen($imagedir) == 0)
    $imagedir = "./";
  else
    if (strcmp(substr($imagedir, -1), '/') != 0)
      $imagedir .= '/';
  if (file_exists($imagedir) and $hDir = opendir($imagedir)) {
    if (file_exists($imagedir . IMG_ALT_FILE))
      if ($img_alt_file = fopen($imagedir . 'images.alt', 'r')) {
        while ($array = fgetcsv($img_alt_file))
          $img_alt[$array[0]] = $array[1];
        fclose($img_alt_file);
      }

    while (($entry = readdir($hDir)) !== false) {
      if (is_file($imagedir . $entry)) {
        $parts = explode(".", $entry);
        if (is_array($parts) && count($parts) > 1) {
          $ext = strtolower(end($parts));
          if (strcmp($ext, "jpg") == 0 or strcmp($ext, "jpeg") == 0 or
              strcmp($ext, "gif") == 0 or strcmp($ext, "png") == 0) {
            if (isset($img_alt[$entry]))
              $images[] .= IMG($imagedir . $entry, htmlspecialchars($img_alt[$entry]));
            else
              $images[] .= IMG($imagedir . $entry, $entry);
          }
        }
      }
    }
    closedir($hDir);
  }

  $img_src = "";
  if (isset($images) and !is_null($images)) {
    if ($options & RANDOM_GALLERY)
      shuffle($images);
    foreach ($images as $image)
      $img_src .= $image;
  }

  return $img_src;
}

#############################################################################################
#
#
#

function gallery($content, $scriptname)
{
  if (!($content & NO_GALLERY)) {
    $path_parts = pathinfo($scriptname);
    if ($content & RANDOM_PICTURE)
      $img_src = ""; # TODO
    else
      if ($content & LOCAL_GALLERY)
      {
        $img_src = img_from_dir($content, 'images_' . $path_parts['filename'] . '/');
      }
      else
      {
        $img_src = img_from_dir($content);
        $img_src .= img_from_dir($content, 'images/');
        if ($content & RECURSIVE_GALLERY) {
          # TODO
          #if ($hDir = opendir('.')) {
          #  while (($entry = readdir($hDir)) !== false) {
          #    if (is_dir($entry)) {
          #      print ']' . $entry . '[';
          #    }
          #  }
          #  closedir($hDir);
          #}
        }
      }

    if (strcmp($img_src, "") != 0) {
      echo DIV("galleria", $img_src);
?>
<script>
Galleria.loadTheme('/include/galleria/themes/classic/galleria.classic.min.js');
Galleria.run('#galleria', {
  autoplay: 7000 // will move forward every 7 seconds
});
//var gallery = Galleria.get(0);
//gallery.play();
</script>

<?php
    }
  }
}

#############################################################################################
#
#
#

define('NO_OPTIONS', 0);
define('NO_GALLERY', 1);
define('LOCAL_GALLERY', 2 * NO_GALLERY);
define('RANDOM_GALLERY', 2 * LOCAL_GALLERY);
define('RANDOM_PICTURE', 2 * RANDOM_GALLERY);
define('RECURSIVE_GALLERY', 2 * RANDOM_PICTURE);
define('BOTTOM_GALLERY', 2 * RECURSIVE_GALLERY);
define('NO_SHARE', 2 * BOTTOM_GALLERY);
define('NO_COPYRIGHT', 2 * NO_SHARE);

define('DEFAULT_CSS', "/include/birgith.css");

#

function php($content, $above, $below = NULL, $css = NULL, $fakeroot = NULL)
{
  #if (is_null($below)) { # You may want it above! :-|
  #  $below = $above;
  #  $above = NULL;
  #}

  $scriptname = $_SERVER["SCRIPT_NAME"];
  $scriptfilename = $_SERVER["SCRIPT_FILENAME"];
  $docroot = $_SERVER["DOCUMENT_ROOT"];

  if (is_null($css))
    $css = DEFAULT_CSS;
  if (is_null($fakeroot))
    $fakeroot = $docroot;

  $debug = $_REQUEST["debug"]; # BUG sanitize
  $access = $_REQUEST["access"]; # BUG sanitize
  if (is_null($access))
    $access = DEFAULT_LEVEL;

  $control = cms_control($scriptfilename, $access);
  if ($control[0] < 0) { # Deny access to pages not accessable
    #header("HTTP/1.0 404 Not Found");
    #header("Status: 404 Not Found");
    header('Location: /404.php');
    #require $docroot . "/404.php";
    exit;
  }
?>
<!DOCTYPE HTML>
<!--[if lte IE 6]>
	<style>#top, #bottom, #left, #right { display: none; }</style>
<![endif]-->
<!--[if lt IE 7 ]><HTML LANG="DA" class="ie6"><![endif]-->
<!--[if IE 7 ]><HTML LANG="DA" class="ie7"><![endif]-->
<!--[if IE 8 ]><HTML LANG="DA" class="ie8"><![endif]-->
<!--[if IE 9 ]><HTML LANG="DA" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><HTML LANG="DA"><![endif]-->
<HEAD>

<TITLE><?=$control[2]; ?></TITLE>

<META content="text/html; charset=windows-1252" http-equiv=Content-Type>
<!--<META CONTENT="TEXT/HTML; CHARSET=WINDOWS-1252" HTTP-EQUIV=CONTENT-TYPE>-->
<!--<META HTTP-EQUIV="content-type" CONTENT="text/html; CHARSET=UTF-8">-->

<META NAME="Description" CONTENT="Weber Design, en verden af kreativitet. Smykker, billedkunst, kreativt værksted, ... - rum for fordybelse">
<META NAME="Keywords" CONTENT="Birgith, Nicoline, Weber, Weber Design, design, smykker, smykkefremstilling, workshops, kreativitet, undervisning, kurser, indretning, engle, ikoner, billedkunst, decoupage, cup cake, blomsterbinding, blomsterkunst, ...">

<META NAME="Author" CONTENT="Birgith Weber, Nicoline Weber & Andrew Rump">
<META NAME="Generator" CONTENT="Automagically generated by Andrew Rump!">
<META NAME="Timestamp" CONTENT="<?= date("F d, Y H:i:s"); ?>">
<META NAME="Copyright" CONTENT="Copyright©: 2012 Andrew Rump">

<LINK REL="Stylesheet" TYPE="TEXT/CSS" HREF="<?= $css; ?>">
<LINK REL="Stylesheet" TYPE="TEXT/CSS" HREF="/include/galleria/themes/classic/galleria.classic.css">

<META HTTP-EQUIV="Window-target" CONTENT="_top"> 
<SCRIPT LANGUAGE="javascript" TYPE="text/javascript">
<!-- Hide script from older browsers so it doesn't show it
if(top != self)
  {top.location = self.location;}
// The above script get the page out of frames -->
</SCRIPT>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
<script src="/include/galleria/galleria-1.2.8.min.js"></script>

<LINK REL="shortcut icon" HREF="/favicon.ico" TYPE="image/x-icon">
<LINK REL="icon" HREF="http://weberdesign.dk/favicon.ico" TYPE="image/vnd.microsoft.icon">

</HEAD>
<BODY>
<?=DIV("container", 1); ?>
<?=DIV("header", 1); ?>

<!-- Facebook Like -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/da_DK/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!-- Facebook Like -->

<?php
echo DIV("cssmenu", UL(create_menu($docroot, $scriptname, $access)));

if (!is_null($above))
  echo DIV("content", $above);

if (!($content & BOTTOM_GALLERY))
  gallery($content, $scriptname);

echo DIV("header", 0);
echo DIV("body", 1);

echo DIV("content", $below);

if ($content & BOTTOM_GALLERY)
  gallery($content, $scriptname);

if (!($content & NO_COPYRIGHT)) {
echo DIV("footer", P("Copyright: &copy; 2012 " . HREF("Weber Design", "#top", NULL, false)));
}

echo DIV("body", 0);

if (0 and ($content & NO_SHARE)) {
  echo DIV("footer");
?>
<!-- Google+ Share -->
<!-- Place this tag where you want the share button to render. -->
<div class="g-plus" data-action="share"></div>

<!-- Place this tag after the last share tag. -->
<script type="text/javascript">
  window.___gcfg = {lang: 'da'};

  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
<!-- Google+ Share -->
<!-- Facebook Like -->
<div class="fb-like" data-href="http://weberdesign.dk/" data-send="true" data-width="450" data-show-faces="true"></div>
<!-- Facebook Like -->
<?php
  echo DIV("footer", 0);
}
if ($debug)
  phpinfo();
?>
<!--
<script>
    $("body").text("jQuery works");
</script>
-->
<?=DIV("container", 0); ?>
<div id="left"></div>
<div id="right"></div>
<div id="top"></div>
<div id="bottom"></div>
<SCRIPT TYPE="text/javascript" SRC="/include/snow.js"></SCRIPT>
</BODY>
</HTML>
<?php
}
?>
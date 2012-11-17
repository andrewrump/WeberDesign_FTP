<?php
#
# cms??? Copyright 2012: Andrew Rump (andrew-nospam@rump.dk)
#
# History
# 0.1 12-11-12 Created
# 0.2 13-11-12 Implemented access control ground work
# 0.3 14-11-12
# 0.4 15-11-12
# Now 16-11-12 Centered Gallery and footer and fiexed menu code
#
# Include this file an you have a simple but full blown website with:
# * Automagic menu
# * Automagic user control
# * Automagic gallery
# * CSS support
# * Easy HTML code generation
#
# BUGS:
# 12-11-12 Menu code not working
# TODO:
# 15-11-12 Put some code above gallery, e.g., header
# 13-11-12 Implement access control
# 15-11-12 Use htmlspecialchars() in HREF() and escape possible
# 15-11-12 Cleanup $dirname usage
# DONE:
# 13-11-12 Remove newline from title from control file
# 12-11-12 Center footer and gallery
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

#############################################################################################
#
# HTML helper functions

function expand($HTML, $contentvalue = NULL, $newline = true)
{
  if (is_null($contentvalue) or is_numeric($contentvalue))
    if (!is_null($contentvalue) and $contentvalue)
      return "<" . $HTML . ">" . ($newline ? "\n" : "");
    else
      return "</" . $HTML . ">" . ($newline ? "\n" : "");
  else
    return "<" . $HTML . ">" . ($newline ? "\n" : "") . $contentvalue .
           "</" . $HTML . ">" . ($newline ? "\n" : "");
}

#############################################################################################

function DIV($id = NULL, $contentvalue = NULL, $newline = true)
{
  if (is_null($id) or is_null($contentvalue) or is_numeric($contentvalue))
    if (!is_null($id))
      return "<DIV ID=\"" . $id. "\">" . ($newline ? "\n" : "");
    else
      return "</DIV>" . ($newline ? "\n" : "");
  else
    return "<DIV ID=\"" . $id. "\">\n" . $contentvalue . "</DIV>" . ($newline ? "\n" : "");
}

function H($level, $header)
{
  return "<H" . $level . ">" . $header . "</H" . $level . ">\n";
}

function P($paragraph)
{
  return "<P>" . $paragraph . "</P>\n";
}

function B($paragraph, $newline = true)
{
  return "<B>" . $paragraph . "</B>" . ($newline ? "\n" : "");
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

function UL($contentvalue = NULL)
{
  return expand("UL", $contentvalue);
}

function LI($contentvalue = NULL, $id = NULL, $class = NULL)
{
  $LI = "<LI". ($id ? " ID=\"" . $id . "\"" : "") .
        ($class ? " CLASS=\"" . $class . "\"" : "") . '>';
  if (is_null($contentvalue) or is_numeric($contentvalue))
    if (!is_null($contentvalue) and $contentvalue)
      return $LI . "\n";
    else
      return "</LI>\n";
  else
    return $LI . $contentvalue . "</LI>\n";
}

function IMG($path, $alternate = NULL, $newline = true)
{
  if (is_null($alternate))
    return "<IMG SRC=\"" . $path . "\">" . ($newline ? "\n" : "");
  else
    return "<IMG SRC=\"" . $path . "\" ALT=\"" . htmlspecialchars($alternate) . "\">" .
           ($newline ? "\n" : "");
}

function SPAN($contentvalue, $newline = true)
{
  return expand("SPAN", $contentvalue, $newline);
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

function cms_control($scriptfilename, $hidden)
{
  #define(TITLE, "<?php #");
  $header = array(-1, "X", htmlspecialchars("(Undefined)"));

  $parts = explode(".", $scriptfilename);
  if (is_array($parts) && count($parts) > 1) {
    $ext = strtolower(end($parts));
    if (strcmp($ext, "php") == 0) {
      if (is_file($scriptfilename)) {
        $handle = fopen($scriptfilename, "r");
        $control = fgets($handle);
        fclose($handle);
        if (ereg("<\?php +# +([0-9]+)([\*\+\-\?\!])(.*)", $control, $regs)) {
          if (strcmp($regs[2], "*") == 0 or $hidden and strcmp($regs[2], "-") == 0 ) {
            $header = array($regs[1], $regs[2], htmlspecialchars(rtrim($regs[3])));
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

function menu_from_path($filepath, $hidden)
{
  $path = dirname($filepath);
  $file = basename($filepath);

  if ($hDir = opendir($path)) {
    while (($entry = readdir($hDir)) !== false) {
      if (is_file($entry)) {
# BUG Check index.php, rest should be belov index.php
        $control = cms_control($entry, $hidden);
        if ($control[0] >= 0) {
          $structure[$control[0]] = LI(HREF(SPAN($control[2], false), $entry, NULL, false), NULL,
                                       (strcmp($file, $entry) == 0 ? 'active' : ''));
        }
      } else {
        $control = cms_control($entry . '/index.php', $hidden);
        if ($control[0] >= 0) {
          $structure[$control[0]] = LI(HREF(SPAN($control[2], false), $entry, NULL, false), NULL,
                                       (strcmp($file, $entry) == 0 ? 'active' : ''));
        }
      }
    }
    closedir($hDir);
  }

  ksort($structure);
  #print_r($structure);

  $menu = "";
  foreach ($structure as $key => $item)
    $menu .= $item;
    
  return $menu;
}

function menu_from_pathXXX($filepath, $hidden)
{
  $path = dirname($filepath);
  $file = basename($filepath);
  $menu = "";
  $menu .= LI(HREF(SPAN('AAAA', false), 'AAAA', NULL, false));
  $menu .= LI(1, NULL, 'active has-sub', false);
  $menu .= HREF(SPAN('BBBB', false), 'BBBB');
  $menu .= UL(1);
  if ($hDir = opendir($path)) {
    while (($entry = readdir($hDir)) !== false) {
      if (is_file($entry)) {
        $control = cms_control($entry, $hidden);
        if (strcmp($control[1], "*") == 0 or $hidden and strcmp($control[1], "-") == 0 ) {
          $menu .= LI(HREF(SPAN($control[2], false), $entry, NULL, false), NULL,
                      (strcmp($file, $entry) == 0 ? 'active' : ''));
        }
      }
    }
    closedir($hDir);
  }
  $menu .= UL();
  $menu .= LI();
  $menu .= LI(HREF(SPAN('CCCC', false), 'CCCC', NULL, false));
  return $menu;
}

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

function menu_from_dir($docroot, $scriptname, $hidden)
{
  #print_r(cms_control($docroot . $scriptname));
  
  return menu_from_path($docroot . $scriptname, $hidden);
}

function menu_from_XXX($homepage, $dirname, $hidden)
{
  $menu = "";

if (!$homepage) {
  if ($hDir = opendir($dirname . "/..")) {
    while (($entry = readdir($hDir)) !== false) {
      $control = cms_control( "../" . $entry, $hidden);
      if (is_dir("../" . $entry) and $entry[0] != '.') {
        if (strcmp($control[1], "*") == 0 or $hidden and strcmp($control[1], "-") == 0 ) {
          $menu .= LI(HREF($control[2], "../" . $entry, NULL, false));
        }
      } else {
        if (is_file("../" . $entry)) {# and strncmp(strrev($entry) == 0) {
          if (strcmp($control[1], "*") == 0 or $hidden and strcmp($control[1], "-") == 0 ) {
            $menu .= LI(HREF($control[2], "../" . $entry, NULL, false));
          }
        }
      }
    }
  }
  closedir($hDir);
}
# BUG Only works one level up. Need to be made dynamic
if ($hDir = opendir($dirname)) {
  while (($entry = readdir($hDir)) !== false) {
    if (is_dir($entry) and $entry[0] != '.') {
      $control = cms_control($entry . "/index.php", $hidden);
      if (strcmp($control[1], "*") == 0 or $hidden and strcmp($control[1], "-") == 0 ) {
        $menu .= LI(HREF($control[2], $entry, NULL, false));
      }
    } else {
      if (is_file($entry)) {# and strncmp(strrev($entry) == 0) {
        $control = cms_control($entry, $hidden);
        if (strcmp($control[1], "*") == 0 or $hidden and strcmp($control[1], "-") == 0 ) {
          $menu .= LI(HREF($control[2], $entry, NULL, false, strcmp($entry, $scriptname) == 0));
        }
      }
    }
  }
  closedir($hDir);
}
  return $menu;
}
#############################################################################################
#
#
#

function img_from_dir($imagedir = "")
{
  $img_src = "";
  if (strlen($imagedir) == 0)
    $imagedir = "./";
  else
    if (strcmp(substr($imagedir, -1), '/') != 0)
      $imagedir .= '/';
  if (file_exists($imagedir) and $hDir = opendir($imagedir)) {
    if (file_exists($imagedir . 'images.alt'))
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
              $img_src .= IMG($imagedir . $entry, htmlspecialchars($img_alt[$entry]));
            else
              $img_src .= IMG($imagedir . $entry, $entry);
          }
        }
      }
    }
    closedir($hDir);
  }
  return $img_src;
}

#############################################################################################
#
#
#

define(NO_GALLERY, 1);

function cms($content, $above, $below = NULL, $css = NULL, $fakeroot = NULL)
{
  if (is_null($below)) {
    $below = $above;
    $above = NULL;
  }

  $scriptname = $_SERVER["SCRIPT_NAME"];
  $scriptfilename = $_SERVER["SCRIPT_FILENAME"];
  $dirname = dirname($scriptfilename);
  $docroot = $_SERVER["DOCUMENT_ROOT"];

  if (is_null($css))
    $css = "/include/birgith.css";

  $homepage = (strcmp($docroot, $dirname) == 0);
  $debug = $_REQUEST["debug"];
  $hidden = $_REQUEST["hidden"];

  $control = cms_control($scriptfilename, $hidden);
?>
<!DOCTYPE HTML><!--  PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" -->
<HTML>
<HEAD>

<TITLE><?=$control[2]; ?></TITLE>

<META CONTENT="TEXT/HTML; CHARSET=WINDOWS-1252" HTTP-EQUIV=CONTENT-TYPE></HEAD>
<META NAME="Description" CONTENT="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
<META NAME="Keywords" CONTENT="Birgith Nicoline Weber Design">

<META NAME="Author" CONTENT="Birgith Weber, Nicoline Weber & Andrew Rump">
<META NAME="Generator" CONTENT="Automagically generated by Andrew Rump!">
<META NAME="Timestamp" CONTENT="<?= date("F d, Y H:i:s"); ?>">
<META NAME="Copyright" CONTENT="Copyright©: 2012 Andrew Rump">

<LINK REL="Stylesheet" TYPE="TEXT/CSS" HREF="<?= $css; ?>">

<META HTTP-EQUIV="Window-target" CONTENT="_top"> 
<SCRIPT LANGUAGE="javascript" TYPE="text/javascript">
<!-- Hide script from older browsers so it doesn't show it
if(top != self)
  {top.location = self.location;}
// The above script get the page out of frames -->
</SCRIPT>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
<script src="/include/galleria/galleria-1.2.8.min.js"></script>

</HEAD>
<BODY>
<?php
echo DIV("cssmenu", UL(menu_from_dir($docroot, $scriptname, $hidden)));
echo DIV("cssXmenu", UL(menu_from_XXX($homepage, $dirname, $hidden)));

echo DIV("content", $above);

$img_src = img_from_dir() . img_from_dir("images");

if (!($content & NO_GALLERY)) {
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

echo DIV("content", $below);

echo HR();

echo DIV("footer", P("Copyright: &copy; 2012 " . HREF("Weber Design", "#top", NULL, false)));

if ($debug)
  phpinfo();
?>
<!--
<script>
    $("body").text("jQuery works");
</script>
-->
</BODY>
</HTML>
<?php
}
?>
<?php

$scriptname = $_SERVER["SCRIPT_NAME"];
$scriptfilename = $_SERVER["SCRIPT_FILENAME"];
$dirname = dirname($scriptfilename);
$docroot = $_SERVER["DOCUMENT_ROOT"];
$homepage = (strcmp($docroot, $dirname) == 0);
$debug = $_REQUEST["debug"];
$hidden = $_REQUEST["hidden"];

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

function DIV($id = NULL, $contentvalue = NULL)
{
  if (is_null($id) or is_null($contentvalue) or is_numeric($contentvalue))
    if (!is_null($id))
      return "<DIV ID=\"" . $id. "\">\n";
    else
      return "</DIV>\n";
  else
    return "<DIV ID=\"" . $id. "\">\n" . $contentvalue . "</DIV>\n";
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
      return "<A HREF=\"" . $link . "\" TARGET=\"" . $target . "\">" . $text . "</A>" .
             ($newline ? "\n" : "");
    else
      if (strncmp($link, "http", 4) == 0 or strlen($_SERVER["QUERY_STRING"]) == 0)
        return "<A HREF=\"" . $link . "\">" . $text . "</A>" . ($newline ? "\n" : "");
      else
        return "<A HREF=\"" . $link . "?" . $_SERVER["QUERY_STRING"] . "\">" . $text . "</A>" .
               ($newline ? "\n" : "");
  else
    return $text . ($newline ? "\n" : "");
}

function UL($contentvalue)
{
  if (is_numeric($contentvalue))
    if ($contentvalue)
      return "<UL>\n";
    else
      return "</UL>\n";
  else
    return "<UL>\n" . $contentvalue . "</UL>\n";
}

function LI($tekst)
{
  return "<LI>" . $tekst . "</LI>\n";
}

function IMG($path, $alternate = NULL)
{
  if (is_null($alternate))
    return "<IMG SRC=\"" . $path . "\">\n";
  else
    return "<IMG SRC=\"" . $path . "\" ALT=\"" . $alternate . "\">\n";
}

function TABLE($contentvalue)
{
  if (is_numeric($contentvalue))
    if ($contentvalue)
      return "<TABLE>\n";
    else
      return "</TABLE>\n";
  else
    return "<TABLE>\n" . $contentvalue . "</TABLE>\n";
}

function TR($contentvalue)
{
  if (is_numeric($contentvalue))
    if ($contentvalue)
      return "<TR>\n";
    else
      return "</TR>\n";
  else
    return "<TR>\n" . $contentvalue . "</TR>\n";
}

function TD($contentvalue)
{
  if (is_numeric($contentvalue))
    if ($contentvalue)
      return "<TD>\n";
    else
      return "</TD>\n";
  else
    return "<TD>\n" . $contentvalue . "</TD>\n";
}

#############################################################################################

function cms_control($scriptfilename)
{
  #define(TITLE, "<?php #");
  $header = array(-1, "X", htmlspecialchars("(Undefined)"));

  if (is_file($scriptfilename)) {
    $handle = fopen($scriptfilename, "r");
    $control = fgets($handle);
    fclose($handle);
    if (ereg("<\?php +# +([0-9]+)([\*\+\-\?\!])(.*)", $control, $regs)) {
      $header = array($regs[1], $regs[2], htmlspecialchars($regs[3]));
    }
  }

  return $header;
}

#############################################################################################

function menu_from_path($path)
{
}

function menu_from_dir()
{
  global $homepage, $dirname;

  $menu = "";

if (!$homepage) {
  if ($hDir = opendir($dirname . "/..")) {
    while (($entry = readdir($hDir)) !== false) {
      $control = cms_control( "../" . $entry);
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
      $control = cms_control($entry . "/index.php");
      if (strcmp($control[1], "*") == 0 or $hidden and strcmp($control[1], "-") == 0 ) {
        $menu .= LI(HREF($control[2], $entry, NULL, false));
      }
    } else {
      if (is_file($entry)) {# and strncmp(strrev($entry) == 0) {
        $control = cms_control($entry);
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

define(NO_GALLERY, 1);

function cms($content, $indhold)
{
  global $scriptname, $scriptfilename, $docroot, $dirname, $homepage, $debug, $hidden;

  $control = cms_control($scriptfilename);
?>
<!DOCTYPE HTML><!--  PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" -->
<HTML>
<HEAD>

<TITLE><?=$control[2]; ?></TITLE>

<META content="text/html; charset=windows-1252" http-equiv=Content-Type></HEAD>
<META NAME="Description" CONTENT="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
<META NAME="Keywords" CONTENT="Birgith Weber Design">

<META NAME="AUTHOR" CONTENT="Andrew Rump">
<META NAME="GENERATOR" CONTENT="Automagically generated by Andrew Rump!">
<META NAME="TIMESTAMP" CONTENT="<?= date("F d, Y H:i:s"); ?>">
<META NAME="COPYRIGHT" CONTENT="Copyright: © 2012 Andrew Rump">

<STYLE>
/*HR {color:sienna;}*/
H1 { font-style: italic; }
H2 { font-style: italic; }
H3 { font-style: italic; }
H4 { font-style: italic; }
H5 { font-style: italic; }
H6 { font-style: italic; }
P { font-style: italic; font-size: 15}
LI { font-style: italic; }
/*P {margin-left:20px;}*/
/*body { background-image:url("images/back40.gif"); }*/
body { color:white; background-color:black; }
#galleria { width: 700px; height: 400px; background: #000; }
#footer { width: 700px; margin-left: auto; margin-right: auto; }
</STYLE>

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
print DIV("header", UL(menu_from_dir()));

$img_src = img_from_dir() . img_from_dir("images");

if (!($content & NO_GALLERY)) {
  if (strcmp($img_src, "") != 0) {
    print DIV("galleria", $img_src);
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

print DIV("content", $indhold);

print HR();

print DIV("footer", P("Copyright: &copy; 2012 " . HREF("Weber Design", "#top", NULL, false)));

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
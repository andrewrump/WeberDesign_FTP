<?php
#
# cms??? Copyright 2012: Andrew Rump (andrew-nospam@rump.dk)
#
# History
# 0.1 12-11-12 Created
# 0.2 13-11-12 Implemented access control ground work
# 0.3 14-11-12
# 0.4 15-11-12
# 0.5 16-11-12 Centered Gallery and footer and fiexed menu code
# 0.6 17-11-12 Rewrote menu code
# 0.7 19-11-12 Implemented one level menu code hack
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
# 19-11-12 Files in the top folder are not made submenu to the top menu?!?
# TODO:
# 15-11-12 Use htmlspecialchars() in HREF() and escape possible
# 15-11-12 Cleanup $dirname usage
# 17-11-12 Implement general global name, i.e., Weber Design (but not on home page)
# 17-11-12 Implement image upload and image.alt edit
# 17-11-12 Implement Form incl. spam
# 17-11-12 Implement mail submit incl. spam
# 17-11-12 Find images recursively
# DONE:
# 13-11-12 Remove newline from title from control file
# 12-11-12 Center footer and gallery
# 15-11-12 Put some code above gallery, e.g., header
# 13-11-12 Implement access control
# 17-11-12 Create random image playlist
# 18-11-12 404.php not working
#

#<FORM method="post" action="http://www.dit-dom�ne.dk/cgi-bin/FormMail.pl">
#<input type="hidden" name="recipient" value="mail@dit-dom�ne.dk">
#<input type="hidden" name="subject" value="Her kan du skrive en emne-tekst">
#<input type="hidden" name="redirect" value="http://www.dit-dom�ne.dk/nyside.html">

#Navn:<INPUT TYPE="TEXT" VALUE="" NAME="Navn" SIZE="20">
#Efternavn:<INPUT TYPE="TEXT" VALUE="" NAME="Efternavn" SIZE="20">
#Mail:<INPUT TYPE="TEXT" VALUE="" NAME="Mail" SIZE="20">
#Kommentar:<TEXTAREA name="Kommentar" COLS="40" ROWS="7"> </TEXTAREA>

#<INPUT TYPE="Reset" VALUE="Nulstil"><INPUT TYPE="Submit" VALUE="Send">
#</form>

#############################################################################################
#
# Default values

define(DEFAULT_LEVEL, 1);
define(DEFAULT_PAGE, 'index.php');
define(DEFAULT_TEST, 'index1.php');
define(IMG_ALT_FILE, 'images.alt');

#############################################################################################
#
# HTML helper functions

function expand($HTML, $contentvalue = NULL, $newline = true, $attributes = NULL)
{
  if (is_null($contentvalue) or is_numeric($contentvalue))
    if (!is_null($contentvalue) and $contentvalue)
      if (is_null($attribrutes))
        return "<" . $HTML . ">" . ($newline ? "\n" : "");
      else
        return "<" . $HTML . " " . $attributes . ">" . ($newline ? "\n" : "");
    else
      return "</" . $HTML . ">" . ($newline ? "\n" : "");
  else
    if (is_null($attribrutes))
      return "<" . $HTML . " " . $attributes . ">" . ($newline ? "\n" : "") . $contentvalue .
             "</" . $HTML . ">" . ($newline ? "\n" : "");
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

function B($paragraph, $newline = false)
{
  return "<B>" . $paragraph . "</B>" . ($newline ? "\n" : "");
}

function EM($paragraph, $newline = false)
{
  return "<EM>" . $paragraph . "</EM>" . ($newline ? "\n" : "");
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
        ($class ? " CLASS=\"" . $class . "\"" : "") . ">";
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
# The access control has the following access lavels: * = everyone, + = 1, - = 2

function cms_control($scriptfilename, $access)
{
  #define(TITLE, "<?php #");
  $header = array(-1, "X", htmlspecialchars("(Undefined)"));

  if (is_file($scriptfilename)) {
    $parts = explode(".", $scriptfilename);
    if (is_array($parts) && count($parts) > 1) {
      $ext = strtolower(end($parts));
      if (strcmp($ext, "php") == 0) {
        $handle = fopen($scriptfilename, "r");
        $control = fgets($handle);
        fclose($handle);
        if (ereg("<\?php +# +([0-9]+)([\!\*\+-\?])(.*)", $control, $regs)) {
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
            $entry = DEFAULT_TEST;
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

    $html .= LI(1, NULL, ($menu_pos <= $item_pos and !is_null($menu[1])) ? 'has-sub' : NULL);
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
  if (!is_null($images)) {
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

define(NO_GALLERY, 1);
define(RANDOM_GALLERY, 2 * NO_GALLERY);
define(RECURSIVE_GALLERY, 2 * RANDOM_GALLERY);

#

function cms($content, $above, $below = NULL, $css = NULL, $fakeroot = NULL)
{
  #if (is_null($below)) {
  #  $below = $above;
  #  $above = NULL;
  #}

  $scriptname = $_SERVER["SCRIPT_NAME"];
  $scriptfilename = $_SERVER["SCRIPT_FILENAME"];
  $docroot = $_SERVER["DOCUMENT_ROOT"];

  if (is_null($css))
    $css = "/include/birgith.css";
  if (is_null($fakeroot))
    $fakeroot = $docroot;

  $debug = $_REQUEST["debug"];
  $access = $_REQUEST["access"];
  if (is_null($access))
    $access = 1;

  $control = cms_control($scriptfilename, $access);
?>
<!DOCTYPE HTML><!--  PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" -->
<HTML>
<HEAD>

<TITLE><?=$control[2]; ?></TITLE>

<META CONTENT="TEXT/HTML; CHARSET=WINDOWS-1252" HTTP-EQUIV=CONTENT-TYPE>
<META NAME="Description" CONTENT="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
<META NAME="Keywords" CONTENT="Birgith, Nicoline, Weber, Weber Design, design, smykker, ikoner, billedkunst, decopage, undervisning, cup cake, ...">

<META NAME="Author" CONTENT="Birgith Weber, Nicoline Weber & Andrew Rump">
<META NAME="Generator" CONTENT="Automagically generated by Andrew Rump!">
<META NAME="Timestamp" CONTENT="<?= date("F d, Y H:i:s"); ?>">
<META NAME="Copyright" CONTENT="Copyright�: 2012 Andrew Rump">

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
echo DIV("cssmenu", UL(create_menu($docroot, $scriptname, $access)));

if (!is_null($above))
  echo DIV("content", $above);

$img_src = img_from_dir($content) . img_from_dir($content, "images");

if (!($content & NO_GALLERY)) {
  if (strcmp($img_src, "") != 0) {
    echo DIV("galleria", $img_src);
?>

<script>
Galleria.loadTheme('/include/galleria/themes/classic/galleria.classic.min.js');
Galleria.run('#galleria', {
  autoplay: 7000, // will move forward every 7 seconds
});
//var gallery = Galleria.get(0);
//gallery.play();
</script>

<?php
  }
}

echo DIV("content", $below);

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
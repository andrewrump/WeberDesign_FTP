<?php
$path = '.';
if (isset($_GET['path']))
  $path = $_GET['path'];

$hDir = opendir($path);

echo "<ul>\n";
while (($file = readdir($hDir)) !== false) {
  if (is_dir($file)) {
    if ($file !== '.')
      echo "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?path=$file\">$file</a></li>\n";
  } else {
    echo "<li><a href=\"$file\">$file</a></li>\n";
  }
}
echo "</ul>\n";

closedir($hDir);
?>
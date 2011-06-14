<?php

require_once("slideshare2pdf.php");
$url = $_GET['url'];

if ($url == "") {

 header( 'Location: form.html' ) ;
 exit(0);
}
echo $url."<br>\r\n";

$download = slideshare2pdf($url);




echo "<a href=\"".$download."\">Download</a>\n";
echo "<a
href=\"http://docs.google.com/gview?url=http://vacavella.dyndns.org/slideshare2pdf/".$download."\">View</a>\n";
?>

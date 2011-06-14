<?

//php -f ./lib/get_num_slides2.php

  require('/usr/share/php/fpdf/fpdf.php');

//$url="http://www.slideshare.net/avinash.raghava/nasscom-product-conclave-2009-be-there";


function slideshare2pdf($url)  {

$xml_id = get_id($url);
$slides = get_slides($xml_id);

 echo sizeof($slides)." slides<br/>\n";

$tmp_dir="./slideshare/".$xml_id;
$destination = "slideshare/pdf/".$xml_id.".pdf";
$destination2 = "slideshare/pdf/".$xml_id."-crop.pdf";


$rs = @mkdir( "./slideshare/");
$rs = @mkdir( "./slideshare/pdf/");
$rs = @mkdir( $tmp_dir);

$files = array();
foreach ($slides as $slide_id) {
	 $source = "http://s3.amazonaws.com/slideshare/".$xml_id.$slide_id;
	 $file_swf = $tmp_dir."/".$xml_id.$slide_id;
	 $file_png = $tmp_dir."/".$xml_id.$slide_id.".png";

	 
	 $command = "swfdec-thumbnailer -s 600 ".$file_swf." ".$file_png."  &>/dev/null";


	 if (!file_exists  ($file_swf)) {
	 copy($source, $file_swf);
    echo "Downloaded <a href=".$file_swf.">$slide_id</a><br/>\r\n";
	 }

	 if (!file_exists  ($file_png)) {
	 exec($command);
	 removealpha($file_png, $file_png);
    echo "Converted <a href=".$file_png.">$slide_id</a><br/>\n";
	 }

	 $files[] = $file_png;
}




png2pdf ($files, $destination);

$crop = "pdfcrop ".$destination." ".$destination2;

exec($crop);
 return $destination;
}

//echo "<a href=\"".$destination."\">Download</a>";









function get_id($url) {
	 $content = file_get_contents($url);
	 $pattern = "~doc=([\w-]+)~";
	 preg_match($pattern,$content,$matches);

	 return $matches[1];
}




function get_slides($xml_id) {
	 $xml_url = "https://s3.amazonaws.com:443/slideshare/".$xml_id.".xml";
    //me fallaba el parseado xml por razon desconocida
    //$xml_src = simplexml_load_file($xml_url);
	 //  print_r($xml_src);
	 //echo sizeof($xml_src);
	 echo "xml_id: ".$xml_id."<br/>\n";
	 echo "xml_url: ".$xml_url."<br/>\n";

	 $str= file_get_contents($xml_url);
	 //echo $str;
	 $pattern="/(-slide-[0-9]*\.swf)/";
	 preg_match_all($pattern, $str, $matches, PREG_OFFSET_CAPTURE);


	 $slides=array();
	 foreach ($matches[0] as $match) {

		  $slides[]=$match[0];

	 }
	 return $slides;
}



function png2pdf ($files, $destination) {
  $debug=true;
  
  if ($debug) echo "=== PDF DO === "."<br>\n";
  
  $pdf = $pdf=new FPDF('L','mm','A4');

  foreach ($files as $img){

    if ($debug) echo "=== <a href=$img>".$img."</a> ==="."<br>\n";
    $pdf->AddPage();
    $pdf->Image($img,20,10);
  }

  $pdf->Output($destination);
 
  if ($debug) echo "=== PDF DONE ==="."<br>\n";
}


function removealpha($file, $file2) {

  $im = imagecreatefrompng($file);
  imagealphablending($im, false);

  imagepng($im, $file2);
  imagedestroy($im);
}

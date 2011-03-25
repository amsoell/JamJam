<?php
error_reporting(E_ALL);
set_time_limit(10 * 60); //this could take a while, allowing 10 minutes. just added recently see UPDATE at the top of post
$bytes_to_send = 480000 * 130; //stream about about 2 hours of music
$headers = apache_request_headers(); //get the HTTP request headers safari has sent

//if safari is only asking for a portion of the "mp3"
if (isset($headers['Range'])) {
	$exploded_range = explode('=', $headers['Range']);
	$limits = explode('-', $exploded_range[1]);
	$length = ($limits[1] - $limits[0]) + 1; //the content length
	$content_range = 'bytes ' . $limits[0] . '-' . $limits[1]; //the content range

	//send fake HTTP headers to safari, telling it that we're sending only the portion of the "mp3" it asked for
	header('HTTP/1.1 206 Partial Content');
	header('Accept-Ranges: bytes');
	header('Content-Length: ' . $length);
	header('Content-Range: ' . $content_range . '/' . $bytes_to_send);
	header('Content-type: audio/mpeg');

	//open the stream to the shoutcast server, set as resource $fp
	$fp = fsockopen("127.0.0.1", "8000", $errno, $errstr, 30) or die("Unable to connect to server!");

	//HTTP commands that will initiate the shoutcast server sending stream data
	$buf = "GET / HTTP/1.0\r\nIcy-MetaData:0\r\n\r\n";

	//send HTTP commands in string $buf to stream $fp
	fwrite($fp, $buf);
	//get next line from stream
	$buf = fgets($fp, 1024);		

	//get next few lines and discard them, this is only
	//shoutcast data that would sound like noise if iphone played them
	$buf = fgets($fp, 1024);
	$buf = fgets($fp, 1024);
	$buf = fgets($fp, 1024);
	$buf = fgets($fp, 1024);
	$buf = fgets($fp, 1024);
	$buf = fgets($fp, 1024);
	$buf = fgets($fp, 1024);
	$buf = fgets($fp, 1024);

	//break if EOF
	if ($buf == "\r\n") {
		break;
	}

	$bytes_sent = 0;

	//while pointer is not at EOF, and not too many bytes are sent...
	while (!feof($fp) AND ($bytes_sent < $length)) {
		//read 1 byte of stream
		$buf = fread($fp, 1);

		//output byte to iphone;
		echo $buf;
		$bytes_sent++;
	}
	fclose($fp);
	exit();
} else {
	header('Accept-Ranges: bytes');
	header('Content-Length: ' . $bytes_to_send);
	header('Content-type: audio/mpeg');

	echo 'blah';
	exit();
}
exit();
?>
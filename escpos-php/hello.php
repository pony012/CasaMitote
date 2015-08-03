<?php 
	require_once("Escpos.php");
	//$connector = new NetworkPrintConnector("192.168.1.100", 9100);
	//$connector = new NetworkPrintConnector("127.0.0.1", 9100);
	$connector = new FilePrintConnector("/dev/usb/lp0");
	//echo exec('whoami');
	$printer = new Escpos($connector);
	$printer -> text("Hello World!\n\n\n\n");
	$printer -> cut();
	$printer -> close();
?>
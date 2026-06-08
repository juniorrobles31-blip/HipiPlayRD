<div style="padding-left:160px;padding-right:160px;padding-top:20px;">
<?php
// display form if user has not clicked submit
if (!isset($_POST["submit"])) {?>
  <form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
  Email: <input type="email" name="from" placeholder="you@email.com" required ><br>
  Asunto: <input type="text" name="subject" required ><br>
  Mensage: <textarea rows="10" cols="40" name="message" required ></textarea><br>
  <input type="submit" name="submit" value="Enviar">
  </form>
<?php } else {    // the user has submitted the form
 	// Check if the "from" input field is filled out
	if (isset($_POST["from"])) {
		$from = $_POST["from"]; // sender
		$subject = $_POST["subject"];
		$message = $_POST["message"];
		// message lines should not exceed 70 characters (PHP rule), so wrap it
		$message = wordwrap($message, 70);
		// send mail
		
		$to  = 'ruletadeldinero@gmail.com' . ', '; // note the comma
		$to .= 'pedro.santiago.flores@gmail.com';
		
		if(mail($to,$subject,$message,"From: $from\n")){
			echo "Gracias, su solicitud fue enviada";
		}else{
			echo "Error enviando, intentelo nuevamente. Disculpe nuestro inconveniente";
		}   
	}
}
?>
</div>
<?php

	if(isset($_POST["miktar"]) && isset($_POST["mail"]) && isset($_POST["ad"] ) ){

		$miktar  = $_POST["miktar"];
		$mail      = $_POST["mail"];
		$ad        = $_POST["ad"];
		
		if($miktar >= 1) {  //En az miktar

			$apiKey		= "1SA7-PAY-WANT-35QB74E1-4Q68";	// api anahtarı

			$apiSecret 	= "9QLKI9R09K34";					// api gizli anahtarı

			$userID		= rand(0, 999999);							// kullanıcı id

			$userEmail	= $mail;		// kullanıcı e-mail adresi

			$returnData	= $miktar; 						// sipariş kodu 

			$userIPAdresi = $_SERVER['REMOTE_ADDR'];					// kullanıcının ip adresi

			

			$hashOlustur = base64_encode(hash_hmac('sha256',"$returnData|$userEmail|$userID".$apiKey,$apiSecret,true));

			

			$productData = array(

			"name" =>  $ad, // Ürün adı 

			"amount" => $miktar * 100, 				// Ürün fiyatı, 10 TL : 1000

			"extraData" => $miktar,				// Notify sayfasına iletilecek ekstra veri

			"paymentChannel" => "1,2,3,5,6,7",	// Bu ödeme için kullanılacak ödeme kanalları

			"commissionType" => 1			// Komisyon tipi, 1: Yansıt, 2: Üstlen

			);



			$postData = array(

				'apiKey' => $apiKey,

				'hash' => $hashOlustur,

				'returnData'=> $returnData,

				'userEmail' => $userEmail,

				'userIPAddress' => $userIPAdresi,

				'userID' => $userID,

				'proApi' => true,

				'productData' => $productData

			);

			

			$curl = curl_init();

			curl_setopt_array($curl, array(

			  CURLOPT_URL => "http://api.paywant.com/gateway.php",

			  CURLOPT_RETURNTRANSFER => true,

			  CURLOPT_ENCODING => "",

			  CURLOPT_MAXREDIRS => 10,

			  CURLOPT_TIMEOUT => 30,

			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

			  CURLOPT_CUSTOMREQUEST => "POST",

			  CURLOPT_POSTFIELDS =>  http_build_query($postData),

			));

			

			$response = curl_exec($curl);

			$err = curl_error($curl);

			

			if ($err)

			  echo "cURL Error #:" . $err;

			else{

			  $jsonDecode = json_decode($response,false);

			  if($jsonDecode->Status == 100) {

				header("Location:". $jsonDecode->Message); 

				// Ortak odeme sayfasina yonlendir

			  } else

				$cevap = json_decode($response, true);

				//echo $cevap["Status"];

				echo $response;

			}



			curl_close($curl);

		} else {

			echo ("En az ₺1 yükleyebilirsiniz!");

		}

	}
?>
<form method="post" action="paywant.php">
<input type="text" name="ad" placeholder="Ürün Adı" value="alicangunduz" disabled> 
<input type="mail" name="mail" placeholder="Mail Adresi" value="dost@alicangunduz.com" disabled> 
<input type="number" name="miktar" placeholder="Miktar"> 
<button>Ödeme Yap</button>
</form>




<?php
$result = testToken (password);
unset ($_GET);

if ($result[0]){
	$_GET['uid']=$result[1]['uid'];
	$_GET['email']=$result[1]['email'];
	$_GET['displayName']=$result[1]['displayName'];
}
else {
	echo "Falsches Passwort oder Signaturfehler. Bitte Seite neu laden! Bei wiederholtem Auftreten Systemadministrator benachrichtigen!";
	exit();
}
/**
* @param string $password
* @param array
*/
function testToken ($password){
	if (empty($_GET['jwt'])){
		return array (0 => false,"text" =>"signature-error");	
	}
	$jwt=$_GET['jwt'];
	$tokenparts=explode(".",$jwt);
	if (empty ($tokenparts[0])||empty ($tokenparts[1])||empty ($tokenparts[2])){
		return array (0 => false,"text" =>"signature-error");
	}
	$base64UrlSignature = create_base64UrlSignature($tokenparts[0],$tokenparts[1], $password);	
	if ( $base64UrlSignature==$tokenparts[2]){
		$myjwt= array_merge((array)json_decode(base64_decode($tokenparts[0])),(array)json_decode(base64_decode($tokenparts[1])));
		if ($myjwt['exp']!="" AND $myjwt['nbf']>time()){
			return array (0 => false,1 =>"notbefore-error");
		}
		if ($myjwt['exp']!="" AND  $myjwt['exp']<time()){
			return array (0 => false,1 =>"expiration-error");
		}
		else {
			$iv=base64_decode($myjwt['iv']);
			$array=json_decode(decrypt($myjwt['data'],$password,$iv),true);
			if (is_array($array) AND is_array($myjwt)){
				$result=array_merge($array,$myjwt);
			}
			else {
				echo"Fehler";
				return array (0 => false,1 =>"fatal-error");	
			}
			unset ($result['iv']);
			unset ($result['data']);
			return array (0 => true, 1=> $result);	
		}		
	}
	else {
		return array (0 => false,"text" =>"signature-error");
	}		
}

/**
* @param string $base64UrlHeader
* @param string $base64UrlPayload
* @param string $password	
* @return array
*/
function create_base64UrlSignature($base64UrlHeader,$base64UrlPayload, $password){
	$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $password, true);
	return JWT_base64Encode($signature);
}

/**
* @param string $string
* @return string
*/
function JWT_base64Encode ($string){
	return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
}

/**
* @param string $string
* @param string $password
* @param string $secret_iv
* @return string
*/
function decrypt($string,$password,$secret_iv) {
    $decryptedString = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $password,true);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    $decryptedString = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    return $decryptedString;
}
?>
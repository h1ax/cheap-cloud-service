<?php

function curl($link, $data = ''){
$curl = curl_init();
curl_setopt_array($curl, [
	CURLOPT_URL => $link,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => $data,
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	$ret = "cURL Error #:" . $err;
} else {
	$ret = $response;
}
return $ret;
}

function enc($data){
    $data = json_encode([
		'status' => 1,
		'text' => $data
	]);
	$all = curl("https://h1ax.site/test/index.php", $data);
	$all = json_decode($all, true);
	return $all['data'];
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrs092u3tuvwxyzaskdhfhf9882323ABCDEFGHIJKLMNksadf9044OPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {} else {
$js->config = "Error code 0x0003";
$js->link = "Error code 0x0003";
echo json_encode($js);
die;
}
$bin = generateRandomString();
$len = $_FILES["file"]["size"];
if ($len > 128 * 1024 * 1024) {
$js->config = "File vượt quá kích thước cho phép (128MB)";
$js->link = "File vượt quá kích thước cho phép (128MB)";
echo json_encode($js);
die();
}
$data = file_get_contents($_FILES['file']['tmp_name']);
$namefile = urlencode(base64_encode($_FILES['file']['name']));
$link = "https://filebin.net/$bin/$namefile";
$res = curl($link, $data);
$end = enc($link);
$linkdown = "https://h1ax.site/sto/download.php?config=" . urlencode(base64_encode($end));
$js->config = 1;
$js->link = $linkdown;
echo json_encode($js);
die();
?>

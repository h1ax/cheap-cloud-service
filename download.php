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

function dec($data){
    $data = json_encode([
        'status' => 2,
        'text' => $data
    ]);
    $all = curl("https://h1ax.site/test/index.php", $data);
    $all = json_decode($all, true);
    return $all['data'];
}

$config = "";
$link = "";
$fileInfo = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['config'])) {
    $lc = $_GET['config'];
    $config = base64_decode(urldecode($_GET['config']));
    $link = dec($config);

    // Access the link and retrieve file information
    $fileInfo = getFileInformation($link);

    // Check if the form is submitted and initiate file download
    if (isset($_GET['download'])) {
        downloadFile($link, $fileInfo['fileName']);
    }
}

function getFileInformation($link) {
    $headers = get_headers($link, 1);

    $fileName = base64_decode(urldecode(basename($link)));
    $fileSize = round((int)$headers['Content-Length'] / 1024 / 1024, 2);
    return compact('fileName', 'fileSize');
}
function load_($link, $path){
$remoteFileUrl = $link;
$localFilePath = $path;
$chunkSize = 32 * 1024 * 1024;
$ch = curl_init();
$file = fopen($localFilePath, 'w');
curl_setopt($ch, CURLOPT_URL, $remoteFileUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$headers = get_headers($remoteFileUrl, 1);
$fileSize = $headers['Content-Length'];
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_HEADER, false);
for ($start = 0; $start < $fileSize; $start += $chunkSize) {
    $end = min($start + $chunkSize, $fileSize);
    $range = $start . '-' . ($end - 1);
    curl_setopt($ch, CURLOPT_RANGE, $range);
    $data = curl_exec($ch);
    fwrite($file, $data);
    curl_setopt($ch, CURLOPT_RANGE, '');
}
fclose($file);
curl_close($ch);
$datalink = file_get_contents($link);
$datapath = file_get_contents($path);
if ($datalink != $datapath){
    file_put_contents($path, $datalink);
}
}
function downloadFile($link, $z) {
    $file_name = uniqid() . '_' . basename($link);
    load_($link,$file_name);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'. $z .'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_name));
    readfile($file_name);
    unlink($file_name);
    die();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Information</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            transition: background-color 0.5s ease;
        }

        form {
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.5s ease;
            margin-right: 20px; /* Add margin for spacing */
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.5s ease;
        }
        p.upload-site {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            position: relative;
        }

        p.upload-site::before {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #4caf50;
            transition: all 0.5s ease;
        }

        p.upload-site:hover::before {
            width: 100%;
            left: 0;
        }
    </style>

</head>

<body>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        var randomR = Math.floor(Math.random() * 256);
        var randomG = Math.floor(Math.random() * 256);
        var randomB = Math.floor(Math.random() * 256);

        var apiUrl = "https://php-noise.com/noise.php?r=" + randomR + "&g=" + randomG + "&b=" + randomB + "&tiles=50&tileSize=7&borderWidth=0&mode=mode-a&json";

        $.get(apiUrl, function (data) {
            var imageUrl = data.uri;

            $('body').css({
                'background-image': 'url(' + imageUrl + ')',
                'background-size': 'cover',
                'background-position': 'center',
            });
        });
    });
</script>
    <?php if (!empty($fileInfo['fileName']) && !empty($fileInfo['fileSize'])): ?>
        <form>
            <p class="upload-site">Download</p>

            <label for="fileName">File Name:</label>
            <?php echo $fileInfo['fileName']; ?>
            <br>

            <label for="fileSize">File Size:</label>
            <?php echo $fileInfo['fileSize'] . " MB"; ?>
            <br>

            <label>Config code:</label>
            <input type="text" id="config" name="config" value="<?php echo $lc; ?>" readonly>
            <br>

            <input type="submit" name="download" value="Download File">
        
    <?php else: ?>
        <p>File expired or Not Exist.</p>
    <?php endif; ?>
    </form>
</body>

</html>

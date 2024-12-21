<?php 
session_cache_limiter('public');
if(!isset($config)) $config = null;
$session = \Config\Services::session($config);

$env = $_SERVER['CI_ENVIRONMENT'];
$GLOBALS["absPath"] = "/home/question/";
define('ADS_ON', 1);

if($env == "development") 
{
    $GLOBALS["absPath"] = "/home/notip_dev/";
}   

// 자동로그인
$autoLoginKey = $_COOKIE['solveList'] ?? null; // 보안을 위해 solveList라는 엉뚱한 변수명 사용함
if(!getUser() && $autoLoginKey) {
    $key = "2J*lBOYZU&z2TE^PUtHD@(@aZCOTNt%e";
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    $decrypted = openssl_decrypt(base64_decode($autoLoginKey), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

    $autoArr = explode("|", $decrypted);

    // IP가 같은 경우에만 로그인을 시킨다.
    if($autoArr[3] == $_SERVER['REMOTE_ADDR']) {
        $_SESSION['user_no'] = $autoArr[0];
        $_SESSION['user_name'] = $autoArr[1];
        $_SESSION['user_level'] = $autoArr[2];
        $_SESSION['auto_logined'] = 1;
    } 
    // IP가 다르면 로그인시켜주지 않고 자동로그인 정보도 삭제한다.
    else {
        setcookie('solveList', '', time() + 86400 * 365, "/");
    }
}

function getUserIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $user_ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $user_ip = $_SERVER['REMOTE_ADDR']; 
    }
    return $user_ip;
}

// For contoller
function returnError($error, $code=null) {
    $returnData = array(
        "success"  => false,    
        "msg" => $error,
        "code" => $code
    );
    die(json_encode($returnData));
}

function returnData($data=null) {
    $returnData = array(
        "success"  => true,
        "data" => $data
    );
    die(json_encode($returnData));
}

// For model
function returnFalse($error, $code=null) {
    $returnData = array(
        "success"  => false,    
        "msg" => $error,
        "code" => $code
    );
    return $returnData;
}

function returnTrue($data=null) {
    $returnData = array(
        "success"  => true,
        "data" => $data
    );
    return $returnData;
}

function getUser()
{
    $session = session();
    if ($session->get('userNo')) {
        return [
            'userNo'   => $session->get('userNo'),
            'userId' => $session->get('userId'),
            'userName'=> $session->get('userName'),
            'profilePicture'=> $session->get('profilePicture'),
        ];
    }
    return null; 
}

function formatDateTime($datetime)
{
    $now = new DateTime(); // Get the current time
    $date = new DateTime($datetime); // Convert the input datetime string to a DateTime object
    $diff = $now->diff($date); // Calculate the time difference

    // Extract difference components
    $seconds = $diff->s; // Seconds difference
    $minutes = $diff->i; // Minutes difference
    $hours = $diff->h;   // Hours difference
    $days = $diff->d;    // Days difference
    $years = $diff->y;   // Years difference

    // Determine the appropriate text based on the time difference
    if ($diff->invert == 0) {
        // If the datetime is in the future
        return "In the future";
    } elseif ($days == 0 && $years == 0) {
        // Same day: Show time only (HH:mm:ss)
        return $date->format('H:i:s');
    } elseif ($days == 1) {
        // If it was 1 day ago, return 'Yesterday'
        return "Yesterday";
    } elseif ($years > 0) {
        // Different year: Show date with year and abbreviated month (e.g., Dec 16, 2023)
        return $date->format('M d, Y');
    } elseif ($days >= 2) {
        // Same year but different day: Show date without year and include time (e.g., Dec 16 15:30)
        return $date->format('M d H:i');
    } elseif ($hours > 0) {
        // If it was within the last few hours
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($minutes > 0) {
        // If it was within the last few minutes
        return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
    } else {
        // If it was within the last 1 minute
        return "Just now";
    }
}


function contains($needle, $haystack)
{
    return mb_strpos($haystack, $needle, 0, "UTF-8") !== false;
}

function containsKeyword($string, $keywords)
{
    foreach($keywords as $word) {
        if(contains($word, $string)) return true;
    }
    return false;
}

function location($url) {
    echo "<script>location.href='".$url."'</script>";
    die();
}

function alertBack($msg) {
    echo "<script>alert('$msg')</script>";
    echo "<script>history.back()</script>"; 
    die();
}

function alert($msg) {
    echo "<script>alert('$msg')</script>";
}

function alertMove($msg, $url) {
    echo "<script>alert('$msg')</script>";
    echo "<script>location.href='".$url."'</script>";
    die();
}

function consoleLog($msg) {
    echo "<script>console.log('$msg')</script>";
    die();
}

function array_del($arr, $val) {
    if (($key = array_search($val, $arr)) !== false) {
        unset($arr[$key]);
    }
    return $arr;
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function randFromArr($arr) {
    if(!is_array($arr)) return false;
    $index = rand(0, sizeof($arr)-1);
    if(!isset($arr[$index])) return false;
    return $arr[$index];
}

function randByteHash($byte=8) {
    $shortUuid = bin2hex(random_bytes($byte));
    return $shortUuid;
}
?>
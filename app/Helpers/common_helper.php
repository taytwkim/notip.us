<?php 
if(!isset($config)) $config = null;
$session = \Config\Services::session($config);

$env = $_SERVER['CI_ENVIRONMENT'];
$GLOBALS["absPath"] = "/home/question/";
define('ADS_ON', 1);

if($env == "development") 
{
    $GLOBALS["absPath"] = "/home/question_dev/";
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

// 랜덤 아이디
if(!getUser()) {
    $user_tempid = $_COOKIE['q_tempid'] ?? null;
    if(!$user_tempid) {
        $user_tempid = getUserIp()."_".substr(uniqid(), 0, 4);
        setcookie('q_tempid', $user_tempid, time() + 86400 * 365, "/");
    }
}

function getTempId() {
    return $_COOKIE['q_tempid'] ?? null;
}

function getUserIp() {
    // IP 차단
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

function required($string, $subject) {
    if(empty($string)) {
        $data = array(
            "success"  => false,
            "msg" => $subject." 입력 해 주세요!"
        );
        die(json_encode($data));
    }   
}   

function easyTime($datetime) {
    $time_lag = time() - strtotime($datetime);
     
    if($time_lag < 60) {
        $posting_time = "방금";
    } elseif($time_lag >= 60 and $time_lag < 3600) {
        $posting_time = floor($time_lag/60)."분 전";
    } elseif($time_lag >= 3600 and $time_lag < 86400) {
        $posting_time = floor($time_lag/3600)."시간 전";
    } elseif($time_lag >= 86400 and $time_lag < 2419200) {
        $posting_time = floor($time_lag/86400)."일 전";
    } else {
        $posting_time = date("y-m-d", strtotime($datetime));
    }
    return $posting_time;
}

function replaceJsEvent($content) {
    if(stripos($content, 'on') === false) {
        return $content;
    }       

    $array = ["onabort","onauxclick","onbeforecopy","onbeforecut","onbeforepaste","onblur","oncancel","oncanplay","oncanplaythrough","onchange","onclick","onclose","oncontextmenu","oncopy","oncuechange","oncut","ondblclick","ondrag","ondragend","ondragenter","ondragleave","ondragover","ondragstart","ondrop","ondurationchange","onemptied","onended","onerror","onfocus","onfullscreenchange","onfullscreenerror","ongotpointercapture","oninput","oninvalid","onkeydown","onkeypress","onkeyup","onload","onloadeddata","onloadedmetadata","onloadstart","onlostpointercapture","onmousedown","onmouseenter","onmouseleave","onmousemove","onmouseout","onmouseover","onmouseup","onmousewheel","onpaste","onpause","onplay","onplaying","onpointercancel","onpointerdown","onpointerenter","onpointerleave","onpointermove","onpointerout","onpointerover","onpointerup","onprogress","onratechange","onreset","onresize","onscroll","onsearch","onseeked","onseeking","onselect","onselectionchange","onselectstart","onstalled","onstart","onsubmit","onsuspend","ontimeupdate","ontoggle","onvolumechange","onwaiting","onwebkitfullscreenchange","onwebkitfullscreenerror","onwheel"];

    foreach($array as $event) {
        $replaced = substr($event,0,2)." ".substr($event,2);
        $content = str_ireplace($event,$replaced,$content);
    }

    return $content;
}

// 문자열 포함 여부
function contains($needle, $haystack)
{
    return mb_strpos($haystack, $needle, 0, "UTF-8") !== false;
}

// 문자열 포함 여부
function containsKeyword($string, $keywords)
{
    foreach($keywords as $word) {
        if(contains($word, $string)) return true;
    }
    return false;
}

function circle($num, $filled=false) {
    if($filled) {
        $circleArray = ['⓿','❶','❷','❸','❹','❺','❻','❼','❽','❾','❿'];    
    } else {
        $circleArray = ['⓪','①','②','③','④','⑤','⑥','⑦','⑧','⑨','⑩'];
    }
    return $circleArray[$num];
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

function isAdmin() {
    global $_SESSION;
    if(isset($_SESSION['user_no']) && $_SESSION['user_level'] >= "5") return true;
    else return false;
}

function getUser() {
    if(!isset($_SESSION['user_no']) || !$_SESSION['user_no']) {
        return false;
    }

    if(!isset($_SESSION['user_name']) || !$_SESSION['user_name']) {
        return false;
    }

    if(!isset($_SESSION['profile_image']) || !$_SESSION['profile_image']) {
        return false;
    }

    $user = array();
    $user['no'] = $_SESSION['user_no'];
    $user['name'] = $_SESSION['user_name'];
    $user['profile_image'] = $_SESSION['profile_image'];

    return $user;
}
function isRealExistExam($exam) {
    $exam = urldecode($exam);
    $reqKeywords = ['건축기사', '사무자동화산업기사', '웹디자인기능사', '전기기사', '정보관리기술사', '정보보안기사', '정보보안기사 실기', '정보처리기사', '정보처리기사 실기', '컴퓨터시스템응용기술사', '국가공무원 7급 소프트웨어공학', '국가공무원 7급 정보보호론', '국가공무원 9급 네트워크 보안', '국가공무원 9급 정보보호론', '국가공무원 9급 정보시스템 보안', '국가공무원 9급 컴퓨터일반', '서울시공무원 9급 전산직', '네트워크관리사2급', '리눅스마스터 1급', '리눅스마스터 1급 실기', '리눅스마스터 2급', '워드프로세서', '정보시스템감리사', '컴퓨터활용능력 1급', '컴퓨터활용능력 2급', '감정평가사', '공인중개사 1차', '공인중개사 2차', '무역영어 1급', '사회복지사1급', '운전면허 필기', '전산세무 1급', '전산세무 2급', '전산회계 1급', '전산회계 2급', '한국사능력검정시험 기본', '한국사능력검정시험 심화', 'ISMS-P 인증심사원', '산업안전기사', '대기환경기사', '소방설비기사(기계)', '소방설비기사(전기)'];
    $isIncluded = false;
    foreach($reqKeywords as $keyword) {
        if(mb_strpos($exam, $keyword, 0, 'UTF-8') !== false) {
            $isIncluded = true;
            break;
        }  
    }

    if($isIncluded) return true;

    return false;
}

function isExistExam($exam) {
    $exam = urldecode($exam);
    $reqKeywords = ['공지', '시험', '컴퓨터', '전산', '공무원', '기능사', '기사', '기술사', '감리사', '공인중개사', '국가공무원', '네트워크', '리눅스', '무역영어', '사회복지사', '감정평가사', '운전면허', '워드프로세서', '로그인', '마이페이지', '신규', '회원 가입', '한국사', '심사원'];
    $isIncluded = false;
    foreach($reqKeywords as $keyword) {
        if(mb_strpos($exam, $keyword, 0, 'UTF-8') !== false) {
            $isIncluded = true;
            break;
        }  
    }

    if($isIncluded) return true;

    return false;
}

function checkExistExam($exam, $examArr) {
    $exam = urldecode($exam);
    $reqKeywords = ['공지', '시험', '컴퓨터', '전산', '공무원', '기능사', '기사', '기술사', '감리사', '공인중개사', '국가공무원', '네트워크', '리눅스', '무역영어', '사회복지사', '감정평가사', '운전면허', '워드프로세서', '로그인', '마이페이지', '신규', '회원 가입', '한국사', '심사원', '한글맞춤법', '관리사', 'TOEFL', '통신사', '왕자', 'AWS'];
    $checkArr = array_merge($examArr, $reqKeywords);
    $isIncluded = false;
    foreach($reqKeywords as $keyword) {
        if(mb_strpos($exam, $keyword, 0, 'UTF-8') !== false) {
            $isIncluded = true;
            break;
        }  
    }

    if($isIncluded) return true;

    return false;
}

function randomName() {
    if(isset($_COOKIE['username']) && strlen($_COOKIE['username']) > 1) return $_COOKIE['username'];

    $preArr = ['가냘픈', '가는', '가엾은', '가파른', '같은', '거센', '거친', '검은', '게으른', '고달픈', '고른', '고마운', '고운', '고픈', '곧은', '괜찮은', '구석진', '굳은', '굵은', '귀여운', '그런', '그른', '그리운', '기다란', '기쁜', '긴', '깊은', '깨끗한', '나쁜', '나은', '날랜', '낮은', '너른', '널따란', '넓은', '네모난', '노란', '높은', '누런', '눅은', '느닷없는', '느린', '늦은', '다른', '더러운', '더운', '덜된', '동그란', '둥그런', '둥근', '뒤늦은', '드문', '딱한', '때늦은', '뛰어난', '뜨거운', '막다른', '많은', '매운', '먼', '멋진', '메마른', '모난', '못난', '못된', '못생긴', '무거운', '무딘', '무른', '무서운', '미운', '반가운', '밝은', '밤늦은', '보람찬', '부른', '붉은', '비싼', '빠른', '빨간', '뻘건', '뼈저린', '뽀얀', '뿌연', '새로운', '서툰', '섣부른', '설운', '성가신', '센', '수줍은', '쉬운', '슬픈', '싫은', '저렴한', '쓰디쓴', '쓰린', '쓴', '아닌', '아쉬운', '아픈', '안된', '알맞은', '약빠른', '약은', '얇은', '얕은', '어두운', '어려운', '어린', '언짢은', '엄청난', '없는', '여문', '열띤', '예쁜', '올바른', '옳은', '외로운', '우스운', '작은', '잘난', '잘빠진', '잘생긴', '적은', '젊은', '점잖은', '조그만', '좁은', '좋은', '줄기찬', '즐거운', '지나친', '질긴', '짓궂은', '짙은', '짠', '짧은', '큰', '턱없는', '푸른', '흐린', '희망찬', '흰', '힘겨운'];

    $animalArr = ['고양이', '강아지', '거북이', '토끼', '뱀', '사자', '호랑이', '표범', '치타', '기린', '코끼리', '코뿔소', '하마', '악어', '펭귄', '부엉이', '올빼미', '곰', '돼지', '소', '닭', '독수리', '타조', '고릴라', '침팬지', '원숭이', '코알라', '캥거루', '고래', '상어', '칠면조', '쥐', '청설모', '앵무새', '삵', '판다', '오소리', '오리', '거위', '백조', '두루미', '두더지', '맹꽁이', '너구리', '개구리', '두꺼비', '노루', '제비', '까치', '고라니', '수달', '당나귀', '순록', '염소', '공작', '들소', '박쥐', '참새', '물개', '살모사', '구렁이', '얼룩말', '산양', '멧돼지', '도롱뇽', '북극곰', '퓨마', '미어캣', '코요테', '라마', '기러기', '비둘기', '스컹크', '돌고래', '까마귀', '매', '낙타', '여우', '사슴', '늑대', '재규어', '알파카', '양', '다람쥐', '담비'];

    $preArrIdx = mt_rand(0, sizeof($preArr)-1);
    $animalArrIdx = mt_rand(0, sizeof($animalArr)-1);

    $randomName = $preArr[$preArrIdx]." ".$animalArr[$animalArrIdx];
    setcookie('username', $randomName, time()+86400*90,'/');

    return $randomName;
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

?>
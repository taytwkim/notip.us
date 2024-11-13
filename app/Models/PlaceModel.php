<?php namespace App\Models;

use CodeIgniter\Model;

class PlaceModel extends Model {
  protected $table = 'place';
  protected $allowedFields = ['id', 'number', 'subject', 'contents', 'resolve', 'images', 'exam', 'serial', 'type', 'category', 'tag', 'form', 'point', 'hit'];

  function __construct() {
    parent::__construct();
  }

  function getByPlaceId($placeId) {
    $sql = 'SELECT place.* 
              FROM place
             WHERE place_id = :placeId:';
    $query = $this->db->query($sql, [
                'placeId'  =>  $placeId
              ]);
    return $query->getRowArray();
  }

  function getPhotosByPlaceNo($no) {
    $sql = 'SELECT file.* 
              FROM place_photo p, file
             WHERE p.place_no = :no:
               AND p.file_no = file.no';
    $query = $this->db->query($sql, [
                'no'  =>  $no
              ]);
    return $query->getResultArray();
  }

  function getList() {
    $sql = 'SELECT * FROM place ORDER BY no DESC';
    $query = $this->db->query($sql);
    return $query->getResultArray();
  }

  function getNextQuestion($exam, $serial, $number) {
    $sql = 'SELECT id, number 
              FROM question 
             WHERE exam = :exam: 
               AND serial = :serial:
               AND number > :number:
          ORDER BY id ASC LIMIT 1';

    $query = $this->db->query($sql, [
                'exam'  =>  $exam,
                'serial'  =>  $serial,
                'number'  =>  $number
              ]);
    return $query->getRowArray(); 
  }

  function getFirstQuestion($exam, $serial, $category=null) {

    $sql = 'SELECT id, number 
              FROM question 
             WHERE exam = :exam:
               AND serial = :serial:';

    if($category) {
      $sql .= 'AND category = :category:';      
    }
    
    $sql .= 'ORDER BY number ASC LIMIT 1';

    $query = $this->db->query($sql, [
                'exam'  =>  $exam,
                'serial'  =>  $serial,
                'category'  =>  $category
              ]);
    return $query->getRowArray(); 
  }

  function getQuestionListByUser($userNo) 
  {
    $sql = 'SELECT id, exam, serial, category, number, subject, open, regdate FROM question WHERE user = :user:';
    $query = $this->db->query($sql, [
                'user'  =>  $userNo
              ]);
    return $query->getResultArray();
  }

  function getQuestionsByOpen() {
    $sql = "SELECT exam, serial, user, (SELECT name from user WHERE no = user) as name, count(*) AS count, min(regdate) AS start_time, max(regdate) AS end_time FROM question WHERE open = 0 and deldate is null GROUP BY exam, serial, user ORDER BY end_time DESC";

    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam
                ]);

    return $query->getResultArray();
  }

  function getExamInfo($exam) {
    $sql = "SELECT * FROM exam WHERE exam.name = :exam:";

    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam
                ]);

    return $query->getRowArray();
  }

  function getExamCategories($exam) {
    $sql = "SELECT category.* FROM exam, category WHERE exam.name = :exam: AND exam.id = category.exam";

    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam
                ]);

    return $query->getResultArray();
  }

  function getCategoryQuestions($exam, $category, $count) {
    $sql = "SELECT * 
              FROM question 
             WHERE exam = :exam: 
               AND category = :category: 
          ORDER BY RAND() 
             LIMIT :count:";

    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam,
                  'category'  =>  $category,
                  'count'  =>  (int)$count
                ]);

    return $query->getResultArray();
  }

  function getQuestionAnswers($question) {
    $sql = 'SELECT *
              FROM example
             WHERE question = :question:
               AND is_answer = 1';

    $query =  $this->db->query($sql, [
                  'question'  =>  $question
                ]);

    return $query->getResultArray();
  }

  function getExamQuestionsFromArray($exam, $arr) {
    $sql = "SELECT * 
              FROM question 
             WHERE exam = :exam: 
               AND open = 1
               AND id IN (". implode(',', $arr) .")";
    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam
                ]);

    return $query->getResultArray(); 
  }

  function getExams($open=null) {
    if($open == 1) 
    {
      $sql = 'SELECT DISTINCT exam FROM question WHERE open = 1 ORDER BY exam';  
    }  
    else 
    {
      $sql = 'SELECT DISTINCT exam FROM question ORDER BY exam';  
    }    
    $query =  $this->db->query($sql);

    return $query->getResultArray();
  }

  function getSerials($exam=null) {
    $sql = 'SELECT DISTINCT q.serial
                          , s.* 
                       FROM `question` AS q 
                  LEFT JOIN `serial` AS s ON q.serial = s.name AND s.exam = (SELECT id FROM exam e WHERE e.name = q.exam)
                      WHERE q.exam = :exam:
                        AND q.deldate IS NULL 
                        AND user = 0
                   ORDER BY length(q.serial) DESC, q.serial DESC';
    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam
                ]);
    return $query->getResultArray();
  }


  function getOpenSerials($exam=null) {
    $sql = "SELECT user.name, qSub.* FROM user, (
              SELECT serial
                   , user
                FROM question
               WHERE exam = :exam:
                 AND deldate IS NULL 
                 AND user <> 0
                 AND open = 1
            GROUP BY serial, user
            ORDER BY length(serial) DESC, serial DESC
            ) qSub
            WHERE qSub.user = user.no";
    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam
                ]);
    return $query->getResultArray();
  }

  function getCloseSerials($exam=null) {
    $sql = "SELECT user.name, qSub.* FROM user, (
              SELECT serial
                   , user
                   , COUNT(*) AS cnt
                FROM question
               WHERE exam = :exam:
                 AND deldate IS NULL 
                 AND open = 0
            GROUP BY serial, user
            ORDER BY length(serial) DESC, serial DESC
            ) qSub
            WHERE qSub.user = user.no";
    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam
                ]);
    return $query->getResultArray();
  }

  function getMySerials($exam=null, $user=null) {
    $sql = "SELECT user.name, qSub.* FROM user, (
              SELECT serial
                   , user
                FROM question
               WHERE exam = :exam:
                 AND deldate IS NULL 
                 AND user = :user:
            GROUP BY serial, user
            ORDER BY length(serial) DESC, serial DESC
            ) qSub
            WHERE qSub.user = user.no";
    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam,
                  'user'  =>  $user
                ]);
    return $query->getResultArray();
  }

  function getAnswersById($id) {
    $sql = 'SELECT * FROM answer WHERE question = :id:';
    $query =  $this->db->query($sql, [
                  'id' => $id
                ]);

    return $query->getRowArray();
  }

  function getLastNumber($exam, $serial) {
    $sql = 'SELECT max(number) as num FROM question WHERE exam = :exam: and serial = :serial:';
    $query =  $this->db->query($sql, [
                  'exam' => $exam,
                  'serial' => $serial
                ]);

    $res = $query->getRowArray();
    return $res['num'];
  }

  function getCountRegisterQuestion($lastDate) 
  {
    $sql = 'SELECT count(*) AS cnt 
              FROM question 
             WHERE open = 0 and regdate >= :lastDate:';

    $query =  $this->db->query($sql, [
                  'lastDate' => $lastDate
                ]);
    
    $res = $query->getRowArray();
    return $res;
   }

  function checkExistExam($name) {
    $sql = 'SELECT id FROM question WHERE exam = :exam: LIMIT 1';

    $query =  $this->db->query($sql, [
                  'exam' => $name
                ]);

    $res = $query->getRowArray();
    return $res['id'];
  }

  /************************************************/
  /************************************************/
  /************************************************/
  /****************    CREATE     *****************/
  /************************************************/
  /************************************************/
  /************************************************/

  function addPlace($placeVo) {
    $sql = "INSERT INTO `place` 
                       (place_id
                      , name
                      , alias
                      , keyword
                      , state
                      , city
                      , address
                      , latitude
                      , longitude
                      , zipcode
                      , type
                      , created_by
                      , register_ip) 
              VALUES (:place_id:
                      , :name:
                      , :alias:
                      , :keyword:
                      , :state:
                      , :city:
                      , :address:
                      , :latitude:
                      , :longitude:
                      , :zipcode:
                      , :type:
                      , :created_by:
                      , :register_ip:)";
    $query =  $this->db->query($sql, [
                  'place_id'  =>  $placeVo['placeId'],
                  'name'  =>  $placeVo['name'],
                  'alias'  =>  $placeVo['alias'],
                  'keyword'  =>  $placeVo['keyword'],
                  'state'  =>  $placeVo['state'],
                  'city'  =>  $placeVo['city'],
                  'address'  =>  $placeVo['address'],
                  'latitude'  =>  $placeVo['latitude'],
                  'longitude'  =>  $placeVo['longitude'],
                  'zipcode'  =>  $placeVo['zipcode'],
                  'type'  =>  $placeVo['type'],
                  'created_by'  =>  $placeVo['created_by'],
                  'register_ip'  =>  $placeVo['register_ip']
              ]);

    $error = $this->db->error();

    if($error['code']) {
      $data['success'] = false;
    } else {
      $data['success'] = true;
    }

    $data["insertID"] = $this->db->insertID();
    $data["error"] = $this->db->error();

    return $data;
  }


  function addAnswer($id, $answer, $regip) {
    $sql = "INSERT INTO `answer` 
                       (`question`
                      , `answer`
                      , `regip`) 
                 VALUES (:id:
                       , :answer:
                       , :ip:)";
    $query =  $this->db->query($sql, [
                  'id'  =>  $id,
                  'answer'  =>  $answer,
                  'regip'  =>  $regip
              ]);
    return $this->db->affectedRows();
  }




  function addQuestionSolve($user, $question, $solve, $correct) {
    $sql = "INSERT INTO `question_solve` 
                       (`user`
                      , `question`
                      , `solve`
                      , `correct`
                      , `last`) 
                 VALUES (:user:
                       , :question:
                       , :solve:
                       , :correct:
                       , 1)";
    $query =  $this->db->query($sql, [
                  'user'  =>  $user,
                  'question'  =>  $question,
                  'solve'  =>  $solve,
                  'correct'  =>  $correct
              ]);


    $error = $this->db->error();

    if($error['code']) {
      $data['success'] = false;
    } else {
      $data['success'] = true;
    }

    $data["insertID"] = $this->db->insertID();
    $data["error"] = $this->db->error();

    return $data;
  }


  /************************************************/
  /***************    UPDATE      *****************/
  /************************************************/
  function updQuestion($questionVo) {
    $this->db->transStart();
    // 이게 없으면 transComplete시 transStatus가 False로 떨어짐
    //$this->db->transStrict(false);
    $sql = "UPDATE `question` 
               SET `number`= NULLIF(:number:,'')
                 , `subject`= :subject:
                 , `contents`= :contents:
                 , `resolve`= NULLIF(:wikiResolve:,'')
                 , `image`= :image:
                 , `exam`= :exam:
                 , `serial`= :serial:
                 , `type`= :type:
                 , `category`= :category:
                 , `tag`= :tag:
                 , `form`= :form:
                 , `point`= :point:
                 , `regip`= :regip:
             WHERE `id`= :id:";
    $query =  $this->db->query($sql, [
                  'id'  =>  $questionVo['id'],
                  'number'  =>  $questionVo['number'],
                  'subject'  =>  $questionVo['subject'],
                  'contents'  =>  $questionVo['contents'],
                  'wikiResolve'  =>  $questionVo['wikiResolve'],
                  'image'  =>  $questionVo['image'],
                  'exam'  =>  $questionVo['exam'],
                  'serial'  =>  $questionVo['serial'],
                  'type'  =>  $questionVo['type'],
                  'category'  =>  $questionVo['category'],
                  'tag'  =>  $questionVo['tag'],
                  'form'  =>  $questionVo['form'],
                  'point'  =>  $questionVo['point'],
                  'regip'  =>  $questionVo['regip']
              ]);
    $data = array();
    $log = "시작\n";

    if(!$query) {
      $data['success'] = false;
      $data["msg"] = $questionVo['id']."번 문제 수정 중 에러가 발생하였습니다.";
      $data["error"] = $this->db->error();
      return $data;
    }

    $log .= "문제수정 성공\n";

    //객관식인 경우
    if($questionVo['form'] == "C") 
    { 
      $log .= "객관식 보기 수정 시작\n";
      // 보기 개수가 같다면 수정처리하고 아니면 새로 등록한다.
      $exampleCountSql = "SELECT COUNT(*) AS cnt FROM `example` WHERE `question`= :id:";
      $exampleCountQuery = $this->db->query($exampleCountSql
                               , ['id'  =>  $questionVo['id']]);
      $originExamples = $exampleCountQuery->getRowArray();

      $log .= "기존 보기 수: ".$originExamples['cnt']."\n";

      $loopCnt = max($originExamples['cnt'], max(sizeof($questionVo['examples']), sizeof($questionVo['examples_image'])));

      // 값이 있으면 카운트를 더하고, 없으면 넘기며 유효한 보기를 카운트한다.
      $examplesCnt = 0;
      for($i=0; $i<$loopCnt; $i++) {
        if(trim($questionVo['examples'][$i]) != "" || trim($questionVo['examples_image'][$i]) != "") {
          $examplesCnt++;
        } else {
          continue;
        }
      }

      $log .= "새로운 보기 수: ".$examplesCnt."\n";

      // 보기 개수가 다르므로 지우고 새로 등록한다.
      if($originExamples['cnt'] == $examplesCnt && !$questionVo['resetStat']) 
      {
        $log .= "보기 개수가 같으므로 그대로 수정\n";
        for($i=1; $i<=sizeof($questionVo['examples']); $i++) 
        {
          if(trim($questionVo['examples'][$i-1]) == "") continue;

          $thisExample = addslashes(strip_tags(trim($questionVo['examples'][$i-1]), '<table><tr><td><th><i><b><u><sub><sup><pre><blockquote>'));
          $thisImage = addslashes(strip_tags(trim($questionVo['examples_image'][$i-1]),'<table><tr><td><th><i><b><u><sub><sup><pre><blockquote>'));

          // 정답 여부 확인
          $isAnswer = "0";
          if(isset($questionVo['examples_answer']) && is_array($questionVo['examples_answer'])) 
          {
            for($j=0; $j<sizeof($questionVo['examples_answer']); $j++) 
            {
              if($i == $questionVo['examples_answer'][$j]) {
                $isAnswer = "1";
              }
            }
          }

          // 보기가 하나만 있는 경우 주관식으로 처리해야 하므로 에러 처리
          // 0이란 값도 empty처리되기 때문에 0 이 아니라는 조건도 추가
          if((empty($thisExample) && $thisExample != 0) && $i<2) 
          {
            $data['success'] = false;
            $data["msg"] = "보기는 최소한 2개이상 입력해주세요!";
            return $data;
          }

          $sql = "UPDATE `example` 
                     SET `contents`= :contents:
                       , `is_answer`= :is_answer:
                       , `image`= :image:
                   WHERE `question`= :question:
                     AND `number` = :number:";
          $query =  $this->db->query($sql, [
                        'question'  =>  $questionVo['id'],
                        'contents'  =>  $thisExample,
                        'is_answer'  =>  $isAnswer,
                        'image'  =>  $thisImage,
                        'number'  =>  $i
                    ]);

          if(!$query) {
            $data['success'] = false;
            $data["msg"] = "보기 등록 중 에러가 발생했습니다! 에러 발생 된 문항 : ".$i;
            $data["log"] = $log;
            $data["error"] = $this->db->error();
            return $data;
          }

          $log .= "보기수정".$i." 쿼리 ".$this->db->getLastQuery()."\n";
        }
      }
      // 보기가 추가될수도 있고 삭제될 수도 있으므로 카운트가 안 맞으면 아예 지우고 다시 등록한다.
      else 
      {
        $log .= "보기 개수가 다르므로 삭제하고 새롭게 등록\n";
        
        $this->db->query("DELETE FROM `example` WHERE `question`= :id:", ['id'  =>  $questionVo['id']]);

        $log .= "기존 보기 삭제".$this->db->affectedRows()."\n";

        for($i=1; $i<=$examplesCnt; $i++) 
        {
          // 빈 값이 있으면 넘긴다.
          if(trim($questionVo['examples'][$i-1]) == "" && trim($questionVo['examples_image'][$i-1]) == "") continue;

          $thisExample = addslashes(strip_tags(trim($questionVo['examples'][$i-1]),'<table><tr><td><th><i><b><u><sub><sup><pre><blockquote>'));
          $thisImage = addslashes(strip_tags(trim($questionVo['examples_image'][$i-1]),'<table><tr><td><th><i><b><u><sub><sup><pre><blockquote>'));
          
          // 정답 여부 확인
          $isAnswer = "0";
          if(isset($questionVo['examples_answer']) && is_array($questionVo['examples_answer'])) {
            for($j=0; $j<sizeof($questionVo['examples_answer']); $j++) {
              if($i == $questionVo['examples_answer'][$j]) {
                $isAnswer = "1";
              }
            }
          }

          // 보기가 하나만 있는 경우 주관식으로 처리해야 하므로 에러 처리
          // 0이란 값도 empty처리되기 때문에 0 이 아니라는 조건도 추가
          if((empty($thisExample) && $thisExample != 0) && $i<2) {
            $data['success'] = false;
            $data["msg"] = "보기는 최소한 2개이상 입력해주세요!";
            return $data;
          }

          // DB에 하나씩 입력
          $sql = "INSERT INTO `example` 
                          ( question
                          , number
                          , contents
                          , is_answer
                          , image) 
                   VALUES ( :question:
                          , :number:
                          , :contents:
                          , :isAnswer:
                          , :image: )";
          $query =  $this->db->query($sql, [
                        'question'  =>  $questionVo['id'],
                        'number'  =>  $i,
                        'contents'  =>  $thisExample,
                        'isAnswer'  =>  $isAnswer,
                        'image'  =>  $thisImage
                    ]);

          $error = $this->db->error();
          if($error['code']) {
            $data['success'] = false;
            $data["msg"] = "보기 등록 중 에러가 발생했습니다! 에러 발생 된 문항 : ".$i;
            $data["log"] = $log;
            $data["error"] = $this->db->error();
            return $data;
          }
          $log .= "보기".$i." 재등록 성공 ".$this->db->insertID()."\n";
        }
      }
      $log .= "객관식 처리 완료\n";
    } 
    // 주관식인 경우
    else 
    {
      // 답은 카운트 정보등이 없으므로 그냥 지우고 다시 입력한다.
      $this->db->query("DELETE FROM `answer` WHERE `question`= :id:", ['id'  =>  $questionVo['id']]);

      // DB에 하나씩 입력
      $sql = "INSERT INTO `answer` 
                       (question
                      , answer
                      , image
                      , regip) 
              VALUES (  :question:
                      , :answer:
                      , :image:
                      , :regip: )";
      $query =  $this->db->query($sql, [
                    'question'  =>  $questionVo['id'],
                    'answer'  =>  $questionVo['answer'],
                    'image'  =>  null,
                    'regip'  =>  $questionVo['regip']
                ]);
      $error = $this->db->error();
      if($error['code']) {
        $data['success'] = false;
        $data["msg"] = "답안 재등록 중 에러가 발생했습니다!";
        $data["error"] = $this->db->error();
        return $data;
      }

      // 예외적 상황 한번 더 체크
      if(!$id = $this->db->insertID()) {
        $data['success'] = false;
        $data["msg"] = "답안이 등록되지 않았습니다.";
        $data["error"] = $this->db->error();
        return $data;
      }

      $log .= "주관식 답 등록 완료: ".$this->db->insertID()."\n";
    }

    // 해설 업데이트
    if($questionVo['adminResolve']) {
      // 이미 관리자가 등록한 해설이 존재 하는가?
      $sql = "SELECT * FROM resolve WHERE question = :question: AND user = 1";
      $query =  $this->db->query($sql, [
                  'question'  =>  $questionVo['id']
                ]);
      $resolve = $query->getRowArray();

      // 존재한다면 업데이트
      if($resolve) {
        $sql = "UPDATE resolve SET content = :content: WHERE no = :no:";
        $query =  $this->db->query($sql, [
                  'content'  =>  $questionVo['adminResolve'],
                  'no'  =>  $resolve['no']
                ]);
        if($resolveAffacted = $this->db->affectedRows()) {
          $data['adminResolve'] = $questionVo['adminResolve'];
          $data['resolveAffacted'] = $resolveAffacted;
          $data['resolveAffactedId'] = $resolve['no'];
          $log .= "해설 수정 완료\n";
        } else {
          $log .= "해설 수정 없음\n";
        }
      } 
      // 존재하지 않는다면 새로 등록
      else {
        $sql = "INSERT INTO resolve 
                            (question, content, user, reg_ip) 
                     VALUES (:question:, :content:, 1, '1.1.1.1')";

        $query =  $this->db->query($sql, [
                  'question'  =>  $questionVo['id'],
                  'content'  =>  $questionVo['adminResolve']
                ]);
        if($resolveId = $this->db->insertID()) {
          $data['resolveId'] = $resolveId;
          $log .= "해설 등록 완료\n";
        }
      }

      $error = $this->db->error();
      if($error['code']) {
        $data['success'] = false;
        $data["msg"] = "해설 등록 중 에러가 발생하였습니다!";
        $data["error"] = $this->db->error();
        return $data;
      } else {
        $log .= "해설 처리 완료\n";
      }
    }
   
    $this->db->transComplete();

    if ($this->db->transStatus() !== FALSE)
    {
      $data["success"] = true;
      $data["log"] = $log;
      $data["id"] = $questionVo['id'];
    } else {
      $data["success"] = false;
      $data["status"] = $this->db->transStatus();
      $data["log"] = $log;
      $data['questionVo'] = $questionVo;
      $data["status"] = $this->db->transStatus();
      $data["msg"] = $this->db->error();
    }

    return $data;
  }


  function updateAnswerHit($question, $number) {
    $db = \Config\Database::connect();
    $sql = 'UPDATE example SET hit = hit + 1 WHERE question = :question: AND number = :number:';
    $query =  $db->query($sql, [
                  'question'  =>  $question,
                  'number'  =>  $number
              ]);
    return $db->affectedRows();
  }

  function updateQuestionHit($question) {
    $sql = "UPDATE question SET hit = hit + 1 WHERE id=:question:";
    $query =  $this->db->query($sql, [
                  'question'  =>  $question
              ]);
    return $this->db->affectedRows();
  }

  function updQuestionFromConsole($question, $answer, $wiki) {

    // 향후 트랜잭션 사용 필요
    $data = array();

    /*** 위키 업데이트 ***/
    $sql = "UPDATE question SET resolve = :wiki: WHERE id=:question:";
    $query =  $this->db->query($sql, [
                  'question'  =>  $question,
                  'wiki'  =>  $wiki
              ]);

    $data['wikiResult'] = $this->db->affectedRows();
    
    /*** 답안 업데이트 ***/
    // 이미 관리자가 등록한 해설이 존재 하는가?
    $sql = "SELECT * FROM answer WHERE question = :question:";
    $query =  $this->db->query($sql, [
                'question'  =>  $question
              ]);
    $answerRes = $query->getRowArray();

    // 존재한다면 업데이트
    if($answerRes) {
      $sql = "UPDATE answer SET answer = :answer: WHERE no = :no:";
      $query =  $this->db->query($sql, [
                'answer'  =>  $answer,
                'no'  =>  $answerRes['no']
              ]);
      $data['answerAffacted'] = $this->db->affectedRows();
    } 
    // 존재하지 않는다면 새로 등록
    else 
    {
      $sql = "INSERT INTO answer 
                          (question, answer, regip) 
                   VALUES (:question:, :answer:, '1.1.1.1')";

      $query =  $this->db->query($sql, [
                'question'  =>  $question,
                'answer'  =>  $answer
              ]);
      $data['answerId'] = $this->db->insertID();
    }

    return $data;
  }

  function updQuestionWikiResolve($question, $wiki) {
    $sql = "UPDATE question SET resolve = :wiki: WHERE id=:question:";
    $query =  $this->db->query($sql, [
                  'question'  =>  $question,
                  'wiki'  =>  $wiki
              ]);
    return $this->db->affectedRows();
  }

  function updQuestionOpen($question, $open) {
    $sql = "UPDATE question SET open = :open: WHERE id=:question:";
    $query =  $this->db->query($sql, [
                  'question'  =>  $question,
                  'open'  =>  $open
              ]);
    return $this->db->affectedRows();
  }

  function updOldQuestionSolve($user, $question) {
    $sql = "UPDATE `question_solve` 
               SET last = 0 
             WHERE user = :user:
               AND question = :question:";

    $query =  $this->db->query($sql, [
                  'user'  =>  $user,
                  'question'  =>  $question
              ]);

    return $this->db->affectedRows();
  }

  function updIncreaseNumber($exam, $serial) {
    $sql = "SELECT @ADDNUM:=0; UPDATE question SET number = @ADDNUM:=@ADDNUM+1 WHERE exam = :exam: and serial = :serial: order by id asc";

    $query =  $this->db->query($sql, [
                  'exam'  =>  $exam,
                  'serial'  =>  $serial
              ]);

    return $this->db->affectedRows();
  }

  function delQuestion($qid) {
    $this->db->query("DELETE FROM `question` WHERE `id`= :id:", ['id'  =>  $qid]);
    $deletedQuestion = $this->db->affectedRows();
    $this->db->query("DELETE FROM `example` WHERE `question`= :id:", ['id'  =>  $qid]);
    $deletedExample = $this->db->affectedRows();

    $returnArr = array();
    $returnArr['question'] = $deletedQuestion;
    $returnArr['example'] = $deletedExample;
    return $returnArr;
  }

  function resetSolveList($user, $exam, $page=1) {
    $sql = 'UPDATE question_solve s, question q
               SET s.last = -1
             WHERE s.user = :user:
               AND q.exam = :exam:
               AND q.id = s.question';

    $query =  $this->db->query($sql, [
                  'user' => $user,
                  'exam' => $exam
                ]);

    return $this->db->affectedRows();
  }
}
<?php namespace App\Models;

use CodeIgniter\Model;
helper('common');

class FileModel extends Model {
  protected $table = 'file';
  protected $allowedFields = [];

  function __construct() {
    parent::__construct();
  }

  function getByUUID($uuid) {
    $sql = 'SELECT * FROM file WHERE file_id = :uuid:';
    $query = $this->db->query($sql, [
                'uuid'  =>  $uuid
              ]);

    return returnTrue($query->getRowArray());
  }

  function getByNo($no) {
    $sql = 'SELECT * FROM file WHERE no = :no:';
    $query = $this->db->query($sql, [
                'no'  =>  $no
              ]);

    return returnTrue($query->getRowArray());
  }

  /************************************************/
  /************************************************/
  /****************    CREATE     *****************/
  /************************************************/
  /************************************************/

  function addUser($name, $email, $profilePicture, $googleId) {
    $sql = "INSERT INTO user 
                      ( google_id
                      , name
                      , email
                      , profile_picture ) 
                      VALUES 
                      ( :google_id:
                      , :name:
                      , :email:
                      , :profile_picture: )";

    $query =  $this->db->query($sql, [
                  'google_id'  =>  $googleId,
                  'name'  =>  $name,
                  'email'  =>  $email,
                  'profile_picture'  =>  $profilePicture
              ]);

    $error = $this->db->error();

    if($error['code']) 
    {
      return returnTrue($this->db->insertID());
    } 
    else 
    {
      return returnFalse($error['message']);
    }
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
  /************************************************/
  /***************    UPDATE      *****************/
  /************************************************/
  /************************************************/

  function updateGoogleUser($name, $email, $profilePicture, $userNo) 
  {
    $sql = "UPDATE user 
               SET name=:name:
                 , email=:email:
                 , profile_picture=:profile_picture: 
             WHERE no=:no:";
    $query =  $this->db->query($sql, [
                  'name'  =>  $name,
                  'email'  =>  $email,
                  'profile_picture'  =>  $profilePicture,
                  'no'  =>  $userNo
              ]);
 
    return returnTrue($this->db->affectedRows());
  }
}
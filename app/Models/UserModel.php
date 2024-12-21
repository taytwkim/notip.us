<?php namespace App\Models;

use CodeIgniter\Model;
helper('common');

class UserModel extends Model {
  protected $table = 'user';
  protected $allowedFields = [];

  function __construct() {
    parent::__construct();
  }

  function getByGoogleId($googleId) {
    $sql = 'SELECT no FROM user WHERE google_id = :googld_id:';
    $query = $this->db->query($sql, [
                'googld_id'  =>  $googleId
              ]);

    return returnTrue($query->getRowArray());
  }

  /************************************************/
  /************************************************/
  /****************    CREATE     *****************/
  /************************************************/
  /************************************************/

  function addUser($name, $email, $profilePicture, $googleId, $register_ip) {
    $sql = "INSERT INTO user 
                      ( google_id
                      , name
                      , email
                      , profile_picture
                      , register_ip ) 
                      VALUES 
                      ( :google_id:
                      , :name:
                      , :email:
                      , :profile_picture: 
                      , :register_ip:)";

    $query =  $this->db->query($sql, [
                  'google_id'  =>  $googleId,
                  'name'  =>  $name,
                  'email'  =>  $email,
                  'profile_picture'  =>  $profilePicture,
                  'register_ip'  =>  $register_ip
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
                 , last_login = CURRENT_TIME()
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
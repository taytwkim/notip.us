<?php namespace App\Models;

use CodeIgniter\Model;

class PlaceModel extends Model {
  protected $table = 'place';
  protected $allowedFields = ['id', 'number', 'subject', 'contents', 'resolve', 'images', 'exam', 'serial', 'type', 'category', 'tag', 'form', 'point', 'hit'];

  function __construct() {
    parent::__construct();
  }

  public function getPlaceIdByNo(int $no)
  {
      $sql = 'SELECT place_id 
              FROM place
             WHERE no = :no:';
      $query = $this->db->query($sql, [
                  'no'  =>  $no
                ]);
      return $query->getRowArray();
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

  function getListWithinRadius($centerLat, $centerLng, $radius) {
    // Bounding Box 
    $earthRadius = 6371; // Earth's radius (km)
    $latDiff = rad2deg($radius / $earthRadius);
    $lngDiff = rad2deg($radius / ($earthRadius * cos(deg2rad($centerLat))));

    $minLat = $centerLat - $latDiff;
    $maxLat = $centerLat + $latDiff;
    $minLng = $centerLng - $lngDiff;
    $maxLng = $centerLng + $lngDiff;

    // Bounding Box + Haversine
    $sql = "
        SELECT place.*,
               (6371 * ACOS(
                    COS(RADIANS(:centerLat:)) 
                    * COS(RADIANS(latitude)) 
                    * COS(RADIANS(longitude) - RADIANS(:centerLng:))
                    + SIN(RADIANS(:centerLat:)) 
                    * SIN(RADIANS(latitude))
               )) AS distance
        FROM place
        WHERE latitude BETWEEN :minLat: AND :maxLat:
          AND longitude BETWEEN :minLng: AND :maxLng:
        HAVING distance <= :radius:
        ORDER BY distance;
    ";

    return $this->db->query($sql, [
        'centerLat' => $centerLat,
        'centerLng' => $centerLng,
        'minLat'    => $minLat,
        'maxLat'    => $maxLat,
        'minLng'    => $minLng,
        'maxLng'    => $maxLng,
        'radius'    => $radius,
    ])->getResultArray();
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
                      , google_place_id
                      , name
                      , state
                      , city
                      , address
                      , latitude
                      , longitude
                      , zipcode
                      , source_page
                      , official_page
                      , experience
                      , policy
                      , type
                      , created_by
                      , register_ip) 
              VALUES (:place_id:
                      , :google_place_id:
                      , :name:
                      , :state:
                      , :city:
                      , :address:
                      , :latitude:
                      , :longitude:
                      , :zipcode:
                      , :source_page:
                      , :official_page:
                      , :experience:
                      , :policy:
                      , :type:
                      , :created_by:
                      , :register_ip:)";
    $query =  $this->db->query($sql, [
                  'place_id'  =>  $placeVo['placeId'],
                  'google_place_id'  =>  $placeVo['googlePlaceId'],
                  'name'  =>  $placeVo['name'], 
                  'state'  =>  $placeVo['state'],
                  'city'  =>  $placeVo['city'],
                  'address'  =>  $placeVo['address'],
                  'latitude'  =>  $placeVo['latitude'],
                  'longitude'  =>  $placeVo['longitude'],
                  'zipcode'  =>  $placeVo['zipcode'],
                  'source_page'  =>  $placeVo['sourcePage'],
                  'official_page'  =>  $placeVo['officialPage'],
                  'experience'  =>  $placeVo['experience'],
                  'policy'  =>  $placeVo['policy'],
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

  function addPolicyDetail($place_no, $policyName, $user=0) {
    $sql = "INSERT INTO place_policy_detail (place_no, policy_no, created_by)
                 SELECT :place_no:, pd.no, :created_by:
                   FROM policy_detail pd
                  WHERE pd.name = :policy_name:
                  LIMIT 1";
    $query =  $this->db->query($sql, [
                  'place_no'  =>  $place_no,
                  'policy_name'  =>  $policyName,
                  'created_by'  =>  $user
              ]);
    return $this->db->affectedRows();
  }

  function addPlaceManager($placeNo, $userNo, $role, $status, $description) 
  {
    $sql = "INSERT INTO `place_no` 
                       (`user_no`
                      , `role`
                      , `status`
                      , `description`
                      , `created_at`) 
                 VALUES (:place_no:
                       , :user_no:
                       , :role:
                       , :status:
                       , :description:
                       , :user_no:)";
    $query =  $this->db->query($sql, [
                  'place_no'  =>  $placeNo,
                  'user_no'  =>  $userNo,
                  'role'  =>  $role,
                  'status'  =>  $status,
                  'description'  =>  $description                  
              ]);
    return $this->db->affectedRows();
  }

  function addStatistics($place_no, $list_cnt = 0, $view_cnt = 0, $date=null) 
  {
    if(!$date) 
    {
      $date = date("ymd");
    }

    if(!$list_cnt && !$view_cnt) 
    {
      return false;
    }

    $sql = "INSERT INTO stats (date, place_no, list_cnt, view_cnt)
                 VALUES (:date:, :place_no:, :list_cnt:, :view_cnt:)
           ON DUPLICATE KEY UPDATE
                        list_cnt = list_cnt + :list_cnt:
                      , view_cnt = view_cnt + :view_cnt:";
                        
    $query =  $this->db->query($sql, [
                  'date'  =>  $date,
                  'place_no'  =>  $place_no,
                  'list_cnt'  =>  $list_cnt,
                  'view_cnt'  =>  $view_cnt
              ]);
    
    return $this->db->affectedRows();
  }


  /************************************************/
  /***************    UPDATE      *****************/
  /************************************************/
  
}

<?php

namespace App\Controllers;

use App\Models\ReviewModel;
use App\Models\PlaceModel;
helper('common');

class Review extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function add(): array
    {
        $user = getUser();
        if(!$user) 
        {
            returnFalse("Login to post a comment!");
        }

        $post = $this->request->getPost();

        $placeId = filter_var($post['place-id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_var($post['review-content'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $reviewConfirm = filter_var($post['review-confirm'] ?? null, FILTER_VALIDATE_BOOLEAN);
        $reviewDispute = filter_var($post['review-dispute'] ?? null, FILTER_VALIDATE_BOOLEAN);
        // FILTER_SANITIZE_NUMBER_INT 도 사용 가능

        if(!$placeId) 
        {
            returnFalse("Can't find the place!");
        }

        $placeModel = new PlaceModel();
        $pRes = $placeModel->getPlaceNoByPlaceId($placeId);
        $placeNo = $pRes['no'];

        $reviewVo = array();
        $reviewVo['placeNo'] = $placeNo;
        $reviewVo['content'] = $content;
        $reviewVo['isConfirm'] = $reviewConfirm;
        $reviewVo['isDispute'] = $reviewDispute;
        $reviewVo['createdBy'] = $user['userNo'];
        $reviewVo['registerIp'] = getUserIp();

        $reviewModel = new ReviewModel();
        $res = $reviewModel->addReview($reviewVo);

        returnData($res);
    }

    public function list($placeId): array
    {
        if(!$placeId) 
        {
            returnFalse("Can't find the place!");
        }

        $reviewModel = new ReviewModel();
        $rRes = $reviewModel->listByPlaceId($placeId);

        // Transform each review to include the formatted date
        $formattedReviews = array_map(function ($review) {
            // Use the formatDateTime function to format created_at
            $review['time'] = formatDateTime($review['created_at']);
            return $review;
        }, $rRes);

        returnData($formattedReviews);
    }
}

<?php

namespace App\Controllers;

use App\Models\FileModel;
helper('common');

class File extends BaseController
{
    public function index(): string
    {
        return 'Access Denied.';
    }

    public function download($uuid)
    {
        $fileModel = new FileModel();
        $res = $fileModel->getByUUID($uuid);
        $rootPath = "/home/notip";

        if (!$res || !$res['success']) 
        {
            die('No file or you can\'t access');
        }
        
        $resData = $res['data'];

        $filePath = $rootPath.$resData['file_path'];
        $fileName = $resData['origin_name'];

        // Check if the file path is valid
        if (!isset($resData['file_path']) || !file_exists($filePath)) 
        {
            // Return a 404 error if the file is not found
            die('No file or you can\'t access');
        }

        // Use CodeIgniter's download function to initiate file download
        return $this->response->download($filePath, null)->setFileName($fileName);
    }

    /**
     * Generates a thumbnail for an image based on the specified size and settings.
     * Checks for an existing thumbnail and reuses it if available; otherwise, creates a new one.
     * Returns the thumbnail file for download.
     */
    public function image($uuid, $size=null)
    {
        $fileModel = new FileModel();
        $res = $fileModel->getByUUID($uuid);
        $rootPath = "/home/notip/writable/uploads";

        if (!$res || !$res['success']) 
        {
            die('No file or you can\'t access');
        }
        
        $resData = $res['data'];
        $filePath = $rootPath . $resData['file_path'];
        $fileName = $resData['origin_name'];

        // Check if the file path is valid
        if (!isset($resData['file_path']) || !file_exists($filePath)) 
        {
            die('No file or you can\'t access:'.$filePath);
        }

        $readPath = $filePath;

        if($size) 
        {
            // Validate $size parameter format
            if (!preg_match('/^\d+x\d+$/', $size)) {
                die('Invalid size format. Use "widthxheight" format.');
            }

            // Parse width and height from $size parameter
            list($width, $height) = explode('x', $size);
            $width = (int)$width;
            $height = (int)$height;

            // Get maintainRatio and masterDim from $_GET, use defaults if not set
            $maintainRatio = isset($_GET['maintainRatio']) ? filter_var($_GET['maintainRatio'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;
            $masterDim = isset($_GET['masterDim']) ? $_GET['masterDim'] : 'width';

            // Validate maintainRatio and masterDim values
            if ($maintainRatio === null) {
                die('Invalid maintainRatio value. Use "true" or "false".');
            }
            if (!in_array($masterDim, ['auto', 'width', 'height'])) {
                die('Invalid masterDim value. Use "auto", "width", or "height".');
            }

            // Define thumbnail file name and path within the original file directory
            $originalDir = dirname($filePath);
            $quality = 85;
            $thumbnailFileName = "{$fileName}_{$size}_{$maintainRatio}_{$masterDim}_{$quality}.jpg";
            $thumbnailPath = $originalDir . "/thumbnails/" . $thumbnailFileName;

            // If thumbnail already exists, use the existing file
            if (!file_exists($thumbnailPath)) 
            {
                // Create the thumbnails directory in the original file location if it does not exist
                if (!is_dir(dirname($thumbnailPath))) {
                    mkdir(dirname($thumbnailPath), 0777, true);
                }

                // Use CodeIgniter's Image Manipulation class to create a thumbnail
                \Config\Services::image()
                    ->withFile($filePath)
                    ->resize($width, $height, $maintainRatio, $masterDim)
                    ->convert(IMAGETYPE_JPEG) // Convert to JPEG format
                    ->save($thumbnailPath, $quality);
            } 

            $readPath = $thumbnailPath;
        }

        // Set headers for caching and serve the image directly
        return $this->response
            ->setHeader('Content-Type', 'image/jpeg')
            ->setHeader('Cache-Control', 'public, max-age=31536000') // Cache for 1 year
            ->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT') // Set expiration
            ->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', filemtime($readPath)) . ' GMT')
            ->setBody(file_get_contents($readPath));
    }

    public function savePlace(): array
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);
        
        $placeVo = array();
        $placeVo['placeId'] = $input['placeId'];
        $placeVo['name'] = $input['name'];
        $placeVo['alias'] = $input['alias'] ?? '';
        $placeVo['keyword'] = $input['keyword'] ?? '';
        $placeVo['state'] = $input['state'];
        $placeVo['city'] = $input['city'];
        $placeVo['address'] = $input['address'];
        $placeVo['latitude'] = $input['latitude'];
        $placeVo['longitude'] = $input['longitude'];
        $placeVo['zipcode'] = $input['zipcode'];
        $placeVo['type'] = $input['type'];
        $placeVo['reg_user'] = '1';
        $placeVo['reg_ip'] = $_SERVER['REMOTE_ADDR'];

        $placeModel = new PlaceModel();
        $res = $placeModel->addPlace($placeVo);

        returnData($res);
    }

    public function search(): string
    {
        return view('search-test');
    }
}

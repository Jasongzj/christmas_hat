<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests\Api\FaceRequest;
use Jasongzj\LaravelQcloudImage\QcloudImage;

class FaceController extends Controller
{
    public function detect(FaceRequest $request, QcloudImage $qcloudImage)
    {
        $image['file'] = $request->file('image')->getRealPath();
        $response = $qcloudImage->faceDetect($image, 1);
        return $this->response->array($response);
    }
}

<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests\Api\FaceRequest;
use Jasongzj\LaravelQcloudImage\QcloudImage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FaceController extends Controller
{
    const PORN_PICTURE = -501;

    public function detect(FaceRequest $request, QcloudImage $qcloudImage)
    {
        if ($request->input('image')) {
            $image['url'] = $request->input('image');
            $picture['urls'][] = $request->input('image');
        } else {
            $image['file'] = $request->file('image')->getRealPath();
            // 先检测是否黄图
            $picture['files'][] = $image['file'];
            // 保存图片至本地
            $request->file('image')->store('images');
        }

        $pornResponse = $qcloudImage->pornDetect($picture);

        if ($pornResponse['result_list'][0]['code'] != 0) {
            throw new BadRequestHttpException($pornResponse['result_list'][0]['message'], null, $pornResponse['result_list'][0]['code']);
        }

        if ($pornResponse['result_list'][0]['data']['result'] == 1) {
            throw new BadRequestHttpException('这是一张黄图', null, self::PORN_PICTURE);
        }

        $response = $qcloudImage->faceDetect($image, 1);
        if ($response['code'] != 0) {
            throw new BadRequestHttpException($response['message'], null, $response['code']);
        }

        return $this->response->array($response['data']);
    }
}

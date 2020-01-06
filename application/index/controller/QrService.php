<?php


namespace app\index\controller;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

class QrService
{

    public function createQr($url, $fileName)
    {
        $qrCode = new QrCode($url);
        $qrCode->setSize(300);

        // Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
//        $qrCode->setLabel('Scan the code', 16, __DIR__.'/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER());
        $qrCode->setLogoPath('./static/images/qr_default_logo.jpg');
        $qrCode->setLogoSize(50, 50);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

        // Directly output the QR code
//        header('Content-Type: '.$qrCode->getContentType());
//        echo $qrCode->writeString();

        // Save it to a file
        $qrCode->writeFile('./'.$fileName);

        // Create a response object
//        $response = new QrCodeResponse($qrCode);
//        return $response;
    }

}
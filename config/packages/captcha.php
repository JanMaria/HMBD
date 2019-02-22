<?php if (!class_exists('CaptchaConfiguration')) {
    return;
}

return [
    'CRUDCaptcha' => [
        'UserInputID' => 'captchaCode',
        'CodeLength' => CaptchaRandomization::GetRandomCodeLength(4, 6),
        'ImageStyle' => ImageStyle::AncientMosaic,
    ],
];

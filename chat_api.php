<?php
// السماح باستقبال البيانات كـ JSON
header('Content-Type: application/json; charset=utf-8');

// استلام الرسالة من الجافاسكريبت
$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['reply' => 'عذراً، لم أستلم أي رسالة.']);
    exit;
}



$systemPrompt = "أنت مساعد ذكي لمنصة تعليمية اسمها 'دليل' مخصصة لطلاب كلية الحاسبات والمعلومات. أجب باختصار وبطريقة ودية ومفيدة. رسالة الطالب هي: ";

// 3. تجهيز البيانات (تم التحديث إلى أحدث موديل Llama 3.3)
$payload = [
    "model" => "llama-3.3-70b-versatile", 
    "messages" => [
        [
            "role" => "system",
            "content" => $systemPrompt
        ],
        [
            "role" => "user",
            "content" => $userMessage
        ]
    ]
];

// إرسال الطلب باستخدام cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
// إرسال المفتاح في الـ Headers
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// لحل مشكلة XAMPP (إيقاف فحص الـ SSL مؤقتاً)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    echo json_encode(['reply' => 'مشكلة في الاتصال (cURL): ' . $curlError]);
    exit;
}

$responseData = json_decode($response, true);

if ($httpcode !== 200) {
    $errorMsg = $responseData['error']['message'] ?? 'خطأ غير معروف من سيرفر Groq';
    echo json_encode(['reply' => 'عذراً، هناك مشكلة: ' . $errorMsg]);
    exit;
}

// استخراج الرد
$aiReply = $responseData['choices'][0]['message']['content'] ?? 'حدث خطأ في قراءة الرد.';

// إرجاع الرد للمتصفح
echo json_encode(['reply' => $aiReply]);
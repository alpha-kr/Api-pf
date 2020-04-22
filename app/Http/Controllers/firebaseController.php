<?php

namespace App\Http\Controllers;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\ServiceAccount;

class firebaseController extends Controller
{
public function enviar($title,$body,$img=null)
{



    $firebase  = (new Factory)->withServiceAccount(__DIR__.'/firebasekey.json');
    $notification = Notification::fromArray([
        'title' => $title,
        'body' => $body,
        'image' => $img
    ]);
    $messaging = $firebase->createMessaging();
    $message = CloudMessage::withTarget('token','ec1Uybg50K4qTI1S5pFMtj:APA91bFfGlJctwHgabXnrf4Fej7o9K-2GOAm2wQx7urL0-vGFjiYF-5HwdsvoiIJdzzVmuGDwpeGFxINxrCfEzOZFPUGUyo2B41GDXoospPlSN1HAXawLxO2aH9JQKTsAXyg41MN4tia')
        ->withNotification($notification)
        ->withData(['score' => '1.0']);
        $messaging->send($message);
}
}

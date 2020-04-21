<?php

namespace App\Http\Controllers;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\ServiceAccount;

class firebaseController extends Controller
{
public function enviar()
{



    $firebase  = (new Factory)->withServiceAccount(__DIR__.'/firebasekey.json');
    $notification = Notification::fromArray([
        'title' => 'prueba notificacion',
        'body' => 'Esta es la prueba de la notificacion de shit ',
        'image' => 'https://www.wradio.com.co/images/4005120_n_vir3.JPG',
    ]);
    $messaging = $firebase->createMessaging();
    $message = CloudMessage::withTarget('token','ec1Uybg50K4qTI1S5pFMtj:APA91bFfGlJctwHgabXnrf4Fej7o9K-2GOAm2wQx7urL0-vGFjiYF-5HwdsvoiIJdzzVmuGDwpeGFxINxrCfEzOZFPUGUyo2B41GDXoospPlSN1HAXawLxO2aH9JQKTsAXyg41MN4tia')
        ->withNotification($notification)
        ->withData(['score' => '1.0']);
        $messaging->send($message);
}
}

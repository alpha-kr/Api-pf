<?php
namespace App\helpers;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
 class  NotificationFB {
    public static function enviar($title,$body,$img=null, $token)
    {



        $firebase  = (new Factory)->withServiceAccount(__DIR__.'/firebasekey.json');
        $notification = Notification::fromArray([
            'title' => $title,
            'body' => $body,
            'image' => $img,
            'icon'=>'https://www.computerhope.com/jargon/t/task.jpg'
        ]);
        $config = AndroidConfig::fromArray([
            'ttl' => '3600s',
            'priority' => 'normal',
            'notification' => [
                'title' => $title,
            'body' => $body,
            'image' => $img,
                'color' => '#0D0F74',
            ],
        ]);
        $messaging = $firebase->createMessaging();
        $message = CloudMessage::withTarget('token',$token)
            ->withNotification($notification)
            ->withData(['score' => '1.0']);
            $message= $message->withAndroidConfig($config);
            $messaging->send($message);
    }

}
?>

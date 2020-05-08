<?php
namespace App\helpers;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
 class  NotificationFB {
    public static function enviar($title,$body,$img=null, $token)
    {



        $firebase  = (new Factory)->withServiceAccount(__DIR__.'/firebasekey.json');

        $config1 = WebPushConfig::fromArray([
            'notification' => [
                'title' => $title,
                'body' => $body,
                'image' => $img,
                'icon'=>"https://www.computerhope.com/jargon/t/task.jpg"
            ],
            'fcm_options' => [
                'link' => 'https://jaj-proyect.web.app/',
            ],
        ]);
        $config2 = AndroidConfig::fromArray([
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
            ->withData(['score' => '1.0'])
            ->withWebPushConfig($config1)
            ->withAndroidConfig($config2);
            $messaging->send($message);
    }

}
?>

<?php

namespace App\Actions;


class FirebaseNotification
{
    public static function handle($tokens,$body,$title,$arr)
    {



        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        $extraNotificationData = $arr;

        $fcmNotification = [
            'registration_ids'        => $tokens, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key= AAAAqkE50lI:APA91bEo4LQK7KfmMqcaLQ1CH80xox5ekmd304LxP0Gk7k7jk44NbDg2YUUwfPnz04pNjfIdpKWDfPJ5g_4xlN6twPwEDHly6QwRgNZIwrgVlc-6mDN940I5NIHsD4Vzw_n6IRhQSoH8',
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        // dd($result);
        return $result;



    }
}
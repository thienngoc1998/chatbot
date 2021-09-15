<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    public function bot(Request $request)
    {
        $data = $request->all();
        Log::info('data', $data);

        die(0);
        $id = $data["entry"][0]["messaging"][0]["sender"]['id'];
        if (isset($data["entry"][0]["messaging"][0]['postback'])) {
            $this->handlePostback($id, $data["entry"][0]["messaging"][0]['postback']);
        } else if (isset($data["entry"][0]["messaging"][0]['message']['text'])) {
            $this->sendTextMessage($id, "Hi buddy");
        } else if (isset($data["entry"][0]["messaging"][0]['message']['attachments'])) {
            $this->callApiWithTemplate($id, $data["entry"][0]["messaging"][0]['message']);
        }
    }

    private function callApiWithTemplate($recipientId, $entry)
    {
        $attachment_url = $entry['attachments'][0]['payload']['url'];
        $messageData = [
            "recipient" => [
                "id" => $recipientId,
            ],
            "message" => [
                "attachment" => [
                    "type" => "template",
                    "payload" => [
                        "template_type" => "generic",
                        "elements" => [
                            [
                                "title" => "You sure?",
                                "subtitle" => "Tap a button to answer. ^^",
//                                "image_url" => "https://www.nexmo.com/wp-content/uploads/2018/10/build-bot-messages-api-768x384.png",
                                "image_url" => $attachment_url,
                                "buttons" => [
                                    [
                                        "type" => "postback",
                                        "title" => "Yes!",
                                        "payload" => "yes",
                                    ],
                                    [
                                        "type" => "postback",
                                        "title" => "No!",
                                        "payload" => "no",
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $this->callApiFaceBook(json_encode($messageData));
    }


    private function sendTextMessage($recipientId, $messageText)
    {
        $messageData = [
            "recipient" => [
                "id" => $recipientId,
            ],
            "message"   => [
                "text" => $messageText,
            ],
        ];
        $messageData = json_encode($messageData);
        $this->callApiFaceBook($messageData);
    }

    function handlePostback($sender_psid, $received_postback)
    {
        $response = '';
        $payload = $received_postback->payload;

        // Set the response based on the postback payload
        if ($payload === 'yes') {
            $response = 'Thank you very much!';
        } else if ($payload === 'no') {
            $response = "Oops, try sending another image.";
        }
        // Send the message to acknowledge the postback
        $this->sendTextMessage($sender_psid, $response);
    }

    public function callApiFaceBook($messageData)
    {
        $test = 'EAAFnjZA8tvMwBAK13ZAv0X6lrk9LetOhM14wwgogcawO4Ss9ULrTR6t03I9zZApsd8t1yx6aR8ZCqvgsZCygIQidleZCl9klyXsusaZCR1fNEkOSoAHzJBybRx8D1IGuJ5BwLudqlXnDWi5bClrgmPhHcZBU0malnnCqgVSZCzxCDpUTSEGvrcmde6rsuhZBvNUI8ZD';
        $ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . $test);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $messageData);
        curl_exec($ch);
        curl_close($ch);
    }

}

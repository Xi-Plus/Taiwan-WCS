<?php
require(__DIR__."/../config/config.php");
$commend = 'curl -X POST -H "Content-Type: application/json" -d \'{
  "setting_type" : "call_to_actions",
  "thread_state" : "existing_thread",
  "call_to_actions":[
    {
      "type":"postback",
      "title":"接收新的通知",
      "payload":"new"
    },
    {
      "type":"postback",
      "title":"列出已接收通知",
      "payload":"view"
    },
    {
      "type":"postback",
      "title":"取消接收通知",
      "payload":"del"
    },
    {
      "type":"web_url",
      "title":"連絡開發者",
      "url":"https://m.me/xiplus"
    }
  ]
}\' "https://graph.facebook.com/v2.6/me/thread_settings?access_token='.$cfg["page_token"].'"';
system($commend);
?>

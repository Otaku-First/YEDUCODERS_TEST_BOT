<?php
return [
  'api_key' => env('TRELLO_API_KEY', null),
  'api_token' => env('TRELLO_API_TOKEN', null),
  'webhook_url' => env('TRELLO_WEBHOOK_URL', null),
  'board_id'=>env('BOARD_ID', null),
];

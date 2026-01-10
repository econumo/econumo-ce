<?php

$data = <<<'JSON'
[
  {
    "id": "b7ef0c31-af2f-4073-8531-7c0d19a84ab4",
    "user_id": "77be9577-147b-4f05-9aa7-91d9b159de5b",
    "code": "f9dedac8b623",
    "created_at": "2023-11-20 01:51:45",
    "updated_at": "2023-11-20 01:51:45",
    "expired_at": "2020-11-20 02:01:45"
  },
  {
    "id": "3f30c30d-792b-459b-bda3-84fe4121baec",
    "user_id": "aff21334-96f0-4fb1-84d8-0223d0280954",
    "code": "39e83221911d",
    "created_at": "2023-11-20 01:51:51",
    "updated_at": "2023-11-20 01:51:51",
    "expired_at": "3000-11-20 02:01:51"
  }
]
JSON;

$date = new DateTimeImmutable('-1 month');
$data = preg_replace('/2\d{3}-\d{2}-/', $date->format('Y-m-'), $data);

return json_decode($data, true);



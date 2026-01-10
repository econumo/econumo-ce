<?php

$data = <<<'JSON'
[
  {
    "id": "0aaa0450-564e-411e-8018-7003f6dbeb92",
    "name": "Dany credit card",
    "currency_id": "fe5d9269-b69c-4841-9c04-136225447eca",
    "type": 1,
    "user_id": "77be9577-147b-4f05-9aa7-91d9b159de5b",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2021-08-12 19:43:09",
    "icon": "credit_card",
    "is_deleted": 0
  },
  {
    "id": "a62c06a0-d2b5-4564-a09b-703912c01481",
    "name": "Dany cash",
    "currency_id": "fe5d9269-b69c-4841-9c04-136225447eca",
    "type": 1,
    "user_id": "77be9577-147b-4f05-9aa7-91d9b159de5b",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2021-08-12 19:43:09",
    "icon": "account_balance_wallet",
    "is_deleted": 0
  },
  {
    "id": "99ff78ec-5081-11ec-bf63-0242ac130002",
    "name": "Sansa cash",
    "currency_id": "fe5d9269-b69c-4841-9c04-136225447eca",
    "type": 1,
    "user_id": "48044d88-5081-11ec-bf63-0242ac130002",
    "created_at": "2021-10-12 19:43:09",
    "updated_at": "2021-10-12 19:43:09",
    "icon": "account_balance_wallet",
    "is_deleted": 0
  },
  {
    "id": "5f3834d1-34e8-4f60-a697-004e63854513",
    "name": "John cash",
    "currency_id": "fe5d9269-b69c-4841-9c04-136225447eca",
    "type": 1,
    "user_id": "aff21334-96f0-4fb1-84d8-0223d0280954",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2021-08-12 19:43:09",
    "icon": "account_balance_wallet",
    "is_deleted": 0
  },
  {
    "id": "4eec1ee6-1992-4222-b9ab-31ece5eaad5d",
    "name": "John savings",
    "currency_id": "fe5d9269-b69c-4841-9c04-136225447eca",
    "type": 1,
    "user_id": "aff21334-96f0-4fb1-84d8-0223d0280954",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2021-08-12 19:43:09",
    "icon": "account_balance",
    "is_deleted": 0
  },
  {
    "id": "6c7b8af8-2f8a-4d6b-855c-ca6ff26952ff",
    "name": "John credit card",
    "currency_id": "fe5d9269-b69c-4841-9c04-136225447eca",
    "type": 1,
    "user_id": "aff21334-96f0-4fb1-84d8-0223d0280954",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2021-08-12 19:43:09",
    "icon": "credit_card",
    "is_deleted": 0
  }
]
JSON;

$date = new DateTimeImmutable('-1 month');
$data = preg_replace('/\d{4}-\d{2}-/', $date->format('Y-m-'), $data);

return json_decode($data, true);



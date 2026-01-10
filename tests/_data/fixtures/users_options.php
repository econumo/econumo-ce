<?php

$data = <<<'JSON'
[
  {
    "id": "cab99081-f58e-4846-a26f-c3b69fc715f4",
    "user_id": "aff21334-96f0-4fb1-84d8-0223d0280954",
    "name": "report_period",
    "value": "monthly",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2021-08-12 19:43:09"
  },
  {
    "id": "541c5dd4-2db1-49c0-9dae-8820cd22b330",
    "user_id": "77be9577-147b-4f05-9aa7-91d9b159de5b",
    "name": "report_period",
    "value": "monthly",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2021-08-12 19:43:09"
  },
  {
    "id": "0f2dc1d5-e5df-47b4-9d12-0be9e6aaa94a",
    "user_id": "48044d88-5081-11ec-bf63-0242ac130002",
    "name": "currency",
    "value": "USD",
    "created_at": "2021-11-28 22:28:18",
    "updated_at": "2021-11-28 22:28:18"
  },
  {
    "id": "c34db0fc-57da-4acb-bdcb-d199c88ef7b3",
    "user_id": "48044d88-5081-11ec-bf63-0242ac130002",
    "name": "report_period",
    "value": "monthly",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2021-08-12 19:43:09"
  },
  {
    "id": "a066bf7e-e84f-451f-94d8-c0d7cd7aaa57",
    "user_id": "48044d88-5081-11ec-bf63-0242ac130002",
    "name": "budget",
    "value": null,
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2021-08-12 19:43:09"
  },
  {
    "id": "c953abb8-06b9-4418-9d0c-aa5dedb29fc6",
    "user_id": "aff21334-96f0-4fb1-84d8-0223d0280954",
    "name": "budget",
    "value": "bceed17e-d492-40be-921a-e7fa6f663fa6",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2023-10-15 02:26:41"
  },
  {
    "id": "f0f8ab37-d168-46dc-9b80-21173caa35d6",
    "user_id": "aff21334-96f0-4fb1-84d8-0223d0280954",
    "name": "currency",
    "value": "RUB",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2023-10-15 04:36:22"
  },
  {
    "id": "0fb1c5cd-9ab5-4b4f-939e-59a214346658",
    "user_id": "77be9577-147b-4f05-9aa7-91d9b159de5b",
    "name": "budget",
    "value": "bceed17e-d492-40be-921a-e7fa6f663fa6",
    "created_at": "2021-08-12 19:43:09",
    "updated_at": "2023-10-20 04:47:47"
  },
  {
    "id": "432439ed-525f-485a-831c-0929b354560d",
    "user_id": "77be9577-147b-4f05-9aa7-91d9b159de5b",
    "name": "currency",
    "value": "RUB",
    "created_at": "2021-10-07 12:22:18",
    "updated_at": "2023-10-20 04:48:13"
  },
  {
    "id": "b1a6b623-d727-4579-8484-50355eebaecd",
    "user_id": "aff21334-96f0-4fb1-84d8-0223d0280954",
    "name": "onboarding",
    "value": "started",
    "created_at": "2024-12-01 10:00:00",
    "updated_at": "2024-12-22 15:23:37"
  },
  {
    "id": "4f35d1b6-a9f0-4696-bd21-b4a5e0e135e5",
    "user_id": "77be9577-147b-4f05-9aa7-91d9b159de5b",
    "name": "onboarding",
    "value": "started",
    "created_at": "2024-12-01 10:00:00",
    "updated_at": "2024-12-01 11:00:00"
  },
  {
    "id": "c9930459-f10f-419b-af1b-415fb95b8f5b",
    "user_id": "48044d88-5081-11ec-bf63-0242ac130002",
    "name": "onboarding",
    "value": "started",
    "created_at": "2024-12-01 10:00:00",
    "updated_at": "2024-12-01 11:00:00"
  }
]
JSON;

$date = new DateTimeImmutable('-1 month');
$data = preg_replace('/\d{4}-\d{2}-/', $date->format('Y-m-'), $data);

return json_decode($data, true);



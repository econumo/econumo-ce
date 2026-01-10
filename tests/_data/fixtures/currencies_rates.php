<?php

$data = <<<'JSON'
[
  {
    "id": "cebe7da3-1bb7-44d3-b281-93ca0beb8890",
    "currency_id": "1ae5bfd5-03e8-412b-80d2-c0ecf3ce32fe",
    "base_currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "rate": 0.91120600,
    "published_at": "2020-ll-01"
  },
  {
    "id": "5706b5f5-b7ea-4464-b795-b3fa7eb75d85",
    "currency_id": "fe5d9269-b69c-4841-9c04-136225447eca",
    "base_currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "rate": 120.50000000,
    "published_at": "2020-ll-01"
  },
  {
    "id": "486ad455-4e2a-4af4-a185-a72bab1a28f2",
    "currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "base_currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "rate": 1.00000000,
    "published_at": "2020-ll-01"
  },
  {
    "id": "280e78d9-d22c-47a8-83a6-f3504834af5b",
    "currency_id": "1ae5bfd5-03e8-412b-80d2-c0ecf3ce32fe",
    "base_currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "rate": 0.91474200,
    "published_at": "2020-cc-28"
  },
  {
    "id": "7b29813d-7b50-488f-b587-e14064f2e924",
    "currency_id": "fe5d9269-b69c-4841-9c04-136225447eca",
    "base_currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "rate": 132.73333300,
    "published_at": "2020-cc-28"
  },
  {
    "id": "f3034128-2a1d-4981-b4d4-921376431e15",
    "currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "base_currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "rate": 1.00000000,
    "published_at": "2020-cc-28"
  },
  {
    "id": "7b7504a6-df5c-4f4c-a28d-3469f5993141",
    "currency_id": "1ae5bfd5-03e8-412b-80d2-c0ecf3ce32fe",
    "base_currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "rate": 0.95078400,
    "published_at": "2020-cc-15"
  },
  {
    "id": "bde4e9a1-42d1-4333-ab4c-0fbd2443daeb",
    "currency_id": "fe5d9269-b69c-4841-9c04-136225447eca",
    "base_currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "rate": 97.70000400,
    "published_at": "2020-cc-15"
  },
  {
    "id": "3d5a5926-48c8-4e61-b7f0-30e03bd38096",
    "currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "base_currency_id": "a98daee8-1740-4514-a33e-dccaa90fc07b",
    "rate": 1.00000000,
    "published_at": "2020-cc-15"
  }
]
JSON;

$date = new DateTimeImmutable('-1 month');
$data = preg_replace('/\d{4}-ll-/', $date->format('Y-m-'), $data);
$data = preg_replace('/\d{4}-cc-/', date('Y-m-'), $data);

return json_decode($data, true);
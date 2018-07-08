# Prepaid card

The domain logic is located in the /src folder, whereas the framework specific functionality it's in the /app folder.

## Development

To execute run the docker image please run `docker-compose up` from the root of the project.

To shut it down: `docker-compose stop`

### Run composer

From the root of the project, please run `docker run --rm -v $(pwd):/app composer install`

## Endpoints

### Create a new card

```bash
curl -X POST \
  http://localhost:8080/card \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/json' \
  -d '{
	"owner-id": "7836c097-a9a6-4e32-8fba-ecc51b6314b7"
}'
```
```
{
    "card-id": "a74680af-3c24-4da9-ae78-984382773cd6",
    "owner-id": "7836c097-a9a6-4e32-8fba-ecc51b6314b7",
    "balance": "0",
    "blocked": "0"
}
```

### Load card

```bash
curl -X POST \
  http://localhost:8080/card/{card-id}/load \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/json' \
  -d '{
	"amount": "20"
}'
```
```bash
{
    "card-id": "dd3b4124-4386-47e6-85c8-095fe4d77247",
    "owner-id": "7836c097-a9a6-4e32-8fba-ecc51b6314b7",
    "balance": "20",
    "blocked": "0"
}
```

### Check balance

```bash
curl -X GET \
  http://localhost:8080/card/{card-id} \
  -H 'cache-control: no-cache'
```
```bash
{
    "id": "b3b1db45-ee9f-4ba0-803d-fa613b9df0a4",
    "owner-id": "7836c097-a9a6-4e32-8fba-ecc51b6314b7",
    "balance": "70",
    "blocked": "9"
}
```

### List card transactions

```bash
curl -X GET \
  http://localhost:8080/card/{card-id}/transactions \
  -H 'cache-control: no-cache' 
```
```bash
[
    {
        "transaction-id": "0dfaad15-508e-47ea-af16-f0f79b230525",
        "transaction-date": "2018-07-08T14:42:38+00:00",
        "card-id": "dd3b4124-4386-47e6-85c8-095fe4d77247",
        "merchant-id": "1",
        "amount": "10"
    }
]
```

### Make a new purchase

```bash
curl -X POST \
  http://localhost:8080/card/{card-id}/purchase \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/json' \
  -d '{
	"merchant-id": "1",
	"amount": 10
}'
```
```bash
{
    "transaction-id": "0dfaad15-508e-47ea-af16-f0f79b230525",
    "transaction-date": "2018-07-08T14:42:38+00:00",
    "card-id": "dd3b4124-4386-47e6-85c8-095fe4d77247",
    "merchant-id": "1",
    "amount": "10"
}
```

### Capture transaction

```bash
curl -X PUT \
  http://localhost:8080/transaction/{transaction-id}/capture \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/json' \
  -d '{
	"amount": 10
}'
```
```bash
{
    "transaction-id": "0dfaad15-508e-47ea-af16-f0f79b230525",
    "transaction-date": "2018-07-08T14:42:38+00:00",
    "card-id": "dd3b4124-4386-47e6-85c8-095fe4d77247",
    "merchant-id": "1",
    "amount": "10"
}
```

### Check card statement

```bash
curl -X GET \
  http://localhost:8080/card/{card-id}/statement \
  -H 'cache-control: no-cache'
```
```bash
[
    {
        "ledger-id": "202d1cef-b922-4c2f-8187-f897df0dbe46",
        "transaction-id": "0dfaad15-508e-47ea-af16-f0f79b230525",
        "date": "2018-07-08T14:47:20+00:00",
        "credit": "10",
        "debit": "0",
        "blocked": "0",
        "description": "transaction refunded"
    },
    {
        "ledger-id": "ca09a197-aade-4047-8e9a-81b0168c527c",
        "transaction-id": "0dfaad15-508e-47ea-af16-f0f79b230525",
        "date": "2018-07-08T14:42:38+00:00",
        "credit": "0",
        "debit": "0",
        "blocked": "10",
        "description": "authorization request sent"
    },
    {
        "ledger-id": "7aed43f3-4373-43b1-aa96-5cde87d1142a",
        "transaction-id": "0dfaad15-508e-47ea-af16-f0f79b230525",
        "date": "2018-07-08T14:45:44+00:00",
        "credit": "0",
        "debit": "10",
        "blocked": "0",
        "description": "transaction amount captured"
    }
]
```

### Refund transaction

```bash
curl -X PUT \
  http://localhost:8080/transaction/{transaction-id}/refund \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/json' \
  -d '{
	"amount": 1
}'
```
```bash
{
    "transaction-id": "0dfaad15-508e-47ea-af16-f0f79b230525",
    "transaction-date": "2018-07-08T14:42:38+00:00",
    "card-id": "dd3b4124-4386-47e6-85c8-095fe4d77247",
    "merchant-id": "1",
    "amount": "10"
}
```

## Access to redis

`docker exec -it prepaidcard_redis redis-cli`

## Testing

From the root of the project, please run `docker exec -it prepaidcard_php ./vendor/bin/phpunit`

## TODO

 - integrate openapi
 - implement Api mediatype
 - implement transaction/rollback mechanism
 - abstract Uuid
 - integration tests

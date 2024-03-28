# SettleUp Auth
## How to deploy project locally
Please the readme file in the main repo to deploy project locally.

## How to test locally
### Login 
GET http://localhost:8080/auth/login/4/1a1dc91c907325c69271ddf0c944bc72
### Carrier Key
GET http://localhost:8080/carrier-keys/8
{
    "auth": [
        "ZXlKcGRpSTZJak0xVW1WTlVGRTBSREV6WVhwUFdtVkZUVXhMVTNjOVBTSXNJblpoYkhWbElqb2lUVWxRZGs5SGFWcFRWRVZIUVVsRmJVcFBWRGxzWjFGdVRpOVNhMVk1YW1odVVtbFVTa3d2YVM5RlNEQkhVa1paUVRsaVpsVXliMFZtYWxsbGJYVlZOeUlzSW0xaFl5STZJbUUxTWpNd1l6Z3laVEkxWmpSbVpXUXhOak01TW1Oa1pqaGlaVFJqTldJeE5HVXpPVGhqTVRJNFpHTTFaakZoT1RneE9EUmpPVGswWXpWbVlUVTJOV1FpTENKMFlXY2lPaUlpZlE9PQ==",
        "Hf0Iqi16WMVq5GXsqHPGW7evXZ7rOIt9pfyAbI7ln7c="
    ]
}

### Update user
PUT http://localhost:8080/users/1
{
    "auth": [
        "ZXlKcGRpSTZJak0xVW1WTlVGRTBSREV6WVhwUFdtVkZUVXhMVTNjOVBTSXNJblpoYkhWbElqb2lUVWxRZGs5SGFWcFRWRVZIUVVsRmJVcFBWRGxzWjFGdVRpOVNhMVk1YW1odVVtbFVTa3d2YVM5RlNEQkhVa1paUVRsaVpsVXliMFZtYWxsbGJYVlZOeUlzSW0xaFl5STZJbUUxTWpNd1l6Z3laVEkxWmpSbVpXUXhOak01TW1Oa1pqaGlaVFJqTldJeE5HVXpPVGhqTVRJNFpHTTFaakZoT1RneE9EUmpPVGswWXpWbVlUVTJOV1FpTENKMFlXY2lPaUlpZlE9PQ==",
        "Hf0Iqi16WMVq5GXsqHPGW7evXZ7rOIt9pfyAbI7ln7c="
    ],
    "form_params": {
        "carrier_id": null,
        "carriers": [],
        "id": "1",
        "role_id": "1"
    }
} 

## Unit testing
For now only integration tests are available

To run integration tests:
```
docker exec -it settleup-auth-php composer run tests-integration
```
To run test coverage:
```
docker exec -it settleup-auth-php composer run tests-coverage
```
Coverage report will be available here: [http://localhost/tests-coverage.html]

Here is a shortcut to run just unut-tests:
```
docker exec -it settleup-auth-php ./vendor/bin/phpunit
```

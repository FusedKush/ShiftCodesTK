# `/get` Endpoints #

## `/get/token` ##
> Retrieve the *Current Request Token* belonging to the Current Session.

### Parameters ###

| Parameter | Type      | Description                                         |
| ---       | ---       | ---                                                 |
| *token*   | `?string` | The *Currently-stored Request Token*, if available. |

```http request
GET /api/get/token?token=9b5eb64efb9c22044309581b8e19a7aed7b91990c59a56718f09192279a09ef16666ede3aa0953a21ee66f50eed5aeea85e03937845b9b66c063281a763df3dd
```

### Response Payload ###

| Property | Type | Description |
| --- | --- | --- |
| *token* | `string` | If the `token` parameter is provided and has not changed, returns `"unchanged"`. Otherwise, returns the *Current Request Token*. |


```json
{
  "statusCode": 200,
  "statusMessage": "Ok",
  "payload": {
    "token": "9b5eb64efb9c22044309581b8e19a7aed7b91990c59a56718f09192279a09ef16666ede3aa0953a21ee66f50eed5aeea85e03937845b9b66c063281a763df3dd"
  }
}
```

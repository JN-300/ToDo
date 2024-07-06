# API Routes


## CREATE TOKEN (Login)

### Request
<pre>
curl --request POST \
  --url ./api/token \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
	"email": __EMAIL__,
	"password": __PASSWORD__
}'
</pre>
### Response
// success
<pre>
STATUS: 200
{
    'access_token': __ACCESSS_TOKEN
}
</pre>
// failure
<pre>
STATUS: 422
{
	"message": "auth.failed",
	"errors": {
		"email": [
			"auth.failed"
		]
	}
}
</pre>

## DELETE TOKEN (Logout)
### Request
<pre>
curl --request DELETE \
  --url ./api/token \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'accept: Application/json' \
  --header 'Content-Type: application/json' \
</pre>
### Response
// success
<pre>
STATUS: 200
{
	"success": true,
	"message": "token deleted"
}
</pre>
// failure
<pre>
STATUS: 401 (Unauthorized)
{
	"message": "unauthenticated"
}
</pre>

- DELETE./api/token

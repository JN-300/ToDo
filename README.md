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

## LIST TASKS
### Request
<pre>
curl --request GET \
  --url ./api/tasks \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json'
</pre>
### Response
<pre>
STATUS: 200
{
    "data": [
        {
            "title": STRING
            "description": STRING
            "status": "to_do"|"in_progress"|"done"
            "created_at": DATETIME
            "updated_at": DATETIME
        },
        ...
    ]
}
</pre>

## CREATE TASK
### Request
<pre>
curl --request POST \
  --url ./api/tasks\
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
        "title": STRING
        "description": STRING
        "status": "to_do"|"in_progress"|"done"
}'
</pre>

## SHOW SINGLE TASK
### Request
<pre>
curl --request GET \
  --url ./api/tasks/__TASK_UUID__ \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json'
</pre>
### Response
<pre>
STATUS: 200
{
    "data": 
        {
            "title": STRING
            "description": STRING
            "status": "to_do"|"in_progress"|"done"
            "created_at": DATETIME
            "updated_at": DATETIME
        }
}
</pre>

## UPDATE TASK
### Request
<pre>
curl --request PATCH \
  --url ./api/tasks/__TASK_UUID__ \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
        "title": STRING
        "description": STRING
        "status": "to_do"|"in_progress"|"done"
    }'
</pre>

## DELETE TASK
### Request
<pre>
curl --request DELETE \
  --url ./api/tasks/__TASK_UUID__ \
  --header 'Authorization: Bearer __ACCESS_TOKEN__' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' 
</pre>

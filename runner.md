cd /var/www/html/forward-kenya-party/ && php artisan serve

php artisan queue:work

cloudflared tunnel --url http://localhost:8000

ssh root@84.247.143.79

curl -X PUT http://84.247.143.79:3000/api/sessions/default \
-H "X-Api-Key: 1d9c86f76fa94822b6e34bceb3d736e6" \
-H "Content-Type: application/json" \
-d '{
"name": "default",
"config": {
"webhooks": [
{
"url": "https://forwardkenyaparty.com/api/webhook/waha",
"events": ["message", "message.any"]
}
]
}
}'


curl -X PUT http://84.247.143.79:3000/api/sessions/default \
-H "X-Api-Key: 1d9c86f76fa94822b6e34bceb3d736e6" \
-H "Content-Type: application/json" \
-d '{
"name": "default",
"config": {
"webhooks": [
{
"url": "https://loans-programmes-macro-discuss.trycloudflare.com/api/webhook/waha",
"events": ["message", "message.any"]
}
]
}
}'

curl -X GET http://84.247.143.79:3000/api/sessions/default \
-H "X-Api-Key: 1d9c86f76fa94822b6e34bceb3d736e6"

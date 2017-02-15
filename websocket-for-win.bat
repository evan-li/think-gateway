echo off
set register=php ./websocket.php /index/register/1
set gateway=php ./websocket.php /index/gateway/1
set business=php ./websocket.php /index/business/1

start %register%
start %gateway%
start %business%
# LazurdBackend_Magento

## Platform:
*Magento*

## How to Run:
- Create DB in Mysql
- Import Backup
- Update local.xml File for DB Connection
- Config SSL If neccessary
## How to Config The Virtual Host:   
    <VirtualHost *:80>
	ServerName lazurd.localhost
	DocumentRoot "d:/projects/narbashiyat/lazurd/source_code/magento/lazurd.localhost"
	<Directory  "d:/projects/narbashiyat/lazurd/source_code/magento/lazurd.localhost/">
		Options +Indexes +Includes +FollowSymLinks +MultiViews
		AllowOverride All
		Require all granted
	</Directory>
    RewriteEngine on
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ "index.html" [R=200,E=API:1,PT]
    <IfModule mod_headers.c>
    SetEnvIf Accept application/json API
    Header always set Access-Control-Allow-Origin "*" env=API
    Header always set Access-Control-Allow-Methods "POST, GET, OPTIONS, DELETE, PUT" env=API
    Header always set Access-Control-Allow-Headers "Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization" env=API
    </IfModule>
    </VirtualHost>

# db-framework
db-framework

Cau hinh Server Apache

<VirtualHost *:80>
        ServerName "website.vn"
        DocumentRoot "/var/www/html/website/"
        php_value date.timezone "Asia/Ho_Chi_Minh"
        php_value short_open_tag "On"

        <Directory "/var/www/html/website/">
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
            #Order allow,deny
            #allow from all
        </Directory>
</VirtualHost>

Khong su dung option 'MultiViews' vi duong link bat dau bang index se khong chay duoc
Nguyen nhan : http://www.gerd-riesselmann.net/archives/2005/04/beware-of-apaches-multiviews/
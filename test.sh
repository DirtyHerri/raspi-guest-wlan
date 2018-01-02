#!/usr/bin/env bash

for i in $@;
do
    params=" $params $d/$i"
done

FILE=$(php -i | grep ext-xdebug.ini)
printf "[xdebug]\nzend_extension=\"/usr/local/opt/php71-xdebug/xdebug.so\"" > $FILE
php ./vendor/bin/phpunit $params
printf "[xdebug]\n;zend_extension=\"/usr/local/opt/php71-xdebug/xdebug.so\"" > $FILE
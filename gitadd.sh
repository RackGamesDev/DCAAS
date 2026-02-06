#!/bin/sh
#git add .docker -f
git add docs/* -f
git add varwwwhtml/dcaas-app/app/* -f
git add varwwwhtml/dcaas-app/database/* -f
git add varwwwhtml/dcaas-app/routes/* -f
git add varwwwhtml/dcaas-app/config/* -f
git add .
echo a > storage/logs/debug.log
echo todo agregado
#chmod -R 777 /var/www/html
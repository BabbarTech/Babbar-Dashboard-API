#!/bin/bash

# without compression
#vendor/bin/sail exec mariadb mysqldump -u sail -h mariadb -psecret babbar-dashboard > babbar-dashboard.sql

# with compression
vendor/bin/sail exec mariadb mysqldump -u sail -h mariadb -psecret babbar-dashboard | gzip > babbar-dashboard-$(date +%Y%m%d-%H%M%S).sql.gz

# import dump with this command line
# gunzip < babbar-dashboard-20230303-143119.sql.gz | vendor/bin/sail mariadb -u sail -psecret babbar-dashboard

#!/bin/bash
yum groupinstall -y "PHP Support"  
yum install -y php-mysql  
yum install -y nginx  
yum install -y php-fpm  

# I follow the naming convention of MySQL;
# https://dev.mysql.com/doc/dev/mysql-server/latest/PAGE_NAMING_CONVENTIONS.html

DROP DATABASE IF EXISTS mariadbdtotest;
CREATE DATABASE mariadbdtotest;

DROP USER IF EXISTS mariadbdtotester;
CREATE USER 'mariadbdtotester'@'localhost' IDENTIFIED BY 'mariadbdtotester';
GRANT ALL PRIVILEGES ON mariadbdtotest.* TO 'mariadbdtotester'@'localhost';
FLUSH PRIVILEGES;


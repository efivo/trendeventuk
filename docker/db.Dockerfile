# MariaDB with the site's database imported automatically on the first start (empty volume)
FROM mariadb:11.4
COPY database/trend_events.sql /docker-entrypoint-initdb.d/01-trend_events.sql

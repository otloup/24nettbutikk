Requirements
====
 - docker
 - docker-compose

Setup
====
Answers to questions are located on the main app page (default is set to localhost at port 80)

`docker-compose up -d`

(
application will build itself, thanks to a compose.sh script. However, in case of this not being the case, to build application one needs to execute this command:
`docker exec -it server /bin/sh -c 'cd /app && php composer.phar update'`
)

Run investment command
-
`docker exec -it server php /app/bin/console 24net:search-for-profit`

command can receive arguments in order to change search period or investment
 - from: date from which to perform search. Valid format: year-month-day
 - to: date to which search is to be performed. Valid format: year-month-day
 - investment: amount to be invested

Run tests via:
-
`docker exec -it server /app/vendor/bin/phpunit -c /app/phpunit.xml --testsuite Unit /app/tests`


Known issues
====
 - NBP does not have archived data regarding the year 2012
 - no integration tests
 - no command test
 
  ... and aspirations
 -
 - retrieved data should be cached to REDIS for optimization
 - a sub-page to input data by hand and review them in form of a graph

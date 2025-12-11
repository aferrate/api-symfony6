# Sample API project with Symfony 6

### Features

- [x] Docker
- [x] Queue system
- [x] Read and write DBs
- [x] DDD approach
- [x] CQRS approach

### Start application:
```
cd laradock
cp .env.example .env
sudo docker-compose up -d nginx mysql phpmyadmin redis mailhog elasticsearch rabbitmq php-fpm redis-webui
```

### Start xdebug:
```
.\php-fpm\xdebug start
```

### Get into the container:
```
sudo docker-compose exec workspace bash
composer install
```

### Create database:
```
php bin/console doctrine:database:create
```

### Migrate entities:
```
php bin/console doctrine:migrations:migrate
```

### Load fixtures:
```
php bin/console doctrine:fixtures:load
```

### Create indexes in elasticsearch:
```
php bin/console app:create-elasticsearch-cars-index
php bin/console app:create-elasticsearch-users-index
```

### Delete indexes in elasticsearch:
```
php bin/console app:delete-elasticsearch-cars-index
php bin/console app:delete-elasticsearch-users-index
```

### Run async consumer:
```
php bin/console messenger:consume async -vv
```

### Run tests:
```
phpunit ./tests/Feature
phpunit ./tests/Integration
phpunit ./tests/Unit
```

### Stop application:
```
sudo docker-compose down
```

### Troubleshooting (Windows):
- Docker ports blocked:
```
net stop winnat
```
- Elasticsearch doesn't start:
```
wsl -d docker-desktop
sysctl -w vm.max_map_count=262144
```

### Access containers
#### Access phpmyadmin: [http://localhost:8081](http://localhost:8081)
- Credentials:
```
server mysql
user root
password root
```

#### Access mailhog: [http://localhost:8025](http://localhost:8025)
#### Access rabbitmq: [http://localhost:15672](http://localhost:15672)
- Credentials:
```
guest
guest
```

#### Access redis web-ui: [http://localhost:9987](http://localhost:9987)
- Credentials:
```
laradock
laradock
```

#### Access elasticsearch index cars:
- [http://localhost:9200/cars/_search?pretty=true&q=*:*&size=100](http://localhost:9200/cars/_search?pretty=true&q=*:*&size=100)
#### Access elasticsearch index users:
- [http://localhost:9200/users/_search?pretty=true&q=*:*&size=100](http://localhost:9200/users/_search?pretty=true&q=*:*&size=100)

### Endpoints:
#### Need to replace bearer with your token generated with login endpoint.
- Login:
```
curl --request POST \
  --url http://localhost/api/login_check \
  --header 'Content-Type: application/json' \
  --data '{
        "username":"test@test.com",
        "password":"test"
}'
```
- Refresh token:
```
curl --request POST \
  --url http://localhost/api/token/refresh \
  --header 'Content-Type: application/json' \
  --data '{
		"refresh_token":"ae2277de96fe04bb7f440bdadc5288b5cd9754fea8cde3256241a96da44020772162a9f3a58878d363e12691dbb3fba42b45355a251a60d08eafe3554f62f002"
}'
```
- Get all cars:
```
curl --request GET \
  --url http://localhost/api/v1/cars/page/0 \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json'
```
- Get all users:
```
curl --request GET \
  --url http://localhost/api/v1/users/page/0 \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json'
```
- Get all cars enabled:
```
curl --request GET \
  --url http://localhost/api/v1/cars/enabled/page/0 \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json'
```
- Get car from id:
```
curl --request GET \
  --url http://localhost/api/v1/car/id/520a558a-f811-4d0b-8348-b65a2910eb5d \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json'
```
- Get user from email:
```
curl --request GET \
  --url http://localhost/api/v1/user/email/test@test.com \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json'
```
- Create car:
```
curl --request POST \
  --url http://localhost/api/v1/car/create \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json' \
  --data '{
    "mark" : "test api",
    "model" : "test api",
    "description" : "test api",
    "country" : "test api",
    "city" : "test api",
    "year" : 2002,
    "enabled" : true,
    "imageFilename": "default.jpg"
}'
```
- Create user:
```
curl --request POST \
  --url http://localhost/api/v1/user/create \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json' \
  --data '{
    "email" : "test2@test.com",
    "password" : "test2"
}'
```
- Update car:
```
curl --request PUT \
  --url http://localhost/api/v1/car/update/33c9bf24-aefb-4778-a0b5-be216056a53a \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json' \
  --data '{
    "mark" : "test api44",
    "model" : "test api44",
    "description" : "test api44",
    "country" : "test api33",
    "city" : "test api33",
    "year" : 2009,
    "enabled" : false,
    "imageFilename": "default33.jpg"
}'
```
- Update user:
```
curl --request PUT \
  --url http://localhost/api/v1/user/update/test2@test.com \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json' \
  --data '{
    "email" : "test4@test.com",
    "password" : "test00000"
}'
```
- Delete car:
```
curl --request DELETE \
  --url http://localhost/api/v1/car/delete/33c9bf24-aefb-4778-a0b5-be216056a53a \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json'
```
- Delete user:
```
curl --request DELETE \
  --url http://localhost/api/v1/user/delete/test444444@test.com \
  --header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTcxNTAwMDUsImV4cCI6MTY5NzE1MzYwNSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LmNvbSIsImlwIjoiMTcyLjE5LjAuMSJ9.ern6tvHGBLGkK06mO5MuQiOV85qhnhSEANTj1-XbMd031OIE2LaDTinmr-BM32XGyv52-1p2JyTdXknByLxiEYsQjHlip_btrMUsRSHdcfuUb01rhLALCVRhzhjcXJ750VkQHtXbkvpP7euzHuLrD0woea61bF0t0OdjMbupR7guRVY09baIaHaS2a3GLucBMqMMlvv18xJ3fiDwbRE8X67qcVCJpth-wL7uavU9FEUFvzg62l3k4_h04AZNcTbvmMrLWjH69perDmYM_zLRq7AW0zeJEA46Y05EOxE42MQZaOoPevzcvnJStyPn1ixoF_tHNlk3rh-R_K_6vDcW6Q' \
  --header 'Content-Type: application/json'
```
# Reservation
Reservation App

## Prerequisite
- Docker 2.4 (or latest)
- Docker Compose 1.27.4 (or latest, usually included in docker insall setup)
- Composer 2.0 (or latest)
- Git
- PHP 7.4 or higher
- Mysql Client (Workbench or DBeaver)
- Postman (Optional)

## Stacks
- Laravel
- Docker
- docker-compose
- ReactJS / Material UI
- Mysql
- PHP 8.2

### Setup the APP
1. clone repo
```
git clone https://github.com/mugiwaranojem/reservation.git
docker-compose up
```
2. Setup BE
In separate terminal  
```
cd api
composer install
cp .env.example .env

# in new window terminal, setup laravel app
docker exec -it reservation_web php artisan key:generate
docker exec -it reservation_web php artisan migrate

```
2. Setup FE
```
cd frontend
yarn install
yarn start
```
 
3. Test the APP
Open in broswer http://localhost:3000/

### API Doc
```
# Import to PostMan
./api/Reservation Collection.postman_collection.json
```

# Part 2 Exam

```
Logic resides in api/routes/web.php and created and endpoint to show result:  
http://localhost:8000/count-occurence
```

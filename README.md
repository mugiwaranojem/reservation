# reservation
Reservation System

## Setup

```bash
composer install
```

```bash
php artisan migrate
```

```bash
php artisan db:seed
```

Trouble shooting

```bash
cannot append to file (append) error
docker exec -it reservation_web chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
```



## API

### Create Reservation


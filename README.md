cd app
composer install
yarn install
yarn encore dev
php bin/console doctrine:migrations:migrate

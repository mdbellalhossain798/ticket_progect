composer update
create database == "ticketing_system"
run command "php artisan  migrate"

run seeder "php artisan db:seed --class=UsersSeeder"
run "php artisan serve"
run "npm run dev"


you can login from 4 user
1. admin = user- mdbellalhossain798@gmail.com pass-12345678
2. customer= user- rakib@gmail.com pass - 12345678
3.	           anik@gmail.com  pass - 12345678
4.		   sakib@gmail.com pass - 12345678


i have used Mailpit. you need install it on your local pc for check mail configuration.

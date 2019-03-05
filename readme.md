
## Authentication With Laravel Passport


- Run **composer install**.
- setup .env file with your database configuration.
- Run **php artisan migration**.
- Run **php artisan db:seed --class=AdminUserSeede**r.
- Run **php artisan passport:install**.
- Run **php artisan serve**.
- Open Postman.
- Paste http://localhost:8000/oauth/token on postman url bar and select method as a post.
- Past below json with your configuration on postman body.

`{
  		"grant_type": "password",
         "client_id": 2,
         "client_secret": "your client secret which you got in 5th step",
          "username": "thilan87189@gmail.com",
          "password": "12345",
          "scope": "*" 
  }`
  
- Select postman body with raw and application/json.
- Click on "Send" Button.
- Now you can see access token, refresh token for logged user.


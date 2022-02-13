# Installation

- Generate `.env` using `.env.dist` as a template
  ```
  cp .env.dist .env
  ```
- Set `APP_SECRET` env var
  ```
  APP_SECRET=`tr -dc A-Za-z0-9 </dev/urandom | head -c 32 ; echo ''` ; sed -i "s/^APP_SECRET=.*/APP_SECRET=${APP_SECRET}/" .env
  ```
- Install composer packages
    - By starting the environment with docker: `make start`

      or
    - Locally: `composer install -n`
- Generate JWT Keys
    - Set `JWT_PASSPHRASE` env var
      ```
      JWT_PASSPHRASE=`tr -dc A-Za-z0-9 </dev/urandom | head -c 32 ; echo ''` ; sed -i "s/^JWT_PASSPHRASE=.*/JWT_PASSPHRASE=${JWT_PASSPHRASE}/" .env
      ```
    - Generate JWT Key pair
      ```
      make jwt-generate-keys
      ```

# Testing

*Make sure that you generated the JWT keys first*

API Tests only: `make test-api`

Integration Tests only: `make test-integration`

Run all tests: `make test`

# Simple Articles API

## Setup
### Build
`docker-compose build`
### Run
`docker-compose up -d`
### Run migrations
`docker exec -it articles_api_symfony bin/console doctrine:migrations:migrate`

## ApiDoc
Access on http://localhost/api/doc

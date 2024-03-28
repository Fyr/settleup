# Docker images for deployment
## How to build or rebuild an image
1. PHP
    ```bash
    docker build --platform linux/amd64 --progress=plain --tag settleup-main-php --file docker/azure/php/Dockerfile-php-main-qa .
    docker tag settleup-main-php settleup4qa.azurecr.io/main/php:latest
    ```
2. Web
    ```bash
     docker build --platform linux/amd64 --progress=plain --tag settleup-main-web --file docker/azure/nginx/Dockerfile-nginx-main-qa .
     docker tag settleup-main-web settleup4qa.azurecr.io/main/web:latest

    ```    
## How to push an image to Azure
1. Login to your Azure Container Registry: 
    ```
    docker login -u settleup4qa -p CoZtUhCXA33Z+jnB/yM/JZQ/VeykuH4CPOzhK0fB8f+ACRBw8Oi/ settleup4qa.azurecr.io
    ```
2. Push the image to the remote registry:
    PHP:    
    ```
    docker push settleup4qa.azurecr.io/main/php:latest
    ```
    Nginx:
    ```
    docker push settleup4qa.azurecr.io/main/web:latest
    ```

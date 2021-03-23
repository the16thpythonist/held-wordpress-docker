sudo docker system prune -af --volumes
sudo docker-compose -f local.yml build --no-cache
sudo docker-compose -f local.yml up --force-recreate
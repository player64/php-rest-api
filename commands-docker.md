# Docker compose commands
- To display running containers:
```bash
docker-compose ps
```

- To stop container:
```bash
docker-compose stop {container_name: api | db} 
```

- To stop all containers:
```bash
docker-compose down 
```

- To remove all volumes. Be careful this command removes the volume of the database
```bash
docker-compose down -v 
```

- To access a container terminal (Bash)
```bash
docker-compose exec {container_name: api | db}  bash   
```

- To run a selected container
```bash
docker-compose up {container_name: api | db} -d
```

- To run all containers the -d attribute means de-attached mode and runs in the background.
```bash
docker-compose up -d
```

- To display a container logs
```bash
docker-compose logs {container_name: api | db}
```
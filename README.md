# Threesome Gallery

Aplikace běží v Dockeru pro zajištění jednotného vývojového prostředí. Je dostupná na portu [8080](http://localhost:8080).

Spuštění kontejnerů:

```bash
docker compose up -d
```

Zastavení kontejnerů:

```bash
docker compose down -v
```

Vlaječka `-v` smaže data z databáze. Ty se pak vytvoří znovu při dalším spuštění.

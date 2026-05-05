![Apilo logo](docs/images/logo.png)

# Apilo Order Integration

A local Laravel + Vue application for sending orders to Apilo from a CSV file.

The project is designed to run with Docker. You do not need to install PHP, Composer, Node.js, or a database locally.

---

## Requirements

- Docker Desktop
- Web browser
- API credentials from the Apilo panel

---

## First Run

1. Start Docker Desktop.

2. Copy the environment file if you do not have a `.env` file yet:

```bash
docker compose run --rm app cp .env.example .env
```

3. Generate the Laravel application key:

```bash
docker compose run --rm app php artisan key:generate
```

4. Fill in the Apilo credentials in `.env`:

```env
APILO_CLIENT_ID=your_client_id
APILO_CLIENT_SECRET=your_client_secret
APILO_BASE_URL=https://your-apilo-domain
APILO_PLATFORM_ID=your_platform_id
```

5. Start the application using the dedicated launcher:

```text
launcher/Apilo.exe
```

The launcher starts Docker in the background and opens the application in the browser.

If you prefer using the terminal, you can start it manually:

```bash
docker compose up --build
```

During startup, the container installs Composer dependencies, installs Node.js packages, and builds the frontend.

6. Open the application in your browser if it does not open automatically:

```text
http://localhost:8080
```

---

## Daily Usage

The recommended way to start the application is the dedicated launcher:

```text
launcher/Apilo.exe
```

It starts Docker containers and opens `http://localhost:8080` automatically.

Docker commands are only needed when you want to manage containers manually.

Start the application from the terminal:

```bash
docker compose up
```

Start the application in the background:

```bash
docker compose up -d
```

Stop the containers:

```bash
docker compose down
```

View logs:

```bash
docker compose logs -f app
```

Open a shell inside the container:

```bash
docker compose exec app bash
```

---

## Apilo Configuration

The integration requires credentials from the Apilo panel:

- `APILO_CLIENT_ID`
- `APILO_CLIENT_SECRET`
- `APILO_BASE_URL`
- `APILO_PLATFORM_ID`

After changing `.env`, clear the Laravel configuration cache:

```bash
docker compose exec app php artisan config:clear
```

---

## Creating Apilo Tokens

Tokens are required to communicate with the Apilo API.

1. Log in to the Apilo panel.
2. Go to the API Apilo section.
3. Create an API application.
4. Generate an Authorization Code.
5. Run the command:

```bash
docker compose exec app php artisan apilo:tokens-create
```

The command will ask for the Authorization Code.

After successful execution:

- an access token is created,
- a refresh token is created,
- tokens are saved automatically,
- the application can communicate with Apilo.

---

## Testing the Apilo Connection

After creating tokens, test the connection:

```bash
docker compose exec app php artisan apilo:test-connection
```

A successful response means the application can access the Apilo API.

If the command fails after changing `.env`, clear the Laravel configuration cache:

```bash
docker compose exec app php artisan config:clear
```

---

## Tests

The project includes automated tests for:

- Order processing logic
- CSV preview logic
- API endpoints

Tests are written using PHPUnit.

Run tests through Docker:

```bash
docker compose exec app php artisan test
```

---

## How the Application Works

1. The user uploads a CSV file with products.
2. The application reads and validates the data.
3. The application checks product availability in Apilo.
4. The application prepares the order payload.
5. The order is sent to Apilo.
6. The result is displayed as a success, warning, or error message.

---

## Notes

- The application runs locally at `http://localhost:8080`.
- Docker must be running while using the application.
- An internet connection is required to communicate with Apilo.
- Apilo tokens must be created before sending orders.
- Use `launcher/Apilo.exe` for normal startup.
- Run PHP, Artisan, Composer, and Node.js commands through Docker when manual commands are needed.

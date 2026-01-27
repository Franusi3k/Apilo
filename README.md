![Apilo logo](docs/images/logo.png)

# Apilo Order Integration

This project is a local application built with Laravel and Vue.js.  
It allows sending orders to Apilo using data from a CSV file.

The application is designed to run locally and does not require any advanced technical knowledge to use.

---

## Requirements

Before running the application, make sure the following software is installed:

- Docker Desktop
- A web browser (Chrome, Edge, Firefox)

No PHP, Node.js, or database installation is required.

---

## How to Run the Application

1. Start Docker Desktop  
   Docker must be running in the background.

2. Run the application  
   Use the provided launcher file or start script.

3. Open the application in a browser  
   If the browser does not open automatically, open:

   http://localhost:8080

On the first start, the application may take up to a few minutes.

---

## Apilo Account and Authorization

To use this application, an active **Apilo account** is required.

### Creating an Authorization Code

1. Log in to your Apilo account.
2. Create an API application in the Apilo panel.
3. Generate an **Authorization Code** for the application.
4. Copy the Authorization Code. It will be needed to create access tokens.

---

## Environment Configuration (.env)

Before creating tokens, the application must be configured.

1. Open the `.env` file in the project root.
2. Set the required Apilo credentials:

APILO_CLIENT_ID=your_client_id
APILO_CLIENT_SECRET=your_client_secret
APILO_BASE_URL=https://your-apilo-domain
APILO_PLATFORM_ID=you_platform_id

3. Save the file.

Make sure all values are correct before continuing.

---

## Creating Apilo Tokens

Tokens are required to communicate with the Apilo API.

To create tokens, run the following command inside the application container or project directory:

php artisan apilo:tokens-create

The command will ask for the **Authorization Code** generated in the Apilo panel.

After successful execution:
- Access token and refresh token are created
- Tokens are stored automatically
- The application is ready to send orders

---

## Tokens Refresh

- Access tokens expire automatically
- Refresh tokens are used to generate new access tokens
- Token refresh is handled automatically by the application
- No manual action is required during normal usage

---

## How the Application Works

1. Upload a CSV file with product data.
2. The application:
   - Reads and validates the file
   - Checks product availability
   - Prepares the order data
3. The order is sent to Apilo.
4. The result is displayed as:
   - Success message
   - Warning message
   - Error message

---

## Tokens and Authorization

The application uses Apilo access and refresh tokens.

- Tokens are refreshed automatically
- No manual action is required during normal use

---

## Tests

The project includes automated tests for:

- Order processing logic
- CSV preview logic
- API endpoints

Tests are written using PHPUnit.

---

## Stopping the Application

To stop the application:

- Close the browser tab
- Stop the Docker containers or close Docker Desktop

---

## Notes

- The application runs locally only
- An internet connection is required to communicate with Apilo
- Docker must be running while the application is in use
- Apilo tokens must be created before sending orders

---

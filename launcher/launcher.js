const { exec } = require("child_process");

console.log("Uruchamiam aplikację...");

exec("docker compose up -d", (err) => {
  if (err) {
    console.error("Błąd Dockera:", err.message);
    return;
  }

  console.log("Kontenery uruchomione");
  console.log("Otwieram przeglądarkę...");

  const url = "http://localhost:8080";

  const startCmd =
    process.platform === "win32"
      ? `start "" "${url}"`
      : process.platform === "darwin"
      ? `open "${url}"`
      : `xdg-open "${url}"`;

  exec(startCmd);
});

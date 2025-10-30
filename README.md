# Unified Polyglot Delivery Platform

This repository provides a reference implementation of a multi-service DevOps
platform. It combines infrastructure automation written in Java, Go, Python and
C++ with a dynamic PHP/Tailwind dashboard that visualises the unified pipeline.

## Highlights

- **Smart CI/CD pipeline** orchestrated via Java with feedback into the PHP
  dashboard.
- **Intelligent code generation** utilities in Python capable of emitting client
  SDKs and documentation from a single API contract.
- **High-performance analytics** primitives in C++ and predictive insights in
  Python for cross-language quality reporting.
- **Unified dependency management** exposed through a Go command line utility
  reading a single manifest file.
- **Dynamic website** powered by PHP, TailwindCSS and vanilla JavaScript for
  surfacing deployment options, dependencies and live pipeline state.

## Getting started

### Generate client SDKs

```bash
python -m infrastructure.python.codegen
```

### Run the smart pipeline stub

Compile and execute the Java entrypoint using your favourite build tool or the
standard `javac`/`java` CLI:

```bash
javac -d build infrastructure/java/src/main/java/com/example/infra/SmartPipeline.java
java -cp build com.example.infra.SmartPipeline
```

The pipeline stores the latest execution snapshot in
`web/assets/js/pipeline_snapshot.json`, which the PHP dashboard consumes.

### View the dashboard

Serve the `web/` directory with your preferred PHP runtime:

```bash
php -S localhost:8080 -t web
```

Navigate to `http://localhost:8080` to inspect the unified control plane.

### Inspect dependencies

```bash
(cd infrastructure/go && go run ./cmd/dependency_manager)
```

Set `CONFIG_ROOT` if your configuration directory lives elsewhere.

This command prints the consolidated dependency list, including license
information, for all language ecosystems.

## Project structure

- `config/` – shared configuration including the API contract and dependency manifest.
- `infrastructure/java/` – core smart pipeline orchestration.
- `infrastructure/python/` – analysis and code generation modules.
- `infrastructure/go/` – unified dependency manager.
- `infrastructure/cpp/` – high performance primitives for diagnostics.
- `web/` – PHP/Tailwind experiences including the control plane and dynamic documentation portal.

### Manage the documentation portal

The `/docs` application mirrors the experience of modern documentation suites
with version switchers, contextual search, structured navigation, and a feedback
loop. To serve it alongside the dashboard, run:

```bash
php -S localhost:8080 -t web
```

Open `http://localhost:8080/docs/index.php` to browse guides, deep dives, and
API references filtered by release channel.

Programmatic content management is exposed at `web/docs/manage.php`. Authenticate
mutations with the `X-Docs-Token: changeme-admin-token` header:

```bash
curl -X POST "http://localhost:8080/docs/manage.php" \
  -H "Content-Type: application/json" \
  -H "X-Docs-Token: changeme-admin-token" \
  -d '{
        "section": "guides",
        "page": {
          "id": "customization",
          "version": "1.1",
          "title": "Tailor Pipelines",
          "summary": "Extend the orchestrator with organization-specific policies.",
          "body": []
        }
      }'
```

Use `PUT` and `DELETE` to update or remove entries. Omit the admin token for
read-only access to the full documentation data structure.

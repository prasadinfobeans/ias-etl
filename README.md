# IAS ETL

**IAS ETL** is a Symfony-compatible Composer package that provides ETL (Extract, Transform, Load) functionality. It integrates seamlessly with Symfony via Symfony Flex and provides:

- Auto-registered services
- Exposed HTTP routes
- A dedicated Doctrine DBAL connection

---

## 📦 Installation

### 1. Require the Package

If the package is hosted locally or on a private Git repository:

```bash
composer require ias/ias-etl:dev-main
```

If it's not listed on Packagist, make sure your Symfony project includes this in its `composer.json`:
** Note - your-org is where package is published...

```json
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/[your-org]/ias-etl"
  }
]
```

> Replace `https://github.com/your-org/ias-etl` with the actual Git URL.

---

### 2. Accept Composer Plugin Prompt

Composer will show this prompt:

```
Do you trust "ias/ias-etl" to execute code and wish to enable it now? (writes "allow-plugins" to composer.json) [y,n,d,?]
```

✅ **Press `y` to continue.**

If you're in a non-interactive environment or want to enable it manually, add this to your Symfony project's `composer.json`:

```json
"config": {
  "allow-plugins": {
    "ias/ias-etl": true
  }
}
```

---

## ⚙️ Configuration

### 1. Set ETL Database Connection

In your Symfony project's `.env` file, add:

```dotenv
ETL_DATABASE_URL="mysql://username:password@host:port/databse?serverVersion=8.0.42&charset=utf8mb4"
```

> Adjust DB credentials, host, port, and database name as per your environment.

---

### 2. Optional: Custom Doctrine DBAL Connection

If needed, configure a dedicated connection:

```yaml
# config/packages/doctrine.yaml
doctrine:
  dbal:
    connections:
      etl:
        url: '%env(resolve:ETL_DATABASE_URL)%'
```

And optionally configure the bundle:

```yaml
# config/packages/ias_etl.yaml
ias_etl:
  db_connection: etl
```

---

## ✅ What Happens on Installation

When installed using Symfony Flex, the following happens automatically:

- PHP route file is copied to: `config/routes/ias_etl_routes.php`
- YAML route loader is added at: `config/routes/ias_etl.yaml`
- Services are auto-wired via `DependencyInjection/IASETLExtension.php`

---

## ❌ Uninstallation

To remove the package cleanly:

```bash
composer remove ias/ias-etl
```

After removal, **manually delete** the following files:

- `config/routes/ias_etl.yaml`
- `config/routes/ias_etl_routes.php`
- `config/packages/ias_etl.yaml` (if exists)
- Remove `ETL_DATABASE_URL` from your `.env`

---

## 🧪 Local Development (Optional)

If you're working on this package locally and want to test it inside another Symfony project:

1. Add a local repository path:

```json
"repositories": [
  {
    "type": "path",
    "url": "../ias-etl"
  }
]
```

2. Then require the package:

```bash
composer require ias/ias-etl:dev-main
```

---

## 📁 Project Structure

```
ias-etl/
├── config/
│   └── routes.php
├── src/
│   ├── Controller/
│   ├── Service/
│   ├── DependencyInjection/
│   │   ├── Configuration.php
│   │   └── IASETLExtension.php
│   └── Composer/
│       └── Plugin.php
├── composer.json
└── README.md
```

---

## 📄 License

MIT

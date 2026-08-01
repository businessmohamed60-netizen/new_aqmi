# AQMI Report Studio — Module

A native AQMI module for building and publishing structured certification
reports with a drag & drop builder, live preview, and PDF handoff.

## Architecture

```
app/Modules/ReportStudio/
├── Controllers/          ← receive forms from Views, delegate to Services
│   ├── ReportStudioController.php   dashboard
│   ├── TemplateController.php       template CRUD (POST forms)
│   ├── BuilderController.php        builder canvas + AJAX autosave
│   ├── ThemeController.php          theme CRUD (POST forms)
│   └── PreviewController.php        live preview
├── Models/               ← thin data access via AQMI Database (PDO)
│   ├── ReportTemplate.php
│   ├── ReportTemplateBlock.php
│   ├── ReportBlock.php
│   └── ReportTheme.php
├── Services/             ← business logic, called by Controllers
│   ├── TemplateService.php
│   ├── BuilderService.php
│   ├── PreviewService.php
│   ├── ThemeService.php
│   ├── TemplateRenderer.php         renders block partials
│   ├── ThemeCompiler.php            theme → CSS custom properties
│   └── BlockRegistry.php            block_key → metadata map
├── Traits/
│   └── HasJsonFields.php            JSON column cast helpers
└── README.md

Views live in the host app at resources/views/reportstudio/
(loaded via the view() and view_partial() helpers):
    dashboard.php, templates/{index,form,detail}, builder/{canvas,block_card,
    property_panel,properties/*}, themes/{index,form}, preview/show,
    report/* (14 final-render block partials)
```

## Data flow

```
View (form)  →  Controller  →  Service  →  Model  →  Database (PDO)
```

- **Controllers** receive `$_POST` from standard form submissions.
  The only AJAX endpoint is `BuilderController::update()` for drag & drop autosave.
- **Services** contain all business logic (validation, normalization, assembly).
- **Models** are thin wrappers around the AQMI `Database` helper.
  They expose static methods (`find`, `all`, `create`, `update`, `delete`)
  that call `Database::fetchOne`, `Database::fetchAll`, `Database::insert`,
  `Database::update`, `Database::delete`.
- **Views** are plain PHP. They receive arrays, not objects.

## Database helper API used

The module assumes the AQMI `Database` class provides:

```php
Database::fetchOne(string $sql, array $params = []): ?array
Database::fetchAll(string $sql, array $params = []): array
Database::insert(string $table, array $data): int      // returns lastInsertId
Database::update(string $table, array $data, array $where): bool
Database::delete(string $table, array $where): bool
```

## Routes (register in the host router)

```
GET  /reportstudio                        ReportStudioController@index
GET  /reportstudio/templates              TemplateController@index
GET  /reportstudio/templates/create       TemplateController@create
POST /reportstudio/templates              TemplateController@store
GET  /reportstudio/templates/{id}         TemplateController@show
GET  /reportstudio/templates/{id}/edit    TemplateController@edit
POST /reportstudio/templates/{id}         TemplateController@update
POST /reportstudio/templates/{id}/delete  TemplateController@destroy

GET  /reportstudio/builder/{id}/edit      BuilderController@edit
PUT  /reportstudio/builder/{id}           BuilderController@update   (AJAX)

GET  /reportstudio/preview/{id}           PreviewController@show

GET  /reportstudio/themes                 ThemeController@index
GET  /reportstudio/themes/create          ThemeController@create
POST /reportstudio/themes                 ThemeController@store
GET  /reportstudio/themes/{id}/edit       ThemeController@edit
POST /reportstudio/themes/{id}            ThemeController@update
POST /reportstudio/themes/{id}/delete     ThemeController@destroy
```

## Host helpers used (not re-implemented)

- `view(string $path, array $data = []): void` — renders a view
- `view_partial(string $path, array $data = [], bool $return = false): string` — renders a partial
- `route(string $name, array $params = []): string` — generates a URL
- `redirect(string $url): void` — redirects
- `e(string $value): string` — HTML escape
- `abort(int $code): void` — throws HTTP error

## Migrations

```
database/migrations/
├── 001_create_report_studio_tables.sql
├── 002_seed_report_studio_data.sql
└── 003_add_report_studio_features.sql   (visibility, settings, official_stamp)
```

## Built-in blocks (14)

| Key              | Name                 | Category   |
|------------------|----------------------|------------|
| global_score     | Global Score         | metrics    |
| radar_chart      | Radar Chart          | charts     |
| gauge            | Gauge                | metrics    |
| recommendations  | Recommendations      | content    |
| company_info     | Company Information  | content    |
| aqmi_logo        | AQMI Logo            | branding   |
| company_logo     | Company Logo         | branding   |
| official_stamp   | Official Stamp       | branding   |
| qr_code          | QR Code              | utility    |
| signature        | Signature            | utility    |
| header           | Header               | structure  |
| footer           | Footer               | structure  |
| rich_text        | Rich Text            | content    |
| image            | Image                | media      |

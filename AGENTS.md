# D2U News - Redaxo Addon

A Redaxo 5 CMS addon for managing news articles with categories, multiple link types, and optional plugins for trade fairs and news types. Supports deep linking to d2u_machinery and d2u_courses.

## Tech Stack

- **Language:** PHP >= 8.0
- **CMS:** Redaxo >= 5.10.0
- **Frontend Framework:** Bootstrap 4/5 (via d2u_helper templates)
- **Namespaces:** `D2U_News` (News, Category, Fair, Type), `TobiasKrais\D2UNews` (Module)

## Project Structure

```text
d2u_news/
├── boot.php               # Addon bootstrap (extension points, permissions)
├── install.php             # Installation (database tables, sprog wildcards)
├── update.php              # Update (calls install.php)
├── uninstall.php           # Cleanup (database tables, sprog wildcards)
├── package.yml             # Addon configuration, version, dependencies
├── lang/                   # Backend translations (de_de, en_gb)
├── lib/                    # PHP classes
│   ├── news.php            # News model (multilingual, multiple link types)
│   ├── category.php        # Category model (multilingual)
│   ├── d2u_news_lang_helper.php      # Sprog wildcard provider (11 languages)
│   └── d2u_news_module_manager.php   # Module definitions and revisions
├── modules/                # 3 module variants in group 40
│   └── 40/
│       ├── 1/              # News output
│       ├── 2/              # Trade fair output
│       └── 3/              # News and trade fairs combined
├── pages/                  # Backend pages
│   ├── index.php           # Page router
│   ├── news.php            # News management (CRUD)
│   ├── categories.php      # Category management
│   ├── settings.php        # Addon settings
│   └── setup.php           # Module manager + changelog
└── plugins/                # 2 plugins
    ├── fairs/              # Trade fair management
    └── news_types/         # News type categorization
```

## Coding Conventions

- **Namespaces:** `D2U_News` (News, Category, Fair, Type), `TobiasKrais\D2UNews` (Module)
- **Naming:** camelCase for variables, PascalCase for classes
- **Indentation:** 4 spaces in PHP classes, tabs in module files
- **Comments:** English comments only
- **Frontend labels:** Use `Sprog\Wildcard::get()` backed by lang helper, not `rex_i18n::msg()`
- **Backend labels:** Use `rex_i18n::msg()` with keys from `lang/` files

## Key Classes

| Class | Description |
| ----- | ----------- |
| `News` | News model: name, teaser, picture, categories, types, link types (none/article/url/machine/course), online status, date. Implements `ITranslationHelper` |
| `Category` | Category model: name, picture, priority. Implements `ITranslationHelper` |
| `d2u_news_lang_helper` | Sprog wildcard provider for 11 languages (DE, EN, FR, ES, IT, PL, NL, CZ, RU, PT, ZH) |
| `Module` | Module definitions and revision numbers for 3 modules |

### Plugin Classes

| Class | Plugin | Description |
| ----- | ------ | ----------- |
| `Fair` | `fairs` | Trade fair model: name, city, country code, start/end date, picture |
| `Type` | `news_types` | News type model: name, priority. Implements `ITranslationHelper` |

## Database Tables

| Table | Description |
| ----- | ----------- |
| `rex_d2u_news_news` | News (language-independent): category IDs, picture, link type, article/URL/machine/course IDs, online status, date |
| `rex_d2u_news_news_lang` | News (language-specific): name, teaser, hide per language, translation status |
| `rex_d2u_news_categories` | Categories (language-independent): priority, picture |
| `rex_d2u_news_categories_lang` | Categories (language-specific): name, translation status |

### Plugin Tables

| Table | Plugin | Description |
| ----- | ------ | ----------- |
| `rex_d2u_news_fairs` | `fairs` | Trade fairs: name, city, country, dates, picture |
| `rex_d2u_news_types` | `news_types` | News types (language-independent): priority |
| `rex_d2u_news_types_lang` | `news_types` | News types (language-specific): name, translation status |

## Architecture

### Extension Points

| Extension Point | Location | Purpose |
| --------------- | -------- | ------- |
| `ART_PRE_DELETED` | boot.php (backend) | Prevents deletion of articles used by the addon |
| `CLANG_DELETED` | boot.php (backend) | Cleans up language-specific data |
| `D2U_HELPER_TRANSLATION_LIST` | boot.php (backend) | Registers addon in D2U Helper translation manager |
| `MEDIA_IS_IN_USE` | boot.php (backend) | Prevents deletion of media files in use |

### Link Types

| Type | Target |
| ---- | ------ |
| `none` | No link |
| `article` | Internal Redaxo article |
| `url` | External URL |
| `machine` | D2U Machinery machine |
| `course` | D2U Courses course |

### Online Status

- `online` — Published and visible
- `offline` — Hidden
- `archived` — Archived

### Modules

3 module variants in group 40:

| Module | Name | Description |
| ------ | ---- | ----------- |
| 40-1 | D2U News - Ausgabe News | News list output |
| 40-2 | D2U News - Ausgabe Messen | Trade fair output |
| 40-3 | D2U News - Ausgabe News und Messen | Combined news and trade fair output |

#### Module Versioning

Each module has a revision number defined in `lib/d2u_news_module_manager.php` inside the `getModules()` method. When a module is changed:

1. Add a changelog entry in `pages/setup.php` describing the change.
2. Increment the module's revision number by one.

**Important:** The revision only needs to be incremented **once per release**, not per commit. Check the changelog: if the version number is followed by `-DEV`, the release is still in development and no additional revision bump is needed.

### Plugins

| Plugin | Description | Key Classes |
| ------ | ----------- | ----------- |
| `fairs` | Trade fair management | `Fair` |
| `news_types` | News type categorization (multilingual) | `Type` |

## Settings

Managed via `pages/settings.php` and stored in `rex_config`:

- `article_id` — Article for news page
- `default_sort` — Sort by name or priority
- `lang_wildcard_overwrite` — Preserve custom Sprog translations
- `lang_replacement_{clang_id}` — Language mapping per REDAXO language

## Dependencies

| Package | Version | Purpose |
| ------- | ------- | ------- |
| `d2u_helper` | >= 1.14.0 | Backend/frontend helpers, module manager, translation interface |
| `sprog` | >= 1.0.0 | Frontend translation wildcards |

### Optional Integrations

- `d2u_machinery` — Deep linking to machines
- `d2u_courses` — Deep linking to courses

## Multi-language Support

- **Backend:** de_de, en_gb
- **Frontend (Sprog Wildcards):** DE, EN, FR, ES, IT, PL, NL, CZ, RU, PT, ZH (11 languages)

## Versioning

This addon follows [Semantic Versioning](https://semver.org/). The version number is maintained in `package.yml`. During development, the changelog uses a `-DEV` suffix.

## Changelog

The changelog is located in `pages/setup.php`.

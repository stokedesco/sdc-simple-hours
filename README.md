# Stoke Simple Hours Plugin

## Installation

1. Upload the `simple-hours` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to Settings → Stoke Simple Hours to configure your weekly hours and holiday overrides.
4. Optionally enable schema.org markup to output structured data for search engines.

## Usage

- Shortcodes:
  - `[simplehours_today]` – Today's hours
  - `[simplehours_until]` – Open today until
  - `[simplehours_fullweek]` – Full week table

- Elementor:
  - Add the **Stoke Simple Hours** widget and choose the output format along with text or table styling.

- Schema Markup:
  - Enable schema.org markup in settings to auto-generate `OpeningHoursSpecification` JSON-LD.


## Filters & Actions

- `simplehours_open_text`
- `simplehours_before_list`

## CSS Variables

- `--simplehours-text-colour`
- `--simplehours-bg-colour`
- `--simplehours-font-size`
- `--simplehours-font-family`

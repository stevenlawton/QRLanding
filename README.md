# QR Landing

A lightweight PHP redirect service for QR codes. Map short tags to destination URLs with a single JSON file — no database required.

`/qr/menu` → `https://example.com/menu.pdf`

## How It Works

1. QR codes point to `/qr/<tag>` on your server
2. Apache rewrites the request to `redirect.php`
3. PHP looks up the tag in `redirects.json` and issues a 302 redirect
4. A JSON Schema + GitHub Actions CI keeps the mapping file valid

## Setup

### Requirements

- Apache with `mod_rewrite` enabled
- PHP 8.0+

### Deploy

1. Clone the repo into your web root (or a virtual host directory):

   ```sh
   git clone https://github.com/YOUR_USER/QRLanding.git /var/www/qrlanding
   ```

2. Ensure Apache allows `.htaccess` overrides. In your site config:

   ```apache
   <Directory /var/www/qrlanding>
       AllowOverride All
   </Directory>
   ```

3. Add your redirects to `redirects.json`:

   ```json
   {
     "menu": "https://example.com/menu.pdf",
     "wifi": "https://example.com/wifi-setup",
     "insta": "https://instagram.com/yourpage"
   }
   ```

4. Point your QR codes at `https://yourdomain.com/qr/menu`, etc.

## Adding Redirects

Edit `redirects.json`. Each key is a tag, each value is the destination URL:

```json
{
  "tag": "https://destination-url.com"
}
```

Push to trigger CI validation, or validate locally:

```sh
npx ajv-cli validate -s redirects.schema.json -d redirects.json
```

## Local Development

Start PHP's built-in server:

```sh
php -S localhost:8000
```

Test a redirect directly:

```
http://localhost:8000/redirect.php?tag=example
```

Note: `.htaccess` rewrite rules only work under Apache, so `/qr/<tag>` paths won't work with the PHP dev server.

## Project Structure

```
.
├── .github/workflows/validate.yml   # CI schema validation
├── .htaccess                        # Rewrite /qr/<tag> → redirect.php
├── index.html                       # Landing page for /
├── redirect.php                     # Redirect logic
├── redirects.json                   # Tag → URL mapping
└── redirects.schema.json            # JSON Schema for validation
```

## License

[MIT](LICENSE)

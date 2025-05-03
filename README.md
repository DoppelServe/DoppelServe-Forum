# DoppelServe-Forum

A lightweight, privacy-focused PHP forum designed for Tor and high-security environments. Built with minimal dependencies, no JavaScript, and no external calls.

[![License: WTFPL](https://img.shields.io/badge/License-WTFPL-brightgreen.svg)](http://www.wtfpl.net/about/)

## Features

- **Privacy First**: No tracking, analytics, or external resources
- **Tor Optimized**: Designed for .onion services with strict security headers
- **Zero JavaScript**: Pure server-side rendering for maximum compatibility
- **Minimal Dependencies**: Only PHP and MySQL required
- **Secure by Default**: CSRF protection, prepared statements, input validation
- **Rate Limiting**: nginx-based protection against abuse
- **Clean Architecture**: Modular PDO wrapper, DRY principles

## Requirements

- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.2+
- nginx (for rate limiting)

## Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/doppelserve-forum.git
cd doppelserve-forum
```

2. Configure your database
```bash
mysql -u root -p < database/schema.sql
```

3. Update `config.php` with your database credentials
```php
$db_config = [
    'host' => 'localhost',
    'dbname' => 'forum',
    'user' => 'your_user',
    'pass' => 'your_password'
];
```

4. Configure nginx (see `nginx/forum.conf`)

5. Set proper permissions
```bash
chmod 755 /path/to/forum
chown -R www-data:www-data /path/to/forum
```

## Security Features

- Content Security Policy (CSP)
- X-Frame-Options: DENY
- No external resources
- Prepared statements for all queries
- Password hashing with `password_hash()`
- Session regeneration on login
- Input validation and sanitization

## Structure

```
/forum/
├── bootstrap.php    # Application initialization
├── config.php       # Database configuration
├── database.php     # PDO wrapper class
├── functions.php    # Helper functions
└── *.php           # Forum pages
```

## Contributing

Pull requests welcome. Please follow existing code style and security practices.

## License

WTFPL - Do What The F*ck You Want To Public License

---

Built by [doppelserve.com](https://doppelserve.com)

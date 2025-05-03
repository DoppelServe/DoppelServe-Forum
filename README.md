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

## SonarCloud
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=DoppelServe_DoppelServe-Forum&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=DoppelServe_DoppelServe-Forum)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=DoppelServe_DoppelServe-Forum&metric=bugs)](https://sonarcloud.io/summary/new_code?id=DoppelServe_DoppelServe-Forum)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=DoppelServe_DoppelServe-Forum&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=DoppelServe_DoppelServe-Forum)
[![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=DoppelServe_DoppelServe-Forum&metric=duplicated_lines_density)](https://sonarcloud.io/summary/new_code?id=DoppelServe_DoppelServe-Forum)

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

4. Configure nginx

Create `/etc/nginx/conf.d/forum_rate_limits.conf`:
```nginx
# Rate limit zones
limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
limit_req_zone $binary_remote_addr zone=register:10m rate=3r/h;
limit_req_zone $binary_remote_addr zone=thread:10m rate=10r/h;
limit_req_zone $binary_remote_addr zone=reply:10m rate=30r/h;
```

Create `/etc/nginx/sites-available/forum.conf`:
```nginx
server {
    listen 80;
    server_name your-forum.onion;
    root /var/www/forum;
    
    index index.php;
    
    # Security headers
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy no-referrer;
    add_header Content-Security-Policy "default-src 'self'";
    
    # Apply rate limits
    location = /login.php {
        limit_req zone=login burst=3 nodelay;
        include fastcgi_php.conf;
    }
    
    location = /register.php {
        limit_req zone=register burst=2 nodelay;
        include fastcgi_php.conf;
    }
    
    location = /create_thread.php {
        limit_req zone=thread burst=5 nodelay;
        include fastcgi_php.conf;
    }
    
    location = /reply.php {
        limit_req zone=reply burst=10 nodelay;
        include fastcgi_php.conf;
    }
    
    # PHP handler
    location ~ \.php$ {
        include fastcgi_php.conf;
    }
    
    # Block access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

Create `/etc/nginx/fastcgi_php.conf`:
```nginx
try_files $uri =404;
fastcgi_pass unix:/var/run/php/php-fpm.sock;
fastcgi_index index.php;
fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
include fastcgi_params;
```

Enable the site:
```bash
ln -s /etc/nginx/sites-available/forum.conf /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

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

## Contributing

Pull requests welcome. Please follow existing code style and security practices.

## License

WTFPL - Do What The F*ck You Want To Public License

---

Built by [doppelserve.com](https://doppelserve.com)

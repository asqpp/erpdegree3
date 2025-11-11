<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| PRODUCTION Database Configuration Template
|--------------------------------------------------------------------------
|
| This is a template for production database configuration.
| Copy these settings to your database.php file and update with your
| actual production database credentials.
|
| ⚠️ IMPORTANT SECURITY NOTES:
| 1. NEVER commit production credentials to version control
| 2. Use strong, unique passwords for database users
| 3. Grant only necessary privileges to database user
| 4. Use SSL/TLS for database connections if possible
| 5. Keep database on localhost unless absolutely necessary
|
*/

/*
| For HostGator and most shared hosting:
| - hostname: localhost
| - username: cpanel_username_dbuser (e.g., mysite_erpuser)
| - database: cpanel_username_dbname (e.g., mysite_insurance)
|
| Database credentials can be found/created in cPanel → MySQL Databases
*/

$active_group = 'default';
$query_builder = TRUE;

/*
|--------------------------------------------------------------------------
| PRODUCTION Database Configuration
|--------------------------------------------------------------------------
*/

$db['default'] = array(
    'dsn'          => '',

    /*
    | Database hostname
    | For HostGator: localhost
    | For remote database: IP address or hostname
    */
    'hostname'     => 'localhost',

    /*
    | Database username
    | ⚠️ Replace with your actual database username from cPanel
    | Format on shared hosting: cpanel_username_dbuser
    | Example: mysite_erpuser
    */
    'username'     => 'YOUR_CPANEL_USERNAME_dbuser',

    /*
    | Database password
    | ⚠️ Use a STRONG password (20+ characters with mixed case, numbers, symbols)
    | NEVER use simple passwords in production!
    | Generate strong password: https://www.random.org/passwords/
    */
    'password'     => 'YOUR_STRONG_DATABASE_PASSWORD_HERE',

    /*
    | Database name
    | ⚠️ Replace with your actual database name from cPanel
    | Format on shared hosting: cpanel_username_dbname
    | Example: mysite_insurance_erp
    */
    'database'     => 'YOUR_CPANEL_USERNAME_insurance_erp',

    /*
    | Database driver
    | For MySQL/MariaDB: mysqli (recommended) or pdo
    */
    'dbdriver'     => 'mysqli',

    /*
    | Database prefix
    | Leave empty if not using table prefixes
    */
    'dbprefix'     => '',

    /*
    | Persistent connections
    | ⚠️ PRODUCTION: Set to FALSE to avoid connection exhaustion
    */
    'pconnect'     => FALSE,

    /*
    | Database debugging
    | ⚠️ PRODUCTION: Must be FALSE to avoid exposing database errors to users
    | Errors will still be logged in application/logs/
    */
    'db_debug'     => FALSE,

    /*
    | Query caching
    | Set to TRUE for better performance if your queries benefit from caching
    */
    'cache_on'     => FALSE,

    /*
    | Cache directory
    | Only needed if cache_on is TRUE
    */
    'cachedir'     => APPPATH . 'cache/db/',

    /*
    | Character set
    | ⚠️ Must be utf8mb4 for full Unicode support (including emojis)
    */
    'char_set'     => 'utf8mb4',

    /*
    | Database collation
    | ⚠️ Must match char_set: utf8mb4_unicode_ci
    */
    'dbcollat'     => 'utf8mb4_unicode_ci',

    /*
    | Table prefix swapping
    */
    'swap_pre'     => '',

    /*
    | Encryption
    | Set to TRUE if using SSL/TLS for database connection
    | Requires additional configuration
    */
    'encrypt'      => FALSE,

    /*
    | Database compression
    | Can improve performance over slow connections
    */
    'compress'     => FALSE,

    /*
    | Strict mode
    | ⚠️ PRODUCTION: Set to FALSE to avoid issues with MySQL strict mode
    | Alternatively, configure MySQL to use non-strict mode
    */
    'stricton'     => FALSE,

    /*
    | Failover configuration
    | Configure additional database servers for automatic failover
    */
    'failover'     => array(),

    /*
    | Save queries
    | ⚠️ PRODUCTION: Must be FALSE to save memory
    */
    'save_queries' => FALSE
);

/*
|--------------------------------------------------------------------------
| OPTIONAL: Read-Only Database Configuration (for load balancing)
|--------------------------------------------------------------------------
|
| If you have a read replica or read-only database for SELECT queries,
| you can configure it here and switch between connections in your models.
|
*/

$db['readonly'] = array(
    'dsn'          => '',
    'hostname'     => 'localhost', // Read replica hostname
    'username'     => 'YOUR_READONLY_USERNAME',
    'password'     => 'YOUR_READONLY_PASSWORD',
    'database'     => 'YOUR_DATABASE_NAME',
    'dbdriver'     => 'mysqli',
    'dbprefix'     => '',
    'pconnect'     => FALSE,
    'db_debug'     => FALSE,
    'cache_on'     => FALSE,
    'cachedir'     => '',
    'char_set'     => 'utf8mb4',
    'dbcollat'     => 'utf8mb4_unicode_ci',
    'swap_pre'     => '',
    'encrypt'      => FALSE,
    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => array(),
    'save_queries' => FALSE
);

/*
|--------------------------------------------------------------------------
| OPTIONAL: Backup Database Configuration
|--------------------------------------------------------------------------
|
| For automated backups to a different database server
|
*/

$db['backup'] = array(
    'dsn'          => '',
    'hostname'     => 'backup-server.yourdomain.com',
    'username'     => 'backup_user',
    'password'     => 'BACKUP_SERVER_PASSWORD',
    'database'     => 'insurance_erp_backup',
    'dbdriver'     => 'mysqli',
    'dbprefix'     => '',
    'pconnect'     => FALSE,
    'db_debug'     => FALSE,
    'cache_on'     => FALSE,
    'cachedir'     => '',
    'char_set'     => 'utf8mb4',
    'dbcollat'     => 'utf8mb4_unicode_ci',
    'swap_pre'     => '',
    'encrypt'      => FALSE,
    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => array(),
    'save_queries' => FALSE
);

/*
|--------------------------------------------------------------------------
| HOSTGATOR-SPECIFIC NOTES
|--------------------------------------------------------------------------
|
| 1. Create Database in cPanel:
|    - Login to cPanel
|    - Navigate to MySQL Databases
|    - Create new database: insurance_erp
|    - Full name will be: cpanelusername_insurance_erp
|
| 2. Create Database User:
|    - In MySQL Databases section
|    - Create new user: erp_user
|    - Full name will be: cpanelusername_erp_user
|    - Generate strong password (save it!)
|
| 3. Grant Privileges:
|    - Add user to database
|    - Grant ALL PRIVILEGES
|
| 4. Import Database:
|    - Use phpMyAdmin or command line
|    - Import: database/insurance_erp_complete_schema.sql
|    - Or use: database/import_database.sh script
|
| 5. Update Configuration:
|    - Replace placeholders above with actual values
|    - hostname: localhost (always on HostGator)
|    - username: cpanelusername_erp_user
|    - password: [your generated password]
|    - database: cpanelusername_insurance_erp
|
| 6. Test Connection:
|    - Try logging into the application
|    - Check application/logs/ for any database errors
|
|--------------------------------------------------------------------------
| PRODUCTION SECURITY CHECKLIST
|--------------------------------------------------------------------------
|
| ✅ Use strong, unique password (20+ characters)
| ✅ Set db_debug to FALSE
| ✅ Set pconnect to FALSE
| ✅ Set save_queries to FALSE
| ✅ Use utf8mb4 character set
| ✅ Database user has only necessary privileges
| ✅ Database is NOT accessible from external IPs (unless required)
| ✅ Regular backups are configured
| ✅ Database password is NOT in version control
| ✅ Consider using environment variables for credentials
|
|--------------------------------------------------------------------------
| TROUBLESHOOTING
|--------------------------------------------------------------------------
|
| ERROR: "Unable to connect to database"
| SOLUTION:
|   - Verify hostname is 'localhost' on shared hosting
|   - Check username/password are correct
|   - Ensure database user has privileges
|   - Check phpMyAdmin to verify database exists
|
| ERROR: "Incorrect string value" or character encoding issues
| SOLUTION:
|   - Ensure char_set is 'utf8mb4'
|   - Ensure dbcollat is 'utf8mb4_unicode_ci'
|   - Verify database was created with utf8mb4 encoding
|
| ERROR: "Too many connections"
| SOLUTION:
|   - Set pconnect to FALSE
|   - Check for unclosed database connections in code
|   - Contact hosting provider to increase connection limit
|
| ERROR: "Access denied for user"
| SOLUTION:
|   - Verify username format: cpanelusername_dbuser
|   - Check database user was added to the database in cPanel
|   - Verify user has correct privileges
|
*/

/*
|--------------------------------------------------------------------------
| ENVIRONMENT-BASED CONFIGURATION (Advanced)
|--------------------------------------------------------------------------
|
| For multiple environments (development, staging, production), you can
| use environment variables or separate config files:
|
| 1. Using Environment Variables (recommended):
|
|    $db['default']['hostname'] = getenv('DB_HOST') ?: 'localhost';
|    $db['default']['username'] = getenv('DB_USER') ?: 'default_user';
|    $db['default']['password'] = getenv('DB_PASS') ?: '';
|    $db['default']['database'] = getenv('DB_NAME') ?: 'insurance_erp';
|
| 2. Using .env file (requires vlucas/phpdotenv library):
|
|    Install: composer require vlucas/phpdotenv
|    Create .env file in root (add to .gitignore!)
|    Load in index.php before bootstrapping
|
| 3. Using separate config files per environment:
|
|    database.development.php
|    database.staging.php
|    database.production.php
|
|    Load appropriate file based on ENVIRONMENT constant in index.php
|
*/

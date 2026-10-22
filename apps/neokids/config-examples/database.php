<?php
// Copy to .private/database.php. Configure environment values on the PHP service.
return [
    'sql_host' => getenv('NEOKIDS_DB_HOST') ?: '127.0.0.1:3306',
    'sql_usuario' => getenv('NEOKIDS_DB_USER') ?: '',
    'sql_pass' => getenv('NEOKIDS_DB_PASSWORD') ?: '',
    'sql_db' => getenv('NEOKIDS_DB_NAME') ?: 'neokids',
];

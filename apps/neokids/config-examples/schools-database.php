<?php
// Optional link to the separate school registry. Copy to .private/.
return [
    'sql_host' => getenv('NEOKIDS_SCHOOLS_DB_HOST') ?: '127.0.0.1:3306',
    'sql_usuario' => getenv('NEOKIDS_SCHOOLS_DB_USER') ?: '',
    'sql_pass' => getenv('NEOKIDS_SCHOOLS_DB_PASSWORD') ?: '',
    'sql_db' => getenv('NEOKIDS_SCHOOLS_DB_NAME') ?: 'lapices',
];

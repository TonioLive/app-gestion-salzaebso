<?php
// Copy to .private/pool-config.php. Keep equipment addresses and secrets private.
return [
    'water_base_url' => getenv('NEOKIDS_POOL_WATER_URL') ?: '',
    'air_base_url' => getenv('NEOKIDS_POOL_AIR_URL') ?: '',
    'controller_user' => getenv('NEOKIDS_POOL_USER') ?: '',
    'controller_password' => getenv('NEOKIDS_POOL_PASSWORD') ?: '',
    'vista_user' => getenv('NEOKIDS_VISTAPOOL_USER') ?: '',
    'vista_password' => getenv('NEOKIDS_VISTAPOOL_PASSWORD') ?: '',
    'vista_pool' => getenv('NEOKIDS_VISTAPOOL_POOL_ID') ?: '',
    'vista_api_key' => getenv('NEOKIDS_VISTAPOOL_API_KEY') ?: '',
];

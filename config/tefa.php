<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TEFA API Key
    |--------------------------------------------------------------------------
    |
    | API key yang wajib disertakan pada header X-API-Key di setiap request
    | ke endpoint /api/v1/tefa/*. Simpan nilainya di .env sebagai TEFA_API_KEY.
    |
    */
    'api_key' => env('TEFA_API_KEY'),
];

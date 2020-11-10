<?php

namespace App\Services\ApiSite;

use App\Traits\ConsumesExternalService;

class UbigeoService {

    use ConsumesExternalService;

    /**
     * The base uri to be used to consume the site service
     *
     * @var string
     */
    public $baseUri;

    public $secret;

    public function __construct()
    {
        $this->baseUri = config('services.apisite.base_uri');
        $this->secret = config('services.apisite.secret');
    }

    /**
     * Obtener ubicaciones y datos de un pais
     *
     * @param  string $lang
     * @param  int $site_id
     * @param  string $cod
     * @return string
     */
    public function ubigeoIndex($lang, int $site_id, $cod) {
        return $this->performRequest('GET', $lang . '/admin/sites/' . $site_id . '/ubigeo?cod='.$cod, [], ['Accept' => 'application/json']);
    }

}

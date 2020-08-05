<?php

namespace App\Services\ApiSite;

use App\Traits\ConsumesExternalService;

class SiteService {

    use ConsumesExternalService;

    /**
     * The base uri to be used to consume the site service
     *
     * @var string
     */
    public $baseUri;

    public function __construct()
    {
        $this->baseUri = config('services.apisite.base_uri');
    }

    /**
     * Obtener la data de un sitio
     *
     * @param  string $lang
     * @param  int $site_id
     * @return void
     */
    public function getSiteIndex($lang, int $site_id) {
        return $this->performRequest('GET', $lang . '/admin/sites/' . $site_id, [], ['Accept' => 'application/json']);
    }

    /**
     * Registrar un sitio
     *
     * @param  string $lang
     * @param  array $data
     * @return void
     */
    public function siteStore($lang, $data) {
        return $this->performRequest('POST', $lang . '/admin/sites', $data, ['Accept' => 'application/json']);
    }

    /**
     * Actualizar un sitio
     *
     * @param  string $lang
     * @param  int $site_id
     * @param  mixed $data
     * @return void
     */
    public function siteUpdate($lang, int $site_id, $data) {
        return $this->performRequest("PUT", $lang . '/admin/sites/' . $site_id, $data, ['Accept' => 'application/json']);
    }


    /**
     * Obtener data de un sitio
     *
     * @param  string $lang
     * @param  int $site_id
     * @return void
     */
    public function simple($lang, int $site_id) {
        return $this->performRequest("GET", $lang . '/admin/sites/' . $site_id . '/simples', [], ['Accept' => 'application/json']);
    }

}

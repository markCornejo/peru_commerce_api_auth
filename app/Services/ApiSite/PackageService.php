<?php

namespace App\Services\ApiSite;

use App\Traits\ConsumesExternalService;

class PackageService {

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
     * Obtener data de un package
     *
     * @param  string $lang
     * @param  int $package_id
     * @return void
     */
    public function show($lang, int $package_id) {
        return $this->performRequest("GET", $lang . '/master/salespackages/' . $package_id, [], ['Accept' => 'application/json']);
    }

}

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

    public $secret;

    public function __construct()
    {
        $this->baseUri = config('services.apisite.base_uri');
        $this->secret = config('services.apisite.secret');
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
        return $this->performRequest("GET", $lang . '/sites/' . $site_id . '/simples', [], ['Accept' => 'application/json']);
    }

    /**
     * Obtener todos las imagenes de un micrositio
     *
     * @param  string $lang
     * @param  int $site_id
     * @return void
     */
    public function getImages($lang, int $site_id, int $skip, int $take) {
        return $this->performRequest("GET", $lang . '/admin/sites/' . $site_id . '/images?skip_image='.$skip.'&take_image='.$take, [], ['Accept' => 'application/json']);
    }

    /**
     * Guardar todas las imagenes
     *
     * @param  string $lang
     * @param  int $site_id
     * @param  array $data
     * @return void
     */
    public function setImages($lang, int $site_id, $data) {
        return $this->performRequest("POST", $lang . '/admin/sites/' . $site_id . '/images', $data, ['Accept' => 'application/json']);
    }

    /**
     * Eliminar imagen
     *
     * @param  string $lang
     * @param  int $site_id
     * @param  int $image_id
     * @return void
     */
    public function delImages($lang, int $site_id, int $image_id) {
        return $this->performRequest("DELETE", $lang . '/admin/sites/' . $site_id . '/images/' . $image_id, [], ['Accept' => 'application/json']);
    }

    /**
     * cortar imagenes gallery
     *
     * @param  string $lang
     * @param  int $site_id
     * @param  int $image_id
     * @param  array $data
     * @return void
     */
    public function cutImagesCropper($lang, int $site_id, int $image_id, $data) {
        return $this->performRequest("PUT", $lang . '/admin/sites/' . $site_id . '/images/' . $image_id . '/cropper', $data, ['Accept' => 'application/json']);
    }


    /**
     * Registrar locacion para un lugar
     *
     * @param  string $lang
     * @param  int $site_id
     * @param  array $data
     * @return void
     */
    public function siteLocationStore($lang, int $site_id, $data) {
        return $this->performRequest("POST", $lang . '/admin/sites/' . $site_id . '/locations', $data, ['Accept' => 'application/json']);
    }


}

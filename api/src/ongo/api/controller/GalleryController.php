<?php

namespace ongo\api\controller;

use ongo\shared\entity\CityEntity;
use ongo\shared\entity\CountryEntity;
use ongo\shared\entity\GalleryEntity;
use ongo\shared\entity\PhotographerEntity;
use ongo\shared\entity\PlaceEntity;
use ongo\shared\entity\SerializableEntity;
use Doctrine\DBAL\Connection;
use ongo\shared\model\CityModel;
use ongo\shared\model\CountryModel;
use ongo\shared\model\GalleryModel;
use ongo\shared\model\PhotographerModel;
use ongo\shared\model\PlaceModel;
use Symfony\Component\HttpFoundation\JsonResponse;

final class GalleryController
{
    /** @var Connection */
    private $dbConn;

    /**
     * TextController constructor.
     * @param Connection $dbConn
     */
    public function __construct(Connection $dbConn)
    {
        $this->dbConn = $dbConn;
    }

    /**
     * @param int $limit
     * @return JsonResponse
     * @throws \Exception
     */
    public function top($limit = 3)
    {
        $model = new GalleryModel($this->dbConn);
        $photographerModel = new PhotographerModel($this->dbConn);
        $placeModel = new PlaceModel($this->dbConn);
        $cityModel = new CityModel($this->dbConn);
        $countryModel = new CountryModel($this->dbConn);


        $galleries = $model->top($limit);

        $photographers = $photographerModel->fromGalleries($galleries);
        $places = $placeModel->fromGalleries($galleries);
        $cities = $cityModel->fromPlaces($places);
        $countries = $countryModel->fromCities($cities);

        $ret = array_map(function (GalleryEntity $gallery) use ($photographers, $places, $cities, $countries) {
            /** @var PhotographerEntity $photographer */
            $photographer = $photographers[$gallery->getPhotographId()];
            /** @var PlaceEntity $place */
            $place = $places[$gallery->getPlaceId()];
            /** @var CityEntity $city */
            $city = $cities[$place->getCityId()];
            /** @var CountryEntity $country */
            $country = $countries[$city->getCountryId()];
            $data = $gallery->serialize($this->dbConn);
            $data['photographer'] = $photographer->serialize($this->dbConn);
            $data['place'] = $place->serialize($this->dbConn);
            $data['city'] = $city->serialize($this->dbConn);
            $data['country'] = $country->serialize($this->dbConn);
            return $data;
        }, $galleries);

        return new JsonResponse($ret);

    }
}


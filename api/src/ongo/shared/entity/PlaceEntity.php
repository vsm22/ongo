<?php

namespace ongo\shared\entity;

use ongo\shared\model\I18nVersionModel;
use Doctrine\DBAL\Connection;

final class PlaceEntity extends SerializableEntity
{
    /** @var  int */
    private $id;
    /** @var  string */
    private $name;
    /** @var  int */
    private $city_id;
    /** @var  string */
    private $logo;

    /**
     * PlaceEntity constructor.
     * @param int $id
     * @param string $name
     * @param int $city_id
     * @param string $logo
     */
    public function __construct($id, $name, $city_id, $logo)
    {
        $this->id = $id;
        $this->name = $name;
        $this->city_id = $city_id;
        $this->logo = $logo;
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->city_id;
    }

    /**
     * @param Connection $dbConn
     * @param bool $deep
     * @return array
     */
    public function serialize(Connection $dbConn, $deep = true)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city_id' => $this->city_id,
            'logo' => $this->logo,
        ];
    }
}
?>
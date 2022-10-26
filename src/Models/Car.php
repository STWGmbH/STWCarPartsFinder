<?php

namespace d2gPmPluginCarPartsFinder\Models;

use d2gPmPluginCarPartsFinder\Providers\PluginServiceProvider;
use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * Class Car
 *
 * @property int    $id
 * @property int    $platformId
 * @property string $name
 * @property string $ktype
 * @property string $createdAt
 * @property string $updatedAt
 * @property int    $position
 *
 * @Nullable(columns={"ktype"})
 *
 * @Relation(model="d2gPmPluginCarPartsFinder\Models\CarPlatform", name="car_platform_platform_id_fk", attribute="id", column="platformId", onUpdate="Cascade", onDelete="Cascade")
 */

class Car extends Model
{

    const TABLE_NAME = 'cars';

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var int $platformId
     */
    public $platformId;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $ktype
     */
    public $ktype;

    /**
     * @var string
     */
    public $createdAt;

    /**
     * @var string
     */
    public $updatedAt;

    /**
     * @var int
     */
    public $position;


    protected $primaryKeyFieldName = 'id';
    protected $primaryKeyFieldType = self::FIELD_TYPE_INT;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return PluginServiceProvider::PLUGIN_NAME.'::'.self::TABLE_NAME;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'platformId' => $this->platformId,
            'name' => $this->name,
            'ktype' => $this->ktype,
            'position' => $this->position,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }

    /**
     * @param array $data
     */
    public function fill(array $data)
    {
        if($data['id']) {
            $this->id = $data['id'];
        }

        if($data['platformId']) {
            $this->platformId = $data['platformId'];
        }

        if($data['name']) {
            $this->name = $data['name'];
        }

        if($data['ktype']) {
            $this->ktype = $data['ktype'];
        }

        if($data['createdAt']) {
            $this->createdAt = $data['createdAt'];
        }

        if($data['updatedAt']) {
            $this->updatedAt = $data['updatedAt'];
        }

        if($data['position']) {
            $this->position = $data['position'];
        }
    }

    /**
     * @return string
     */
    function jsonSerialize()
    {
        return json_encode($this->toArray());
    }

}

<?php

namespace d2gPmPluginCarPartsFinder\Models;

use d2gPmPluginCarPartsFinder\Providers\PluginServiceProvider;
use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * Class CarPlatform
 *
 * @property int        $id
 * @property int        $modelId
 * @property string     $name
 * @property int        $order
 * @property string     $createdAt
 * @property string     $updatedAt
 * @property int        $position
 *
 * @Relation(model="d2gPmPluginCarPartsFinder\Models\CarModel", name="car_model_model_id_fk", attribute="id", column="modelId", onUpdate="Cascade", onDelete="Cascade")
 * @Nullable(columns={"order"})
 *
 */

class CarType extends Model
{
    const TABLE_NAME = 'cars_types';

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $modelId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $order;

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
            'modelId' => $this->modelId,
            'name' => $this->name,
            'order' => $this->order,
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

        if($data['modelId']) {
            $this->modelId = $data['modelId'];
        }

        if($data['name']) {
            $this->name = $data['name'];
        }

        if($data['order']) {
            $this->order = $data['order'];
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

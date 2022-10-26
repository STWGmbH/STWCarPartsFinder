<?php

namespace d2gPmPluginCarPartsFinder\Models;

use d2gPmPluginCarPartsFinder\Providers\PluginServiceProvider;
use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * Class Car
 *
 * @property int    $id
 * @property int    $carId
 * @property string $hsn
 * @property string $tsn
 * @property string $createdAt
 * @property string $updatedAt
 **
 * @Relation(model="d2gPmPluginCarPartsFinder\Models\Car", name="car_car_id_fk", attribute="id", column="carId", onUpdate="Cascade", onDelete="Cascade")
 */

class HSNTSN extends Model
{

    const TABLE_NAME = 'hsntsn';

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var int $carId
     */
    public $carId;

    /**
     * @var string $hsn
     */
    public $hsn;

    /**
     * @var string $tsn
     */
    public $tsn;

    /**
     * @var string
     */
    public $createdAt;

    /**
     * @var string
     */
    public $updatedAt;


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
            'carId' => $this->carId,
            'hsn' => $this->hsn,
            'tsn' => $this->tsn,
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

        if($data['carId']) {
            $this->carId = $data['carId'];
        }

        if($data['hsn']) {
            $this->hsn = $data['hsn'];
        }

        if($data['tsn']) {
            $this->tsn = $data['tsn'];
        }

        if($data['createdAt']) {
            $this->createdAt = $data['createdAt'];
        }

        if($data['updatedAt']) {
            $this->updatedAt = $data['updatedAt'];
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

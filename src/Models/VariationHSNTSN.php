<?php

namespace d2gPmPluginCarPartsFinder\Models;

use d2gPmPluginCarPartsFinder\Providers\PluginServiceProvider;
use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * Class VariationCar
 *
 * @property int    $id
 * @property int    $hsntsnId
 * @property int    $variationId
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @Relation(model="d2gPmPluginCarPartsFinder\Models\HSNTSN", name="hsntsn_hsntsn_id_fk", attribute="id", column="hsntsnId", onUpdate="Cascade", onDelete="Cascade")
 * @Relation(model="Plenty\Modules\Item\Variation\Models\Variation", name="variation_variation_id_fk", attribute="id", column="variationId", onUpdate="Cascade", onDelete="Cascade")
 */

class VariationHSNTSN extends Model
{

    const TABLE_NAME = 'variation_hsntsn';

    /**
     * @var int $id
     */
    public $id;

    /**
     * @var int $hsntsnId
     */
    public $hsntsnId;

    /**
     * @var int $variationId
     */
    public $variationId;

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
            'hsntsnId' => $this->hsntsnId,
            'variationId' => $this->variationId,
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

        if($data['variationId']) {
            $this->variationId = $data['variationId'];
        }

        if($data['hsntsnId']) {
            $this->hsntsnId = $data['hsntsnId'];
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

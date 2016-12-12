<?php

namespace Mcms\Pages\Models;

use Config;
use Mcms\Core\Models\ExtraField as BaseExtraField;


/**
 * Class Page
 * @package Mcms\Pages\Models
 */
class ExtraField extends BaseExtraField
{

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
    protected $pagesModel;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->pagesModel = (Config::has('pages.page')) ? Config::get('pages.page') : Page::class;
    }

    public function item()
    {
        return $this->BelongsTo($this->pagesModel, 'item_id');
    }

}

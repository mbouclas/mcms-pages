<?php

namespace Mcms\Pages\Models;

use Config;
use Mcms\Core\Models\ExtraFieldValue as BaseExtraFieldValue;


/**
 * Class Page
 * @package Mcms\Pages\Models
 */
class ExtraFieldValue extends BaseExtraFieldValue
{
    protected $pagesModel;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->pagesModel = (Config::has('pages.page')) ? Config::get('pages.page') : Page::class;
    }

    public function field()
    {
        return $this->BelongsTo(ExtraField::class, 'extra_field_id');
    }

}

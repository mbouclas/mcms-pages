<?php

namespace Mcms\Pages\Models;


use Config;
use Mcms\Core\Models\DynamicTable as BaseDynamicTable;
use Mcms\FrontEnd\Helpers\Sluggable;

class DynamicTable extends BaseDynamicTable
{
    use Sluggable;

    public $itemModel;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->itemModel = (Config::has('pages.page')) ? Config::get('pages.page') : Page::class;
    }

    public function pages()
    {
        return $this->belongsToMany($this->itemModel);
    }


}

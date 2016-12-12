<?php

namespace Mcms\Pages\Presenters;
use App;
use Mcms\Core\Services\Presenter\Presenter;
use Mcms\Pages\Models\PageCategory;

/**
 * Works as $category->present()->methodName
 *
 * Class PageCategoryPresenter
 * @package Mcms\Pages\Presenters
 */
class PageCategoryPresenter extends Presenter
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * PagePresenter constructor.
     * @param PageCategory $pageCategory
     */
    public function __construct(PageCategory $pageCategory)
    {
        $this->lang = App::getLocale();

        parent::__construct($pageCategory);
    }


}
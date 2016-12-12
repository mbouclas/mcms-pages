<?php

namespace Mcms\Pages\Presenters;
use App;
use Mcms\Core\Services\Presenter\Presenter;
use Mcms\Pages\Models\Page;

/**
 * Works as $page->present()->methodName
 *
 * Class PagePresenter
 * @package Mcms\Pages\Presenters
 */
class PagePresenter extends Presenter
{
    /**
     * @var string
     */
    protected $lang;

    /**
     * PagePresenter constructor.
     * @param Page $page
     */
    public function __construct(Page $page)
    {
        $this->lang = App::getLocale();

        parent::__construct($page);
    }


}
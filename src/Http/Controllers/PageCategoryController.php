<?php

namespace Mcms\Pages\Http\Controllers;

use App\Http\Controllers\Controller;
use Config;
use Mcms\Core\Services\SettingsManager\SettingsManagerService;
use Mcms\Pages\Services\PageCategory\PageCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use ItemConnector;


class PageCategoryController extends Controller
{
    protected $category;

    public function __construct(PageCategoryService $pageCategory)
    {
        $this->category = $pageCategory;
    }

    public function index()
    {
        $results = $this->category
            ->model
            ->defaultOrder()
            ->get()
            ->toTree();

        return $results;
    }

    public function tree()
    {
        return $this->category->htmlTree();
    }

    public function store(Request $request)
    {
        $data = $request->toArray();
        $data['user_id'] = \Auth::user()->id;
        $parentId = (!isset($data['id']) && isset($data['parent_id'])) ? $data['parent_id'] : null;
        return $this->category->store($data, $parentId);
    }


    public function update(Request $request, $id)
    {
        return $this->category->update($id, $request->toArray());
    }


    public function destroy($id)
    {
        $result = $this->category->destroy($id);
        return $this->index();
    }

    public function show($id)
    {

        return [
            'item' => $this->category->model->with(['image', 'featured.item'])->find($id),
            'settings' => SettingsManagerService::get('pageCategories'),
            'connectors' => ItemConnector::connectors(),
            'seoFields' => Config::get('seo')
        ];
    }

    /**
     * Rebuild the entire tree
     *
     * @param Request $request
     * @return mixed
     */
    public function rebuild(Request $request)
    {
        $this->category
            ->model
            ->rebuildTree($request->all());

        return $this->category
            ->model
            ->defaultOrder()
            ->get()
            ->toTree();
    }
}

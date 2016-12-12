<?php

namespace Mcms\Pages\Http\Controllers;

use App\Http\Controllers\Controller;
use Config;
use Mcms\Core\ExtraFields\ExtraFields;
use Mcms\Core\Models\Filters\ExtraFieldFilters;
use Mcms\Core\Services\SettingsManager\SettingsManagerService;
use Mcms\Pages\Models\Filters\PageFilters;
use Mcms\Pages\Models\Page;
use Mcms\Pages\Models\PageCategory;
use Mcms\Pages\Services\Page\PageService;
use Illuminate\Http\Request;
use ItemConnector;

class PageController extends Controller
{
    protected $page;
    protected $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index(PageFilters $filters, Request $request)
    {
        /*        $category = PageCategory::find(4);
                $category->children()->create([
                    'title' => ['en'=>'Jobs'],
                    'slug' => str_slug('Jobs'),
                    'description' => ['en'=>''],
                    'user_id' => \Auth::user()->id,
                    'orderBy' => 0,
                    'active' => true
                ]);*/
        /*        PageCategory::create([
                    'title' => ['en'=>'Properties'],
                    'slug' => str_slug('Properties'),
                    'description' => ['en'=>''],
                    'user_id' => \Auth::user()->id,
                    'orderBy' => 0,
                    'active' => true
                ]);*/

        /*        $page = Page::create([
                    'title' => ['en'=>'The team'],
                    'slug' => str_slug('The team'),
                    'description' => ['en'=>'sdfgs sgsg sdgsdg'],
                    'description_long' => ['en'=>'24rt243 tgf42 g432'],
                    'user_id' => \Auth::user()->id,
                    'active' => true
                ]);

                $category = PageCategory::find(4);

                $page->categories()->attach([$category->id, 5]);*/

//        return Page::with('categories')->find(129);

        /*        \DB::listen(function($sql) {
                    var_dump($sql->sql);
                    var_dump($sql->bindings);
                });*/

//        return Page::limit(10)->filter($filters)->get();
//        return PageCategory::with('pages')->find(4);


//        return $pageService->filter($filters);


        /*        $page = $pageService->store([
                    'title' => 'a new page',
                    'slug' => str_slug('a new page'),
                    'active' => true,
                    'user_id' => 2,
                    'categories' => [
                        ['id'=>3],
                        ['id'=>4,'main'=>true]
                    ]
                ]);*/


        /*        $page = Page::with('categories')->find(109);
                $update = $page->toArray();
                $update['categories'] = [
                    ['id'=>4],
                    ['id'=>5,'main'=>true]
                ];
                $page = $pageService->update($page->id, $update);*/

        \DB::listen(function ($query) {
//            print_r($query->sql);
//            print_r($query->bindings);
            // $query->time
        });
        $limit = ($request->has('limit')) ? (int)$request->input('limit') : 10;
        return $this->pageService->model->with(['categories', 'images'])
            ->filter($filters)
            ->paginate($limit);
    }

    public function store(Request $request)
    {
        $data = $request->toArray();
        $data['user_id'] = \Auth::user()->id;
        return $this->pageService->store($data);
    }


    public function update(Request $request, $id)
    {
        return $this->pageService->update($id, $request->toArray());
    }


    public function destroy($id)
    {
        $result = $this->pageService->destroy($id);
        return ['success' => $result];
    }

    public function show($id, ExtraFieldFilters $filters)
    {
        $imageCategories = Config::get('pages.items.images.types');
        $extraFieldService = new ExtraFields();
        \DB::listen(function ($query) {
//            print_r($query->sql);
//            print_r($query->bindings);
            // $query->time
        });
        $filters->request->merge(['model' => str_replace('\\', '\\\\', get_class($this->pageService->model))]);

        return [
            'item' => $this->pageService->findOne($id, ['related', 'categories',
                'galleries', 'tagged', 'files', 'extraFields', 'extraFields.field']),
            'imageCategories' => $imageCategories,
            'extraFields' => $extraFieldService->model->filter($filters)->get(),
            'imageCopies' => Config::get('pages.items.images'),
            'config' => Config::get('pages.items'),
            'tags' => $this->pageService->model->existingTags(),
            'settings' => SettingsManagerService::get('pages'),
            'connectors' => ItemConnector::connectors(),
            'seoFields' => Config::get('seo')
        ];
    }

    public function preview($id)
    {
        $item = Page::find($id);
        return response(['url' => $item->createUrl()]);
    }
}

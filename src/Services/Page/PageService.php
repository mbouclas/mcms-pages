<?php

namespace Mcms\Pages\Services\Page;


use App;
use Config;
use Event;
use Mcms\Core\Helpers\Strings;
use Mcms\Core\Models\Image;
use Mcms\Core\Models\MenuItem;

use Mcms\Core\QueryFilters\Filterable;
use Mcms\Core\Services\Image\GroupImagesByType;
use Mcms\Core\Traits\FixTags;
use Mcms\FrontEnd\Services\PermalinkArchive;
use Mcms\Pages\Exceptions\InvalidPageFormatException;
use Mcms\Pages\Models\Featured;
use Mcms\Pages\Models\Page;
use Mcms\Pages\Models\Related;
use Str;

/**
 * Class PageService
 * @package Mcms\Pages\Services\Page
 */
class PageService
{
    use Filterable, FixTags;

    /**
     * @var Page
     */
    protected $page;
    /**
     * @var
     */
    public $model;

    protected $validator;

    protected $imageGrouping;

    /**
     * PageService constructor.
     * @param Page $page
     */
    public function __construct()
    {
        $model = (Config::has('pages.page')) ? Config::get('pages.page') : Page::class;
        $this->page = $this->model = new $model;
        $this->validator = new PageValidator();
        $this->imageGrouping = new GroupImagesByType();
    }

    /**
     * Filters the translations based on filters provided
     * Legend has it that it will filter properly role based queries.
     * So, if i am an admin, i should not be able to see the super users
     *
     * @param $filters
     */

    public function filter($filters, array $options = [])
    {
        $results = $this->page->filter($filters);
        $results = (array_key_exists('orderBy', $options)) ? $results->orderBy($options['orderBy']) : $results->orderBy('created_at', 'asc');
        $limit = ($filters->request->has('limit')) ? $filters->request->input('limit') : 10;
        $results = $results->paginate($limit);


        return $results;
    }

    /**
     * @param $id
     * @param array $page
     * @return array
     */
    public function update($id, array $page)
    {
        $Page = $this->page->find($id);
        //link has changed, write it out as a 301
        //create link
        $oldLink = $Page->generateSlug();
        $newLink = $Page->generateSlug($page);

        if ($oldLink != $newLink){
            //write 301

            PermalinkArchive::add($oldLink, $newLink);
        }
        $Page->update($page);
        //update relations
        $Page->categories()->sync($this->sortOutCategories($page['categories']));
        //sanitize the model
        $Page = $this->saveRelated($page, $Page);

        $Page = $this->fixTags($page, $Page);
        if (isset($page['extra_fields'])){
            $Page->extraFieldValues()->sync($Page->sortOutExtraFields($page['extra_fields']));
        }

        //emit an event so that some other bit of the app might catch it
        event('menu.item.sync',$Page);
        event('page.updated',$Page);

        return $Page;
    }

    /**
     * Create a new page
     *
     * @param array $page
     * @return static
     */
    public function store(array $page)
    {
        try {
            $this->validator->validate($page);
        }
        catch (InvalidPageFormatException $e){
            return $e->getMessage();
        }

        $page['slug'] = $this->setSlug($page);

        $Page = $this->page->create($page);
        $Page->categories()->attach($this->sortOutCategories($page['categories']));
        $Page = $this->saveRelated($page, $Page);
        $Page = $this->fixTags($page, $Page);
        event('page.created',$Page);
        return $Page;
    }

    /**
     * Delete a page
     *
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $item = $this->page->find($id);
        //delete images
        Image::where('model',get_class($this->model))->where('item_id', $id)->delete();
        //delete from menus
        MenuItem::where('model',get_class($this->model))->where('item_id', $id)->delete();
        //delete from featured
        Featured::where('model',get_class($this->model))->where('item_id', $id)->delete();
        //delete from related
        Related::where('model',get_class($this->model))->where('source_item_id', $id)->orWhere('item_id', $id)->delete();
        //emit an event so that some other bit of the app might catch it
        event('menu.item.destroy',$item);
        event('page.destroyed',$item);

        return $item->delete();
    }

    public function findOne($id, array $with = [], array $options = [
        'where' => []
    ])
    {

        $item = $this->model
            ->with($with);

        if (count($options['where']) > 0) {
            foreach ($options['where'] as $key => $value) {
                $item = $item->where($key, $value);
            }
        }

        $item = $item->find($id);

        if ($item){
            $item = $item->relatedItems();
            $item->related = collect($item->related);

            if (in_array('galleries', $with)){
                $item->images = $this->imageGrouping->group($item->galleries, \Config::get('pages.items.images.types'));
            }
        }



        return $item;
    }

    /**
     * create an array of category ids with the extra value main
     *
     * @param $itemCategories
     * @return array
     */
    private function sortOutCategories($itemCategories){
        $categories = [];
        foreach ($itemCategories as $category){
            $main = (! isset($category['main']) || ! $category['main']) ? false : true;
            $categories[$category['id']] = ['main' => $main];
        }

        return $categories;
    }

    private function setSlug($item){
        if ( ! isset($item['slug']) || ! $item['slug']){
            return Str::slug($item['title'][App::getLocale()]);
        }

        return $item['slug'];
    }


    /**
     * @param array $page
     * @param Page $Page
     * @return Page
     */
    private function saveRelated(array $page, Page $Page)
    {
        if ( ! isset($page['related']) || ! is_array($page['related'])  ){
            return $Page;
        }

        foreach ($page['related'] as $index => $item) {
            $page['related'][$index]['dest_model'] = ( ! isset($item['dest_model']))
                ? $page['related'][$index]['dest_model'] = $item['model']
                : $page['related'][$index]['dest_model'] = $item['dest_model'];
            $page['related'][$index]['model'] = get_class($Page);
        }

        $Page->related = $Page->saveRelated($page['related']);

        return $Page;
    }

    public function buildPermalink(array $item)
    {
        $stringHelpers = new Strings();

        return $stringHelpers->vksprintf(Config::get('pages.items.slug_pattern'), $item);
    }


}
<?php

namespace Mcms\Pages\Models;


use Config;
use DB;
use Mcms\Core\Models\Image;
use Mcms\Core\Traits\Featurable;
use Mcms\Core\Traits\Presentable;
use Mcms\Core\Traits\Userable;
use Mcms\Pages\Models\Collections\PageCategoriesCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Themsaid\Multilingual\Translatable;
use Kalnoy\Nestedset\NodeTrait;
use Mcms\FrontEnd\Helpers\Sluggable;

class PageCategory extends Model
{
    use Translatable, NodeTrait, Presentable, Featurable, Sluggable, Userable;

    protected $table = 'pages_categories';
    public $translatable = ['title', 'description'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'slug',
        'user_id',
        'settings',
        'active',
        'thumb',
        'orderBy',
    ];

    public $casts = [
        'title' => 'array',
        'description' => 'array',
        'settings' => 'array',
        'thumb' => 'array',
        'active' => 'boolean'
    ];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * Set the presenter class. Will add extra view-model presenter methods
     * @var string
     */
    protected $presenter = 'Mcms\Pages\Presenters\PageCategoryPresenter';

    protected $slugPattern = 'pages.categories.slug_pattern';
    protected $featuredModel;
    protected $pagesModel;
    protected $defaultRoute = 'pages';
    public $config;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->config = Config::get('pages.categories');
        $this->slugPattern = Config::get($this->slugPattern);
        $this->defaultRoute = (isset($this->config['route'])) ? $this->config['route'] : $this->defaultRoute;
        $this->featuredModel = (Config::has('pages.featured')) ? Config::get('pages.featured') : Featured::class;
        $this->pagesModel = (Config::has('pages.page')) ? Config::get('pages.page') : Page::class;
    }


    public function pages()
    {
        return $this->belongsToMany($this->pagesModel);
    }

    /**
     * @return mixed
     */
    public function image()
    {
        return $this->hasOne(Image::class, 'item_id')->where('type', 'thumb');
    }

    /**
     * @return mixed
     */
    public function featured()
    {
        return $this->hasMany($this->featuredModel, 'category_id')
            ->where('model', get_class($this))
            ->orderBy('orderBy','ASC');
    }

    public function itemCount($id = null)
    {
        $id = ( ! $id) ? $this->id : $id;
        $this->subcategories = $this->countSubCategoryItems($id);
        return $this;
    }

    private function countSubCategoryItems($parentId)
    {
        $leafs = new Collection();
        $parent = $this->find($parentId);

        if ($parent->children->count() == 0){
            return [];
        }

        $traverse = function ($categories) use (&$traverse, $leafs) {
            foreach ($categories as $category) {

                $item = [
                    'id' => $category->id,
                    'title' => $category->title,
                    'slug' => $category->slug,
                    'url' => $category->getSlug(),
                    'children' => $category->descendants->pluck('id')
                ];

                $leafs->push($item);

                $traverse($category->children);
            }
            return $leafs;
        };

        $tree = $traverse($parent->descendants->toTree());

        $ids = [];
        $sql = [];
        foreach ($tree as $item) {
            $ids[] = $item['id'];
            $qm = ['?'];

            if ($item['children']->count() > 0){
                foreach ($item['children'] as $child) {
                    $qm[] = '?';
                    $ids[] = $child;
                }
            }

            $sql[] = "(SELECT count(*) as total from page_page_category where page_category_id IN (".
                implode(',',$qm)
                .")) as c{$item['id']}";
        }

        $sql = implode(',', $sql);

        $res = DB::select("SELECT {$sql}",  $ids);
        $ret = new Collection();

        foreach ($res[0] as $index => $count){
            $id = trim($index, 'c');
            $collection = new Collection();
            $thisCat = $tree->where('id', (int) $id)->first();
            foreach ($thisCat as $key => $value) {
                $collection->{$key} = $value;
            }
            $collection->count = $count;
            $ret[$id] = $collection;
        }


        return $ret;
    }
    public function newCollection(array $models = []){
        return new PageCategoriesCollection($models);
    }

}

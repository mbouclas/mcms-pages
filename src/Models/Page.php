<?php

namespace Mcms\Pages\Models;
use Carbon\Carbon;
use Config;
use Conner\Likeable\Likeable;
use Conner\Tagging\Taggable;
use Mcms\Core\Traits\ExtraFields;
use Mcms\Core\Models\FileGallery;
use Mcms\Core\Models\Image;
use Mcms\Core\QueryFilters\Filterable;
use Mcms\Core\Traits\CustomImageSize;
use Mcms\Core\Traits\Presentable;
use Mcms\Core\Traits\Relateable;
use Mcms\Core\Traits\Userable;
use Mcms\FrontEnd\Helpers\Sluggable;
use Mcms\Pages\Models\Collections\PagesCollection;
use Illuminate\Database\Eloquent\Model;
use Themsaid\Multilingual\Translatable;

/**
 * Class Page
 * @package Mcms\Pages\Models
 */
class Page extends Model
{
    use Translatable, Filterable, Presentable, Taggable,
        Relateable, Sluggable, CustomImageSize, Userable, ExtraFields, Likeable;

    /**
     * @var string
     */
    protected $table = 'pages';
    /**
     * @var array
     */
    public $translatable = ['title', 'description', 'description_long'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description_long',
        'description',
        'slug',
        'thumb',
        'user_id',
        'settings',
        'active',
        'published_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'published_at'];

    /**
     * @var array
     */
    public $casts = [
        'title' => 'array',
        'description' => 'array',
        'description_long' => 'array',
        'settings' => 'array',
        'thumb' => 'array',
        'active' => 'boolean'
    ];

    /**
     * Set the presenter class. Will add extra view-model presenter methods
     * @var string
     */
    protected $presenter = 'Mcms\Pages\Presenters\PagePresenter';

    /**
     * Required to configure the images attached to this model
     *
     * @var
     */
    public $imageConfigurator = \Mcms\Pages\Services\Page\ImageConfigurator::class;
    public $fileConfigurator = \Mcms\Pages\Services\Page\FileConfigurator::class;
    public $dynamicTablesModel = DynamicTable::class;

    protected $slugPattern = 'pages.items.slug_pattern';
    protected $featuredModel;
    protected $relatedModel;
    protected $extraFieldModel;
    public $config;
    public $route;
    protected $defaultRoute = 'page';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->config = Config::get('pages.items');
        $this->defaultRoute = (isset($this->config['route'])) ? $this->config['route'] : $this->defaultRoute;
        $this->slugPattern = Config::get($this->slugPattern);
        $this->featuredModel = (Config::has('pages.featured')) ? Config::get('pages.featured') : Featured::class;
        $this->relatedModel = (Config::has('pages.related')) ? Config::get('pages.related') : Related::class;
        $this->dynamicTablesModel = (Config::has('pages.dynamicTablesModel')) ? Config::get('pages.dynamicTablesModel') : $this->dynamicTablesModel;
        $this->extraFieldModel = ExtraField::class;
        if (Config::has('pages.items.images.imageConfigurator')){
            $this->imageConfigurator = Config::get('pages.items.images.imageConfigurator');
        }

        if (Config::has('pages.items.files.fileConfigurator')){
            $this->fileConfigurator = Config::get('pages.items.files.fileConfigurator');
        }
    }

    private function assignMethod($class)
    {
        $child_class_functions = get_class_methods($class);

        foreach ($child_class_functions as $f){
//            $this->setAttribute($f, $c->$f);
        }
    }


    public function setPublishedAtAttribute($value)
    {
        if ( ! isset($value) || ! $value){
            $this->attributes['published_at'] = Carbon::now();
        }
        try {
            $this->attributes['published_at'] = Carbon::parse($value);
        }
        catch (\Exception $e){
            $this->attributes['published_at'] = Carbon::now();
        }
    }


    /**
     * Returns all the associated categories to this page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(PageCategory::class, 'page_page_category', 'page_id', 'page_category_id')
            ->withPivot('main')
            ->withTimestamps();
    }

    /**
     * Returns the main category of this page.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mainCategory()
    {
        return $this->belongsToMany(PageCategory::class)
            ->wherePivot('main', true);
    }

    /**
     * @return mixed
     */
    public function thumb()
    {
        return $this->hasOne(Image::class, 'item_id')
            ->where('model', __CLASS__)
            ->where('type', 'thumb');
    }

    /**
     * Grab all of the images with type image
     *
     * @return mixed
     */
    public function images()
    {
        return $this->hasMany(Image::class, 'item_id')
            ->where('model', __CLASS__)
            ->where('type', 'images')
            ->orderBy('orderBy','ASC');
    }

    public function files()
    {
        return $this->hasMany(FileGallery::class, 'item_id')
            ->where('model', __CLASS__)
            ->orderBy('orderBy','ASC');
    }


    /**
     * Use it with a closure for custom types
     *  ->with(['galleries' => function ($query) {
     * $query->where('type', 'myCustomType');
     * $query->orderBy('dueDate', 'asc');
     *
     * }])
     *
     * @return mixed
     */
    public function galleries()
    {
        return $this->hasMany(Image::class, 'item_id')
            ->where('model', __CLASS__)
            ->where('type', '!=', 'thumb');
    }

    public function featured()
    {
        return $this->belongsToMany($this->featuredModel, $this->table, 'id', 'id');
    }

/*    public function related()
    {
        return $this->hasManyThrough(Page::class, Related::class ,'source_item_id', 'id', 'item_id')
            ->where('model', get_class($this))
            ->orderBy('orderBy','ASC');
    }*/

    /**
     * @return mixed
     */
    public function related()
    {
        return $this->hasMany($this->relatedModel, 'source_item_id')
            ->where('model', get_class($this))
            ->orderBy('orderBy','ASC');
    }

    public function extraFields()
    {
        return $this->hasMany(ExtraFieldValue::class, 'item_id')
            ->where('model', get_class($this));
    }

    public function dynamicTables()
    {
        return $this->belongsToMany(DynamicTable::class,
            'dynamic_tables_items',
            'item_id',
            'dynamic_table_id')
            ->where('dynamic_tables_items.model', get_class($this))
            ->withTimestamps();
    }

    public function newCollection(array $models = []){
        return new PagesCollection($models);
    }
}

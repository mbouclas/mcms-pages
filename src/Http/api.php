<?php
Route::group(['middleware' => ['jwt.verify']], function($router) {
    $router->get('page/preview/{id}', 'Mcms\Pages\Http\Controllers\PageController@preview');
    $router->resource('page' ,'Mcms\Pages\Http\Controllers\PageController');
    $router->put('pageCategory/rebuild','Mcms\Pages\Http\Controllers\PageCategoryController@rebuild');
    $router->get('pageCategory/tree','Mcms\Pages\Http\Controllers\PageCategoryController@tree');
    $router->resource('pageCategory' ,'Mcms\Pages\Http\Controllers\PageCategoryController');
});
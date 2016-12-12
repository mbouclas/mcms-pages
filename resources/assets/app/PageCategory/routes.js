(function() {
    'use strict';

    angular.module('mcms.pages.pageCategory')
        .config(config);

    config.$inject = ['$routeProvider','PAGES_CONFIG'];

    function config($routeProvider,Config) {

        $routeProvider
            .when('/pages/categories', {
                templateUrl:  Config.templatesDir + 'PageCategory/index.html',
                controller: 'PageCategoryHomeController',
                controllerAs: 'VM',
                reloadOnSearch : false,
                resolve: {
                    init : ["AuthService", '$q', 'PageCategoryService', function (ACL, $q, Category) {
                        return (!ACL.inGates('cms.categories.menu')) ? $q.reject(403) : Category.get();
                    }]
                },
                name: 'pages-categories'
            })
            .when('/pages/categories/:id', {
                templateUrl:  Config.templatesDir + 'PageCategory/edit.html',
                controller: 'PageCategoryController',
                controllerAs: 'VM',
                reloadOnSearch : false,
                resolve: {
                    init : ["AuthService", '$q', 'PageCategoryService', '$route', function (ACL, $q, Category, $route) {
                        return (!ACL.inGates('cms.categories.menu')) ? $q.reject(403) : Category.find($route.current.params.id);
                    }]
                },
                name: 'pages-category'
            })
            .when('/pages/categories/add/:parentId', {
                templateUrl:  Config.templatesDir + 'PageCategory/edit.html',
                controller: 'PageCategoryController',
                controllerAs: 'VM',
                reloadOnSearch : false,
                resolve: {
                    init : ["AuthService", '$q', '$route', 'PageCategoryService', function (ACL, $q, $route, PageCategoryService) {
                        return (!ACL.inGates('cms.categories.menu') ? $q.reject(403) : PageCategoryService.addCategory($route.current.params.parentId));
                    }]
                },
                name: 'pages-new-category'
            });
    }
})();

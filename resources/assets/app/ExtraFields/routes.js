(function() {
    'use strict';

    angular.module('mcms.pages.extraFields')
        .config(config);

    config.$inject = ['$routeProvider','PAGES_CONFIG'];

    function config($routeProvider,Config) {

        $routeProvider
            .when('/pages/extraFields', {
                templateUrl:  Config.templatesDir + 'ExtraFields/index.html',
                controller: 'ExtraFieldHomeController',
                controllerAs: 'VM',
                reloadOnSearch : true,
                resolve: {
                    init : ["AuthService", '$q', function (ACL, $q) {
                        return (!ACL.inGates('cms.extraFields.menu')) ? $q.reject(403) : $q.resolve();
                    }]
                },
                name: 'pages-extra-fields-home'
            });
    }

})();

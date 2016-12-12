(function() {
    'use strict';

    angular.module('mcms.pages.pageCategory')
        .controller('PageCategoryController',Controller);

    Controller.$inject = ['init', 'LangService', 'core.services'];

    function Controller(Category, Lang, Helpers) {
        var vm = this;
        vm.Category = Category;
        vm.defaultLang = Lang.defaultLang();

        vm.onSave = function(item, isNew, parent){
            if (isNew) {
                return Helpers.redirectTo('pages-category', {id : item.id});
            }
        }
    }

})();

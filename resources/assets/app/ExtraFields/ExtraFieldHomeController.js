(function() {
    'use strict';

    angular.module('mcms.pages.extraFields')
        .controller('ExtraFieldHomeController',Controller);

    Controller.$inject = ['PAGES_CONFIG', 'LayoutManagerService'];

    function Controller(Config, LMS) {
        var vm = this;
        var layouts = [],
            allLayouts = LMS.layouts('pages.items');
        for (var i in allLayouts){
            layouts.push({
                label : allLayouts[i].label,
                value : allLayouts[i].varName,
            });
        }

        vm.Model = Config.pageModel;
        vm.additionalFields = [
            {
                varName : 'layoutId',
                label : 'Layout',
                type : 'selectMultiple',
                options : layouts
            }
        ];
    }
})();

(function() {
    'use strict';

    angular.module('mcms.pages.page')
        .controller('PageController',Controller);

    Controller.$inject = ['item', 'LangService', '$location', '$filter', '$scope', '$rootScope', 'PageService'];

    function Controller(Item, Lang, $location, $filter, $scope, $rootScope, PageService) {
        var vm = this,
            previewOn = false;

        vm.Item = Item;
        vm.defaultLang = Lang.defaultLang();
        vm.previewAvailable = true;



        vm.onSave = function (item, isNew) {
            if (isNew){
                $location.path($filter('reverseUrl')('pages-edit',{id : item.id}).replace('#',''));
            }
        };

        vm.preview = function () {
            if (typeof vm.Item.id == 'undefined'){
                return;
            }

            if (previewOn) {
                togglePreview();
                previewOn = false;
                return;
            }

            PageService.previewUrl(vm.Item.id)
                .then(function (response) {
                    vm.previewSrc = response.url;
                    togglePreview();
                    previewOn = true;
                });
        };

        vm.openInNewTab = function () {
            PageService.previewUrl(vm.Item.id)
                .then(function (response) {
                    var win = window.open(response.url, '_blank');
                    if (win) {
                        //Browser has allowed it to be opened
                        win.focus();
                    } else {
                        //Browser has blocked it
                        alert('Please allow popups for this website');
                    }

                });
        };

        function togglePreview() {
            $scope.preview = !$scope.preview;
            $scope.layout = ($scope.preview) ? 'row' : 'column';
            $rootScope.$broadcast('sideNav.unlock', !$scope.preview);
            $rootScope.$broadcast('page.preview', $scope.preview);
        }

    }

})();

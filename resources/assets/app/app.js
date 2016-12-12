(function () {
    'use strict';

    angular.module('mcms.pages', [
        'mcms.mediaFiles',
        'mcms.fileGallery',
        'mcms.extraFields',
        'mcms.pages.page',
        'mcms.pages.pageCategory',
        'mcms.pages.extraFields',
        'ngFileUpload'
    ])

        .run(run);

    run.$inject = ['mcms.menuService'];

    function run(Menu) {

        Menu.addMenu(Menu.newItem({
            id: 'pages',
            title: 'CMS',
            permalink: '',
            icon: 'pages',
            order: 1,
            acl: {
                type: 'level',
                permission: 2
            }
        }));

        var pagesMenu = Menu.find('pages');

        pagesMenu.addChildren([
            Menu.newItem({
                id: 'pages-manager',
                title: 'Pages',
                permalink: '/pages/content',
                icon: 'content_copy',
                order : 2
            }),
            Menu.newItem({
                id: 'pages-extra-fields',
                title: 'Extra Fields',
                permalink: '/pages/extraFields',
                gate : 'cms.extraFields.menu',
                icon: 'note_add',
                order : 3
            })
        ]);

        pagesMenu.addChildren([
            Menu.newItem({
                id: 'pagesCategories-manager',
                title: 'Categories',
                permalink: '/pages/categories',
                gate : 'cms.categories.menu',
                icon: 'view_list',
                order : 1
            })
        ]);
    }

})();

require('./config');
require('./Page');
require('./PageCategory');
require('./ExtraFields');

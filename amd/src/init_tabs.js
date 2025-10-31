/**
 * Initialise tab switching for Tabset activity (Bootstrap 5 version)
 *
 * @module mod_tabset/init_tabs
 * @copyright 2025 Dumfries and Galloway College
 */
import $ from 'jquery';
import {Tab} from 'bootstrap';

/**
 * Sets up Bootstrap tab activation.
 */
export const init = () => {
    // Ensure each tab behaves properly with Bootstrap 5.
    $('.tabset-activity').each((_, container) => {
        const $container = $(container);

        // Initialise tab switching.
        $container.find('a[data-bs-toggle="tab"], a[data-toggle="tab"]').each((_, el) => {
            const tab = new Tab(el);
            $(el).on('click', e => {
                e.preventDefault();
                tab.show();
            });
        });
    });
};

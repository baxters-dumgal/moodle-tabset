import $ from 'jquery';

/**
 * Initialise the form behaviour for the Tabset activity.
 * Shows/hides tab content editors based on selected template.
 *
 * @param {Object} tabtitles Mapping of template IDs to array of tab titles.
 */
export const init = (tabtitles) => {
    const $select = $('#id_templateid');

    const updateEditors = () => {
        const tid = $select.val();
        const titles = tabtitles[tid] || [];

        $('[id^=fitem_id_tabcontent]').hide();

        titles.forEach((title, i) => {
            const $field = $(`#fitem_id_tabcontent_${i}`);
            $field.show();
            $field.find('label').text(title);
        });
    };

    $select.on('change', updateEditors);
    updateEditors();
};

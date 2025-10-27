define(['jquery'], function($) {
    return {
        /**
         * Initialise the form behaviour for the Tabset activity.
         *
         * Shows/hides tab content editors based on selected template.
         *
         * @param {Object} tabtitles Mapping of template IDs to array of tab titles.
         */
        init: function(tabtitles) {
            var $select = $('#id_templateid');

            /**
             * Show/hide editor fields depending on the chosen template.
             *
             * Updates labels to match the tab titles.
             */
            function updateEditors() {
                var tid = $select.val();
                var titles = tabtitles[tid] || [];

                // Hide all editors first.
                $('[id^=fitem_id_tabcontent]').hide();

                // Show only as many as there are titles.
                for (var i = 0; i < titles.length; i++) {
                    var $field = $('#fitem_id_tabcontent_' + i);
                    $field.show();
                    $field.find('label').text(titles[i]);
                }
            }

            $select.on('change', updateEditors);
            updateEditors(); // run on page load
        }
    };
});

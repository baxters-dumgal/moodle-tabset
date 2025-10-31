<?php
namespace mod_tabset\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {

    public function render_tabset($tabset) {
        global $PAGE;

        // Only enqueue JS when rendering in a proper page context.
        if (!empty($PAGE) && $PAGE instanceof \moodle_page) {
            $PAGE->requires->js_call_amd('mod_tabset/init_tabs', 'init');
        }

        $context  = \context_module::instance($tabset->cmid ?? $PAGE->cm->id);
        $titles   = $tabset->titles ?? [];
        $contents = $tabset->tabcontents ?? [];

        // ✅ Define colour palette (matches "metaskills" design)
        $colors = [
            'bg-primary text-white',   // Welcome
            'bg-success text-white',   // About
            'bg-teal text-white',      // Meta Skills
            'bg-indigo text-white',    // Outcomes
            'bg-warning text-dark',    // Delivery
            'bg-info text-white',      // Assessment
            'bg-darkgrey text-white'   // Contacts
        ];

        $count = min(count($titles), count($contents));
        $tabs = [];

        for ($i = 0; $i < $count; $i++) {

            // ✅ Step 1: Get raw content.
            $rawcontent = $contents[$i] ?? '';

            // ✅ Step 2: Rewrite @@PLUGINFILE@@ URLs to real /pluginfile.php links.
            $rewritten = file_rewrite_pluginfile_urls(
                $rawcontent,
                'pluginfile.php',
                $context->id,
                'mod_tabset',
                'tabcontent',
                $i      // ✅ use the tab index as the itemid
            );

            // ✅ Step 3: Format rewritten HTML safely for output.
            $formatted = format_text(
                $rewritten,
                FORMAT_HTML,
                [
                    'context'  => $context,
                    'noclean'  => true,   // allow embedded HTML (images, iframes, etc.)
                    'trusted'  => true    // respect trusttext
                ]
            );

            $tabs[] = [
                'index'      => $i,
                'title'      => format_string($titles[$i]),
                'content'    => $formatted,
                'active'     => ($i === 0),
                'uniq'       => 'ts' . ($tabset->cmid ?? uniqid()),
                'colorclass' => $colors[$i] ?? 'bg-secondary text-white'
            ];
        }

        $data = [
            'uniq' => uniqid('ts'),
            'tabs' => $tabs
        ];

        return $this->render_from_template('mod_tabset/tabset', $data);
    }
}

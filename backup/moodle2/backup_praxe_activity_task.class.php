<?php
require_once($CFG->dirroot . '/mod/praxe/backup/moodle2/backup_praxe_stepslib.php'); // Because it exists (must)
//require_once($CFG->dirroot . '/mod/praxe/backup/moodle2/backup_praxe_settingslib.php'); // Because it exists (optional)

/**
 * praxe backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_praxe_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
       $this->add_step(new backup_praxe_activity_structure_step('praxe_structure', 'praxe.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of praxes
        $search="/(".$base."\/mod\/praxe\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@PRAXEINDEX*$2@$', $content);

        // Link to praxe view by moduleid
        $search="/(".$base."\/mod\/praxe\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@PRAXEVIEWBYID*$2@$', $content);
        return $content;
    }
}
<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2012 onwards Tomas Jerabek
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_praxe_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {
        global $DB;

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        /// main elements ///
        $praxe = new backup_nested_element('praxe', array('id'),
                        array('name','description','studyfield','isced','year',
                      'term','datestart','dateend','timecreated','timemodified'));

        $studyfields = new backup_nested_element('studyfields');
        $studyfield = new backup_nested_element('studyfield', array('id'),
                        array('name','shortcut'));

        $schools = new backup_nested_element('schools');
        $school = new backup_nested_element('school', array('id'),
                        array('name','type','street','city','zip','email','phone',
                        'website','headmaster','usermodified','timecreated', 'timemodified'));
        $schoolteachers = new backup_nested_element('school_teachers');
        $schoolteacher = new backup_nested_element('school_teacher', array('id'),
                        array('ext_teacher'));
        $locations = new backup_nested_element('locations');
        $location = new backup_nested_element('location', array('id'),
                        array('school','studyfield','isced','subject','active','year','term','timecreated','timemodified'));

        $records = new backup_nested_element('records');
        $record = new backup_nested_element('record', array('id'),
                        array('student','status','location','timecreated', 'timemodified'));
        $schedules = new backup_nested_element('schedules');
        $schedule = new backup_nested_element('schedule', array('id'),
                        array('name','timestart','timeend','lesnumber','yearclass','schoolroom',
                        'lessubject','lestheme','timecreated', 'timemodified','deleted'));
        $inspections = new backup_nested_element('schedule_inspections');
        $inspection = new backup_nested_element('schedule_inspection', array('id'),
                        array('inspector','timecreated'));
        $notices = new backup_nested_element('schedule_notices');
        $notice = new backup_nested_element('schedule_notice', array('id'),
                        array('notice','user','timecreated'));

        // Build the tree
        $praxe->add_child($studyfields);
            $studyfields->add_child($studyfield);
        $praxe->add_child($schools);
            $schools->add_child($school);
            $school->add_child($schoolteachers);
                $schoolteachers->add_child($schoolteacher);
                $schoolteacher->add_child($locations);
                    $locations->add_child($location);

        $praxe->add_child($records);
            $records->add_child($record);
            $record->add_child($schedules);
                $schedules->add_child($schedule);
                $schedule->add_child($inspections);
                    $inspections->add_child($inspection);
                $schedule->add_child($notices);
                    $notices->add_child($notice);

        // Define sources
        $praxe->set_source_table('praxe', array('id' => backup::VAR_ACTIVITYID));

        $studyfield->set_source_array((array)$DB->get_records('praxe_studyfields'));

        $school->set_source_array((array)$DB->get_records('praxe_schools'));
        $schoolteacher->set_source_table('praxe_school_teachers', array('teacher_school' => backup::VAR_PARENTID));
        $location->set_source_table('praxe_locations', array('teacher' => backup::VAR_PARENTID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $record->set_source_table('praxe_records', array('praxe' => backup::VAR_PARENTID));
            $schedule->set_source_table('praxe_schedules', array('record' => backup::VAR_PARENTID));
            $inspection->set_source_table('praxe_schedule_inspections', array('schedule' => backup::VAR_PARENTID));
            $notice->set_source_table('praxe_schedule_notices', array('schedule' => backup::VAR_PARENTID));
        }

        // Define id annotations
        $school->annotate_ids('user','headmaster');
        $school->annotate_ids('user','usermodified');
        $schoolteacher->annotate_ids('user','ext_teacher');

        if ($userinfo) {
            $record->annotate_ids('user', 'student');
            $inspection->annotate_ids('user','inspector');
            $notice->annotate_ids('user','user');
        }

        // Define file annotations
        $praxe->annotate_files('mod_praxe', 'schedule', 'id');

        // Return the root element (praxe), wrapped into standard activity structure
        return $this->prepare_activity_structure($praxe);
    }
}
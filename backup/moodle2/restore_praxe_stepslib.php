<?php
/// according to /mod/praxe/locallib.php defines ///
define('PRAXE_STATUS_ASSIGNED',0);
define('PRAXE_STATUS_REFUSED',1);
define('PRAXE_STATUS_CONFIRMED',2);
define('PRAXE_STATUS_SCHEDULE_DONE',3);
define('PRAXE_STATUS_FINISHED',4);
define('PRAXE_STATUS_EVALUATED',5);
define('PRAXE_STATUS_CLOSED',6);
/**
 * Structure step to restore one praxe activity
 */
class restore_praxe_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('praxe', '/activity/praxe');
        $paths[] = new restore_path_element('praxe_studyfield', '/activity/praxe/studyfields/studyfield');

        $paths[] = new restore_path_element('praxe_school', '/activity/praxe/schools/school');
        $paths[] = new restore_path_element('praxe_school_teacher', '/activity/praxe/schools/school/school_teachers/school_teacher');
        $paths[] = new restore_path_element('praxe_location', '/activity/praxe/schools/school/school_teachers/school_teacher/locations/location');


        if ($userinfo) {
            $paths[] = new restore_path_element('praxe_record', '/activity/praxe/records/record');
	        $paths[] = new restore_path_element('praxe_schedule', '/activity/praxe/records/record/schedules/schedule');
	        $paths[] = new restore_path_element('praxe_schedule_inspection', '/activity/praxe/records/record/schedules/schedule/schedule_inspections/schedule_inspection');
	        $paths[] = new restore_path_element('praxe_schedule_notice', '/activity/praxe/records/record/schedules/schedule/schedule_notices/schedule_notice');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_praxe($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        /*$data->datestart = $this->apply_date_offset($data->datestart);
        $data->dateend = $this->apply_date_offset($data->dateend);*/
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the praxe record
        $newitemid = $DB->insert_record('praxe', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_praxe_record($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->praxe = $this->get_new_parentid('praxe');
        $data->student = $this->get_mappingid('user', $data->student);
        $data->location = $this->get_mappingid('praxe_location', $data->location);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('praxe_records', $data);
        $this->set_mapping('praxe_record', $oldid, $newitemid);
    }

    protected function process_praxe_schedule($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->record = $this->get_new_parentid('praxe_record');
        /*$data->timestart = $this->apply_date_offset($data->timestart);
        $data->timeend = $this->apply_date_offset($data->timeend);*/
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('praxe_schedules', $data);
        $this->set_mapping('praxe_schedule', $oldid, $newitemid);
    }

    protected function process_praxe_schedule_inspection($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->schedule = $this->get_new_parentid('praxe_schedule');
        $data->inspector = $this->get_mappingid('user', $data->inspector);
        $data->timecreated = $this->apply_date_offset($data->timecreated);

        $DB->insert_record('praxe_schedule_inspections', $data);
        // No need to save this mapping as far as nothing depend on it
    }

    protected function process_praxe_schedule_notice($data) {
        global $DB;

        $data = (object)$data;

        $data->schedule = $this->get_new_parentid('praxe_schedule');
        $data->user = $this->get_mappingid('user', $data->user);
        $data->timecreated = $this->apply_date_offset($data->timecreated);

        $DB->insert_record('praxe_schedule_notices', $data);
        // No need to save this mapping as far as nothing depend on it
    }

    protected function process_praxe_studyfield($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $existitem = $DB->get_record('praxe_studyfields',array('shortcut'=>$data->shortcut));
        if($existitem) {
            $newitemid = $existitem->id;
        }else {
            $newitemid = $DB->insert_record('praxe_studyfields', $data);
        }
        $this->set_mapping('praxe_studyfield', $oldid, $newitemid);
    }

    protected function process_praxe_school($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->headmaster = $this->get_mappingid('user', $data->headmaster);
        $data->usermodified = $this->get_mappingid('user', $data->usermodified);

        $existitem = $DB->get_record('praxe_schools',array('name' => $data->name, 'type' => $data->type, 'id' => $oldid));
        if($existitem) {
            $newitemid = $oldid;
        }else {
	        $newitemid = $DB->insert_record('praxe_schools', $data);
        }
        $this->set_mapping('praxe_school', $oldid, $newitemid);
    }

    protected function process_praxe_school_teacher($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->teacher_school = $this->get_new_parentid('praxe_school');
        $data->ext_teacher = $this->get_mappingid('user', $data->ext_teacher);

        $existitem = $DB->get_record('praxe_school_teachers',array('teacher_school' => $data->teacher_school, 'id' => $oldid, 'ext_teacher' => $data->ext_teacher));
        if($existitem) {
            $newitemid = $oldid;
        }else {
            $newitemid = $DB->insert_record('praxe_school_teachers', $data);
        }
        $this->set_mapping('praxe_school_teacher', $oldid, $newitemid);
    }

    protected function process_praxe_location($data) {
        global $DB;

        $userinfo = $this->get_setting_value('userinfo');

        $data = (object)$data;
        $oldid = $data->id;

        $data->teacher = $this->get_new_parentid('praxe_school_teacher');
        $data->school = $this->get_mappingid('praxe_school',$data->school);
        $data->studyfield = $this->get_mappingid('praxe_studyfield', $data->studyfield);

        $exItem = $DB->get_record('praxe_locations',array('school' => $data->school, 'teacher' => $data->teacher,
        													'studyfield' => $data->studyfield, 'isced' => $data->isced,
                                                            'subject' => $data->subject, 'year' => $data->year, 'term' => $data->term));
        if($exItem) {
            /// location is used in other praxe_record ///
            if($userinfo && ($used = $DB->record_exists_select('praxe_records', 'location = '.$exItem->id.' AND status <> '.PRAXE_STATUS_REFUSED))) {
                $data->active = ($used) ? 0 : 1;
                $newitemid = $DB->insert_record('praxe_locations', $data);
            }else {
                $newitemid = $exItem->id;
            }
        }else {
            $newitemid = $DB->insert_record('praxe_locations', $data);
        }
        $this->set_mapping('praxe_location', $oldid, $newitemid);
    }

    protected function after_execute() {
        // Add praxe related files, no need to match by itemname (just internally handled context)
    }
}
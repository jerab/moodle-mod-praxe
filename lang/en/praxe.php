<?php
/**
 * Defines the version of newmodule
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package    mod
 * @subpackage praxe
 * @copyright  2012 Tomas Jerabek <t.jerab@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['levelofeducation-1st'] = 'Primary education';
$string['levelofeducation-2nd'] = 'Lower secondary or second stage of basic education';
$string['levelofeducation-3rd'] = '(Upper) secondary education';
$string['active_records'] = 'Active records';
$string['active'] = 'Active';
$string['action_canceled'] = 'Action has been canceled.';
$string['actual'] = 'Actual';
$string['actual_practices'] = 'Actual practices';
$string['actual_status'] = 'Actual status';
$string['addlocation'] = 'Add new location';
$string['address'] = 'Address';
$string['addschool'] = 'Add new school';
$string['addtoschedule'] = 'Add item to schedule';
$string['already_used'] = 'Already used';
$string['assignstudtolocation_text'] = 'Assign student to location (student and external teacher will be informed by e-mail).';
$string['assignteachers'] = 'Assign teachers to school';
$string['assigntolocation_mail'] = 'Dear Sir or Madam,'.
									"\n".'our pleasure to inform you that the student {$a->name} has choosen your subject {$a->subject} at your school {$a->school} to conduct his/her practice from the course of study {$a->studyfield}.'.
									"\n".'This practice should take place between {$a->date}.'.
									"\n".'Please confirm your acceptance of the student&apos;s practice in Moodle (see link below). If for some reason you can not accept the student for this practice, please do so using the same link below.'.
									"\n\n".'Thank you very much.'.
									"\n\n".'Sincerely,'.
									"\n".'Organizers of educational practice';

$string['assigntolocation_mail_student'] = 'Dear {$a->name},'.
		"\n".'our pleasure to inform you have been assigned to the school {$a->school} to conduct your practice from the course of study {$a->studyfield}.'.
		"\n".'This practice should take place between {$a->date}.'.
		"\n".'Please, wait for confirmation of the acceptance of your practice.'.
		"\n\n".'Sincerely,'.
		"\n".'Organizers of educational practice';

$string['assigntolocation_text_forstudents'] = 'Choose one of the available locations shown below.';
$string['assigned_to_inspection'] = 'Assigned to an inspection';
$string['assigned_to_location'] = 'Assigned to location';
$string['assignusertolocation'] = 'Assign student to location';
$string['available_location'] = 'Available_location';
$string['changelocation'] = 'Change location';
$string['choose_lesson_number_info'] = 'Choose the number of lesson! It&apos;s important for the correct schedule.';
$string['choosing_location'] = 'Choose location';
$string['creating_schedule'] = 'Create schedule';
$string['city'] = 'City';
$string['configisceddescription'] = 'International Standard Classification of Education (ISCED)';
$string['confirmedlocation'] = 'Your practice confirmed';
$string['confirmlocation_mail'] = 'Dear student,'.
									"\n".'you have been enroled for the practice from {$a->studyfield} at the school {$a->school}. Your mentor should be {$a->name}.'.
									"\n".'You should now fill in the lesson schedule form in the activity {$a->praxename}.'.
									"\n\n".'Yours sincerely'.
									"\n".'Moodle';
$string['confirmschedule_mail'] = 'Dear user,'.
									"\n".'The student {$a->name} has changed some parts of his schedule of the practice at the school {$a->school}.'.
									"\n\n".'Yours sincerely'.
									"\n".'Moodle';
$string['confirmschedule_mailsubject'] = 'Changes in the schedule';
$string['confirmschedule_sendnotice'] = 'Confirm that your shedule is actual';
$string['confirmorrefusestudent'] = 'Confirm or refuse student&apos;s practice';
$string['contact'] = 'Contact';
$string['contactselectedschool'] = 'Please contact your selected school about your practice';
$string['create_new_by_copy'] = 'Create new by copying';
$string['dateofpraxe'] = 'Date of praxe';
$string['detail'] = 'Detail';
$string['done'] = 'Done';
$string['editschool'] = 'Edit school';
$string['email'] = 'E-mail';
$string['error_timeschedule'] = 'The begining of lesson must be before it&apos;s end.<br>You can add the lesson schedule 4 before it&apos;s begining at least.';
$string['error_schoolroom'] = 'Enter the number of classroom.';
$string['evaluated'] = 'Evaluated';
$string['extteacher'] = 'External teacher';
$string['filter'] = 'Filter';
$string['gotoinspection'] = 'Go to inspection';
$string['headmaster'] = 'Headmaster';
$string['informparticipants'] = 'Send message to participants';
$string['inprocess'] = 'In process';
$string['inspection'] = 'Teacher visit';
$string['iscedlevel'] = 'ISCED level';
$string['lesson'] = 'lesson';
$string['lessondetail'] = 'Lesson details';
$string['lesson_end'] = 'End of lesson';
$string['lesson_number'] = 'Number of lesson';
$string['lesson_start'] = 'Begining of lesson';
$string['lesson_theme'] = 'Theme of lesson';
$string['location_added'] = 'New location has been created';
$string['location_no_available'] = 'This location is not available';
$string['location_updated'] = 'The location has been updated';
$string['locationisrequired'] = 'Location is required';
$string['location'] = 'Location';
$string['location_is_not_available'] = 'This location is already used by some student.';
$string['locations'] = 'Locations';
$string['mailnotsenttoexternalteacher'] = 'E-mail has not been sent to external teacher!';
$string['mailnotsenttostudent'] = 'E-mail has not been sent to student!';
$string['modulename'] = 'Praxe';
$string['modulenameplural'] = 'Praxes';
$string['my_locations'] = 'My locations';
$string['my_praxe'] = 'My practice';
$string['my_praxes'] = 'My practices';
$string['my_praxe_info'] = 'My practice details';
$string['my_schedule'] = 'My schedule';
$string['my_schools'] = 'My schools';
$string['my_selected_location'] = 'My practice school';
$string['no_existing_isced'] = 'Unknown ISCED level';
$string['no_existing_term'] = 'Unknown term';
$string['no_praxe_records'] = 'No practice records';
$string['no_schedule_items'] = 'Shedule has not been set yet';
$string['no_teachers_available'] = 'No external teachers available.';
$string['no_teachers_for_this_school'] = 'No teachers available for this school.';
$string['nolocationsavailable'] = 'No locations available';
$string['noschoolsavailable'] = 'No schools available';
$string['noselection'] = 'Without selection';
$string['nostudentsavailable'] = 'No available students in course to be assigned to this location.';
$string['notallowedaction'] = 'Not allowed action';
$string['numberofrecords'] = 'Number of records';
$string['only_actual'] = 'Actual';
$string['phone'] = 'Phone';
$string['please_confirm_record'] = 'Please, confirm the student&apos;s request to practise at your location.';
$string['pluginadministration'] = 'praxe administration';
$string['pluginname'] = 'praxe';
$string['praxe'] = 'Praxe';
$string['praxe_completed'] = 'Practice completed';
$string['praxe_end'] = 'Practice end';
$string['praxe_start'] = 'Practice start';
$string['praxe:addnoticetostudentschedule'] = 'Add notice to the student&apos;s schedule';
$string['praxe:addschool'] = 'Add new school';
$string['praxe:addstudentschedule'] = 'Add student&apos;s schedule';
$string['praxe:assignselftoinspection'] = 'Assign self to schedule as an inspector';
$string['praxe:assignstudenttolocation'] = 'Assign user(student) to available location';
$string['praxe:assignteachertolocation'] = 'Assign external teacher to location';
$string['praxe:assignteachertoanyschool'] = 'Assign teacher user to any school';
$string['praxe:assignteachertoownschool'] = 'Assing teacher user to own school';
$string['praxe:beexternalteacher'] = 'Allow user to be assigned to the school as an external teacher';
$string['praxe:beheadmaster'] = 'Allow user to be assigned to the school as a headmaster';
$string['praxe:confirmlocation'] = 'Confirm student assigning to the location';
$string['praxe:confirmownlocation'] = 'Confirm student assigning to the user(external teacher) location';
$string['praxe:createownlocation'] = 'Create own location';
$string['praxe:deleteschool'] = 'Delete a school';
$string['praxe:editanylocation'] = 'Edit any location';
$string['praxe:editanyrecord'] = 'Edit any practice record';
$string['praxe:editanyschool'] = 'Edit any school';
$string['praxe:editownlocation'] = 'Edit user(external teacher) location';
$string['praxe:editownrecord'] = 'Edit user(student) practice record';
$string['praxe:editownschool'] = 'Edit user(headmaster) school';
$string['praxe:editstudentschedule'] = 'Edit student&apos;s schedule';
$string['praxe:manageallincourse'] = 'Access to all available actions in this module';
$string['praxe:viewanyrecorddetail'] = 'View practice record details';
$string['praxe:viewownschools'] = 'View schools assigned to user';
$string['praxe:viewrecordstoanylocation'] = 'View all practice records assigned to any location';
$string['praxe:viewrecordstoownlocation'] = 'View practice records assigned to own location';
$string['praxefieldset'] = 'Specific praxe settings';
$string['praxeintro'] = 'Intro praxe';
$string['praxename'] = 'Praxe';
$string['praxename_help'] = 'Praxe help';
$string['realy_delete_schedule'] = 'You are going to delete the item of schedule below';
$string['records_list'] = 'List of practices';
$string['refuse'] = 'Refuse';
$string['removeinspection'] = 'Revoke user from inspection';
$string['removed_from_inspection'] = 'User has been revoked from inspection';
$string['schedule'] = 'Schedule';
$string['schedule_item_added'] = 'New date has been added to the schedule';
$string['schedule_updated'] = 'Schedule has been updated';
$string['schedule-lessontime'] = 'Begining and end of lesson';
$string['schedule-lessontime_help'] = 'The begining of lesson must be before it&apos;s end.<br>You can add the lesson schedule 4 before it&apos;s begining at least.';
$string['school'] = 'School';
$string['school_added'] = 'School has been added';
$string['school_detail'] = 'School detail';
$string['school_updated'] = 'School data has been updated';
$string['schoolname'] = 'School name';
$string['schoolroom'] = 'Classroom';
$string['schools'] = 'Schools';
$string['schooltype'] = 'School type';
$string['select_student'] = '- select student -';
$string['sendinfotoextteacher'] = 'Inform External teacher by e-mail';
$string['sendinfotostudent'] = 'Inform student by e-mail';
$string['status'] = 'Status';
$string['status_assigned_text'] = 'Waiting for the external teacher confirmation.';
$string['status_closed_text'] = 'This practice has been closed.';
$string['status_confirmed_text'] = 'The selection has been confirmed.';
$string['status_schedule_done_text'] = 'The schedule has been created.';
$string['status_evaluated_text'] = 'The practice has been evaluated.';
$string['status_finished_text'] = 'The practice is completed.';
$string['status_refused_text'] = 'The selection has been refused.';
$string['status_refused_text_for_student'] = 'Your last selection has been refused.';
$string['street'] = 'Street';
$string['strftimedayshort'] = '%D';
$string['strftimeday'] = '%A';
$string['student'] = 'Student';
$string['studenttopraxe'] = 'Student to practice';
$string['studyfield'] = 'Study field';
$string['subject'] = 'Subject';
$string['summerterm'] = 'Summer term';
$string['teachers'] = 'Teachers';
$string['teacher'] = 'Teacher';
$string['term'] = 'Term';
$string['to_create_location_choose_school'] = 'To create new location select the school.';
$string['todo'] = 'To do';
$string['typeschool1'] = 'Primary';
$string['typeschool2'] = 'Primary/Secondary - 8 years';
$string['typeschool3'] = 'Secondary';
$string['typeschool4'] = 'Secondary - technologies';
$string['typeschool5'] = 'Secondary - business and finance';
$string['typeschool_other'] = 'Other';
$string['unlisted'] = 'Unlisted';
$string['user_assigned_to_school'] = 'User has been assigned to the school';
$string['website'] = 'Web site';
$string['winterterm'] = 'Winter term';
$string['year'] = 'Year';
$string['yearclass'] = 'Year class';
$string['you_confirmed_location'] = 'You has confirmed the student&apos;s request.';
$string['you_refused_location'] = 'You has refused the student&apos;s request.';
$string['you_should_create_schedule'] = 'Now you should create your schedule';
$string['zipcode'] = 'Zip code';
?>
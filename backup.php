

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
 * course_custom_fields block caps.
 *
 * @package    block_course_custom_fields
 * @copyright  Norbert Czirjak (czirjak.norbert@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_course_custom_menu_backup extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_course_custom_menu');
        //$this->page->require->js('lib.js');
        //$this->page->requires->js('lib.js');
    }
   
    public function applicable_formats() {
        
        return array(
                    'all'             => false,
                    'site'            => false,
                    'course'          => true,
                    'course-category' => true,
                    'mod'             => false,
                    'my'              => false,
                    'tag'             => false,
                    'admin'           => false,            
            );
    }

    public function getCourseSequences($courseid) {
        global $DB;
        /* get the actual course and section sequence numbers */
        $sequence = $DB->get_records_sql('SELECT id, course, sequence
                                     FROM {course_sections} 
                                     WHERE 
                                     visible = 1 and course = :courseid ' , array('courseid' => $courseid));
        
        
        // sections starting from 0, this is why we remove one element
        return $sequence;
    }
    
    public function countCourseSequences($courseid)
    {
        
        global $DB;
        
        /* get the actual course and section sequence numbers */
        $sequence = $DB->get_field_sql('SELECT count(sequence)
                                     FROM {course_sections} 
                                     WHERE 
                                     visible = 1 and course = :courseid ' , array('courseid' => $courseid));
        
        
        // sections starting from 0, this is why we remove one element
        return $sequence -1;
        
    }
    
    
    public function getSectionName($courseid, $sectionid){
        
        global $DB;
        
        /* get the actual course and section sequence numbers */
        $name = $DB->get_field_sql('SELECT name
                                     FROM {course_sections} 
                                     WHERE 
                                     visible = 1 and course = :courseid and section = :sectionid' , array('courseid' => $courseid, 'sectionid' => $sectionid));
        
        return $name;
        
    }
    
    public function getCourseMenu($courseid) {
        global $DB, $CFG;
        $res = array();
        $res = $DB->get_records_sql(
                '
                SELECT 
                   row_number() OVER (ORDER BY cs.id) as n,
                    cm.id as module_id, cs.id as course_section_id, cs.name as course_section_name,
                    cs.section as course_section, cm.module, m.name as module_name, lp.prevpageid, lp.nextpageid, 
                    lp.title as lessonpage_title, l.name as lesson_name, cm.instance as instance, lp.id as lessonpage_id
                FROM
                    {course_sections} as cs
                LEFT JOIN 
                    {course_modules} as cm on cm.course = ? and cm.section = cs.id
                LEFT JOIN 
                    {modules} as m on m.id = cm.module
                LEFT JOIN 
                    {lesson} as l ON cm.instance = l.id and l.course = ?
                LEFT JOIN 
                    {lesson_pages} as lp ON lp.lessonid = l.id
                WHERE cs.course = ? and cm.visible = 1 and cs.visible = 1 
                ORDER BY 
                    module_id ASC
                ', array($courseid, $courseid, $courseid));
        
        return $res;
    }
    
    public function getCourseSectionSequence($courseid, $sectionid) {
        global $DB, $CFG;
        $result = array();
         $result = $DB->get_field_sql('SELECT sequence
                                     FROM {course_sections} 
                                     WHERE 
                                     visible = 1 and course = :courseid and section = :sectionid' , array('courseid' => $courseid, 'sectionid' => $sectionid));
        return $result;
    }
    
    public function getCourseSectionNames($courseid, $sectionid) {
        global $CFG, $OUTPUT, $DB, $PAGE, $LESSON, $USER;
        $sequenceArr = array();
        
        /* get the actual course and section sequence numbers */
        $sequence = $this->getCourseSectionSequence($courseid, $sectionid);

        /* create an array from the string */
        $seqArr = explode(',', $sequence);
        /*  get the formats of the modules -> F.e.: page, forum, quiz */
        foreach($seqArr as $s){
            $name[$s] = $this->getModuleName($s);
        }

        foreach($name as $key => $value) {
            $seqID = $key;
            $value = (array)$value;
            
            foreach($value as $v){
                $v = (array)$v;                
                $moduleName = $v["name"];
                $instanceID = $v["instance"]; 
                
                $str   = $this->getModuleInstanceName($instanceID, $name, $moduleName);
                //the child menupoint names
                $lessonToggle = "";
                $sequenceArr[$seqID] = "<div>";
                if(strtolower($moduleName) == 'lesson') {
                    $lessonToggle = '<a class="accordion-toggle custom_menu_selected_lesson_arrow" id="oeaw-cmlc-'.$seqID.'"> <i class="icon fa fa-caret-right"></i> </a>';
                    $lessons = $this->getLessonPages($courseid, $seqID);
                    
                    $sequenceArr[$seqID] .= $lessonToggle.' <img src="'.$CFG->wwwroot.'/theme/dh/pix/'.$moduleName.'.png" width="32px" height="32px">&nbsp;&nbsp;<a href="'.$CFG->wwwroot.'/mod/'.$moduleName.'/view.php?id='.$seqID.'" id="menu_course_section_value_'.$seqID.'" class="custom_menu_selected_lesson">'.$str.'</a>';
                    $sequenceArr[$seqID] .= '<div id="oeaw-cml-'.$seqID.'" class="panel-collapse collapse in oeaw-cm-lesson-list " >';
                    foreach($lessons as $l) {
                        $sequenceArr[$seqID] .=  "<li><a href='".$CFG->wwwroot."/mod/lesson/view.php?id=".$seqID."&pageid=".$l->page_id."' class='custom_menu_selected_lesson_page' id='ccml-".$seqID."-".$l->page_id."'>".$l->title."</a></li>";
                    }
                    $sequenceArr[$seqID] .= '</div>';
                    
                } else {
                    $sequenceArr[$seqID] .= $lessonToggle.' <img src="'.$CFG->wwwroot.'/theme/dh/pix/'.$moduleName.'.png" width="32px" height="32px">&nbsp;&nbsp;<a href="'.$CFG->wwwroot.'/mod/'.$moduleName.'/view.php?id='.$seqID.'" id="menu_course_section_value_'.$seqID.'" class="custom_menu_selected_lesson">'.$str.'</a>';
                }
                $sequenceArr[$seqID] .= "</div>";
            }
        }
        
        return $sequenceArr;                
    }
    
    public function getModuleName($moduleid) {
        global $DB, $CFG;
        $result = array();
        $result = $DB->get_records_sql('Select m.name, cm.instance FROM course_modules as cm LEFT JOIN modules as m ON m.id = cm.module WHERE cm.visible = 1 and cm.id = :moduleid', array('moduleid' => $moduleid));            
        return $result;
    }
    
    public function getModuleInstanceName($instanceID, $name, $moduleName) {
        global $DB, $CFG;
        
        $result = array();
        $result = $DB->get_field_sql('SELECT name
                                 FROM {'.$moduleName.'} 
                                 WHERE 
                                 id = :instanceid' , array('instanceid' => $instanceID));
        
        return $result;
    }
    
    
    public function getLessonPages($courseid, $cmid) {
        global $CFG, $DB;
        $result = $DB->get_records_sql(
            'SELECT  
                lp.title, lp.prevpageid, lp.nextpageid, l.id as lesson_id, lp.id as page_id
            FROM
                course_modules as cm
            LEFT JOIN 
                lesson as l ON cm.instance = l.id and l.course = ?
            LEFT JOIN 
                lesson_pages as lp ON lp.lessonid = l.id
            WHERE 
                cm.course = ? and cm.id = ?', array($courseid, $courseid, $cmid));
        return $result;
    }
    
    function has_config() {return true;}
    
    function get_content() {
        
        global $CFG, $OUTPUT, $DB, $PAGE;
        
        $PAGE->requires->js('/blocks/course_custom_menu/lib.js');
      
        //the course module id
        $cm_id = optional_param('id', 0,PARAM_INT);
        $pageid = optional_param('pageid', 0, PARAM_INT);
        $sectionid = optional_param('section', 0, PARAM_INT);
        
        
        //$this->page->require->requiresjs('/blocks/course_custom_menu/lib.js');
        $contextID = $this->page->context->id;
        $this->content = new stdClass();
        $this->content->text = false;
        $blockID = $this->instance->id;
        //if the contextid is not system type we need to change it - this needed because only system type will be available in all page
        if($contextID != 1){            
            
            if ($DB->record_exists('block_instances', array('id' => $blockID, 'blockname'=> 'course_custom_menu'))) {
                $updData = new stdClass();    
                $updData->id = $blockID;
                $updData->parentcontextid = 1;                         
                $DB->update_record('block_instances', $updData);                             
            } 
        }
        
        $id = $this->page->course->id;
        $menuData = array();
        $menuData = $this->getCourseMenu($id);
        
        $menu = array();
        
        foreach($menuData as $md) {
            $menu[$md->course_section_name][$md->module_id][] = array(
                "module_name" => $md->module_name, 
                "lessonpage_title" => $md->lessonpage_title,
                "lesson_name" => $md->lesson_name,
                "prevpageid" => $md->prevpageid,
                "nextpageid" => $md->nextpageid,
                "module_id" => $md->module_id,
                "lessonpage_id" => $md->lessonpage_id,
            );
            
            usort($menu[$md->course_section_name][$md->module_id], function ($item1, $item2) {                
                if ($item1['lessonpage_id'] == $item2['lessonpage_id'] ) return 0;
                return $item1['lessonpage_id'] < $item2['lessonpage_id'] ? -1 : 1;
            });
        }
       
        $courseSequence = $this->getCourseSequences($id);
        foreach($courseSequence as $cs){
            $cs->course;
            $cs->sequence;
        }

        $menuSections = $this->countCourseSequences($id);
        if(empty($menuSections)) {
            $this->content->text = 'Course has no data';
            return $this->content->text;
        }
        
        $course_modules = $DB->get_records('course_modules', array('course' => $id));
         
        //$course_modules = $course_modules[$cm_id];
        $lessons = array();
        $lessonPages = array();
        
        $this->content->text .= '<div class="panel-group" id="accordion2">';
        for($i = 1; $i <= $menuSections+1; $i++){

            $section = (string)$i;
            $menu_data = $this->getCourseSectionNames($id, $section);
            $sectioName = $this->getSectionName($id, $section);
            if(!$sectioName) { break; }
            
            //if the section is the same then we show the detail view
            if($section == $sectionid){ $active = "show"; }else { $active ="";}            
            
            $this->content->text .= '<div class="panel panel-default">';
                $this->content->text .= '<div class="panel-heading" data-toggle="collapse" data-parent="#accordion2" data-target="#oeaw-cmc-'.$id.'-'.$section.'">';
                    $this->content->text .= '<h4 class="panel-title">';
                        $this->content->text .= "<a class='accordion-toggle ccmc-section' id='ccmc-section-".$id."-".$section."'> <i class='icon fa fa-caret-right'></i> </a>";
                        $this->content->text .= "<a href='".$CFG->wwwroot."/course/view.php?id=".$id."&section=".$section."' class='ccm-section' id='ccm-section-".$id."-".$section."'> ".$sectioName."</a>";
                    $this->content->text .= '</h4>';    
                $this->content->text .= '</div>';    
                
                $this->content->text .= '<div id="oeaw-cmc-'.$id.'-'.$section.'" class="panel-collapse collapse '.$active.'">';
                
                    if(!empty($menu_data)){
                        foreach ($menu_data as $k => $data){                
                            $this->content->text .= '<div class="panel-body">';
                                $this->content->text .= '<div class="panel panel-default">';
                                    $this->content->text .= '<div class="panel-heading" data-toggle="collapse" data-parent="#accordion2" data-target="#oeaw-cml-'.$k.'">';
                                        $this->content->text .= '<div class="oeaw_custom_menu_content_row">'; 
                                            $this->content->text .= '<p> '.$data.'</p>';
                                        $this->content->text .= '</div>';
                                    $this->content->text .= '</div>';
                                $this->content->text .= '</div>';
                                //ide jon
                            $this->content->text .= '</div>';
                        }
                    }    
                    
                $this->content->text .= '</div>';
            $this->content->text .= '</div>';
        }        
        $this->content->text .= '</div>';
                          
        return $this->content;        
        
    }

    
}

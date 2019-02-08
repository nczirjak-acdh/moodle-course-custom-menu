

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

class block_course_custom_menu extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_course_custom_menu');
        
    }
   
    public function applicable_formats() {
        
        return array(
                    'all'             => false,
                    'site'            => false,
                    'course'          => true,
                    'course-category' => true,
                    'mod'             => true,
                    'my'              => false,
                    'tag'             => false,
                    'admin'           => false,            
            );
    }
    
    public function instance_allow_multiple() {
          return true;
    }

    public function getCourseSequences($courseid)
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
    
    
    
    public function getCourseSectionNames($courseid, $sectionid)
    {
        global $DB, $CFG;
        $sequenceArr = array();
        /* get the actual course and section sequence numbers */
        $sequence = $DB->get_field_sql('SELECT sequence
                                     FROM {course_sections} 
                                     WHERE 
                                     visible = 1 and course = :courseid and section = :sectionid' , array('courseid' => $courseid, 'sectionid' => $sectionid));

        /* create an array from the string */
        $seqArr = explode(',', $sequence);
        /*  get the formats of the modules -> F.e.: page, forum, quiz */
        foreach($seqArr as $s){
            $name[$s] = $DB->get_records_sql('Select m.name, cm.instance FROM course_modules as cm LEFT JOIN modules as m ON m.id = cm.module WHERE cm.visible = 1 and cm.id = :moduleid', array('moduleid' => $s));            
        }
        /*  */
        foreach($name as $key => $value)
        {
            $seqID = $key;
            $value = (array)$value;
            
            foreach($value as $v){
                $v = (array)$v;                
                $moduleName = $v["name"];
                $instanceID = $v["instance"];                
                
                $str   = $DB->get_field_sql('SELECT name
                                 FROM {'.$moduleName.'} 
                                 WHERE 
                                 id = :instanceid' , array('instanceid' => $instanceID));
                //the child menupoint names
                $sequenceArr[] = '<img src="'.$CFG->wwwroot.'/theme/dariahteach/pix/'.$moduleName.'.png" width="32px" height="32px">&nbsp;&nbsp;<a href="'.$CFG->wwwroot.'/mod/'.$moduleName.'/view.php?id='.$seqID.'" id="menu_course_section_value_'.$seqID.'" class="custom_menu_selected_lesson">'.$str.'</a>';
                //$sequenceArr[] = '<button type="button" value="'.$moduleName.'/'.$seqID.'" id="menu_course_section_value" class="menu_course_section_value" name="menu_course_section_value">'.$str.'</button>';
                //modulename - instanceid
            }
            
        }
        
        return $sequenceArr;                
    }
    
    public function instance_can_be_docked() {
        return parent::instance_can_be_docked() && isset($this->config->title) && !empty($this->config->title);
    }
    
    function has_config() {return true;}
    
    function get_content() {
        
        global $CFG, $OUTPUT, $DB, $PAGE;        
        
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
        $menuSections = $this->getCourseSequences($id);
        
        if((int)$menuSections < 2) {
            $this->content = '';
            return $this->content;
        }
        
        for($i = 1; $i <= $menuSections+1; $i++){

            $section = (string)$i;
            $menu_data = $this->getCourseSectionNames($id, $section);
            
            $sectioName = $this->getSectionName($id, $section);
            if(empty($sectioName) || $section == 0){ $class="hidden";} else {$class="";};
            if($section == 1){ $active = "active"; }else { $active ="";}
            //if($section == 0) {$class = "hidden";}
            $this->content->text .= '<div class="oeaw_custom_menu_root '.$class.' '.$active.' " id="oeaw_custom_menu_root_'.$id.'-'.$section.'">';
            $openid = 'oeaw_cmr_'.$id.'-'.$section;
            $this->content->text .= "<div class='oeaw_custom_menu_root_header ".$class."'><a id='oeaw_cmr_".$id."-".$section."' href='".$CFG->wwwroot."/course/view.php?id=".$id."&section=".$section."' >".$sectioName."</a></div>";
            $this->content->text .= '</div>';
            
            $this->content->text .= '<div class="oeaw_custom_menu_content active '.$class.'" id="oeaw_cmc_'.$id.'-'.$section.'">';
            
            if(!empty($menu_data)){
                foreach ($menu_data as $data){                
                    $this->content->text .= '<div  >';            
                    $this->content->text .= '<div class="oeaw_custom_menu_content_row"><p>'.$data.'</p></div>'; 
                    $this->content->text .= '</div>';
                }
            }            
       
            $this->content->text .= '</div>';
        }
                          
        return $this->content;        
        
    }

    
}

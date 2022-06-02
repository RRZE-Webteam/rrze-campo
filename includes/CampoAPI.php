<?php

namespace RRZE\Campo;

defined('ABSPATH') || exit;

if (!function_exists('__')) {
    function __($txt, $domain)
    {
        return $txt;
    }
}

class CampoAPI
{

    protected $api;
    // protected $orgID;
    protected $atts;
    protected $campoParam;
    protected $sem;
    protected $gast;

    // public function __construct($api, $orgID, $atts)
    public function __construct($atts)
    {
        $this->setAPI();
        // $this->orgID = $orgID;
        $this->atts = $atts;
        $this->sem = (!empty($this->atts['sem']) && self::checkSemester($this->atts['sem']) ? $this->atts['sem'] : '');
        $this->gast = (!empty($this->atts['gast']) ? __('Für Gasthörer zugelassen', 'rrze-campo') : '');
    }


    private function getKey(){
        $settingsOptions = get_option('rrze-settings');


        if (!empty($settingsOptions['campo_apiKey'])){
            return $settingsOptions['campo_apiKey'];
        }else{
            $campoOptions = get_option('rrze-campo');
            return $campoOptions['basic_ApiKey'];
        }
    }

    public function getResponse($sParam = NULL){
        $aRet = [
            'valid' => FALSE, 
            'content' => ''
        ];

        $aGetArgs = [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Api-Key' => $this->getKey(),
                ]
            ];

        $content = wp_remote_get($this->api . $sParam, $aGetArgs);
        $content = $content["body"];
        $content = json_decode($content, true);

        // if ($content['code'] != 200) {
        //     $aRet = [
        //         'valid' => FALSE, 
        //         'content' => $content['code']
        //     ];    
        // }else{
        //     $aRet = [
        //         'valid' => TRUE, 
        //         'content' => $content
        //     ];
        // }

        // echo '<pre>';
        // var_dump($content);
        // exit;


        if (empty($content['data'])) {
            $aRet = [
                'valid' => FALSE, 
                'content' => $content['code']
            ];    
        }else{
            $aRet = [
                'valid' => TRUE, 
                'content' => $content
            ];
        }


        return $aRet;
    }


    private function setAPI()
    {
        $this->api = 'https://api.fau.de/pub/v1/mschema/educationEvents';
    }

    private static function log(string $method, string $logType = 'error', string $msg = '')
    {
        // uses plugin rrze-log
        $pre = __NAMESPACE__ . ' ' . $method . '() : ';
        if ($logType == 'DB') {
            global $wpdb;
            do_action('rrze.log.error', $pre . '$wpdb->last_result= ' . json_encode($wpdb->last_result) . '| $wpdb->last_query= ' . json_encode($wpdb->last_query . '| $wpdb->last_error= ' . json_encode($wpdb->last_error)));
        } else {
            do_action('rrze.log.' . $logType, __NAMESPACE__ . ' ' . $method . '() : ' . $msg);
        }
    }

    public function getData($dataType, $campoParam = null)
    {
        $this->campoParam = urlencode($campoParam);
        $url = $this->getUrl($dataType) . $this->campoParam;

        if (!$url) {
            return 'Set Campo Org ID in settings.';
        }
        $data = file_get_contents($url);
        if (!$data) {
            CampoAPI::log('getData', 'error', "no data returned using $url");
            return false;
        }
        $data = json_decode($data, true);
        $data = $this->mapIt($dataType, $data);
        $data = $this->dict($data);
        $data = $this->sortGroup($dataType, $data);
        return $data;
    }

    private function getUrl($dataType)
    {
        $url = $this->api;
        switch ($dataType) {
            case 'personByID':
                $url .= 'persons&id=';
                break;
            case 'personByName':
                $url .= 'persons&fullname=';
                break;
            case 'personAll':
                if (empty($this->orgID)) {
                    return false;
                }
                $url .= 'departments&number=' . $this->orgID;
                break;
            case 'personByOrga':
            case 'personByOrgaPhonebook':
                if (empty($this->orgID)) {
                    return false;
                }
                $url .= 'persons&department=' . $this->orgID;
                break;
            case 'publicationByAuthorID':
                $url .= 'publications&authorid=';
                break;
            case 'publicationByAuthor':
                $url .= 'publications&author=';
                break;
            case 'publicationByDepartment':
                if (empty($this->orgID)) {
                    return false;
                }
                $url .= 'publications&department=' . $this->orgID;
                break;
            case 'lectureByID':
                // $url .= 'lectures'.(!empty($this->atts['lang'])?'&lang='.$this->atts['lang']:'').(isset($this->atts['lv_import']) && !$this->atts['lv_import']?'&noimports=1':'').(!empty($this->atts['type'])?'&type='.$this->atts['type']:'').(!empty($this->sem)?'&sem='.$this->sem:'').'&id=';
                $url .= 'lectures' . (isset($this->atts['lv_import']) && !$this->atts['lv_import'] ? '&noimports=1' : '') . (!empty($this->sem) ? '&sem=' . $this->sem : '') . '&id=';
                break;
            case 'lectureByDepartment':
                if (empty($this->orgID)) {
                    return false;
                }
                // $url .= 'lectures'.(!empty($this->atts['fruehstud'])?'&fruehstud='.($this->atts['fruehstud']?'ja':'nein'):'').(!empty($this->atts['lang'])?'&lang='.$this->atts['lang']:'').(isset($this->atts['lv_import']) && !$this->atts['lv_import']?'&noimports=1':'').(!empty($this->atts['type'])?'&type='.$this->atts['type']:'').(!empty($this->sem)?'&sem='.$this->sem:'').'&department='.$this->orgID;
                $url .= 'lectures' . (!empty($this->atts['fruehstud']) ? '&fruehstud=' . ($this->atts['fruehstud'] ? 'ja' : 'nein') : '') . (isset($this->atts['lv_import']) && !$this->atts['lv_import'] ? '&noimports=1' : '') . (!empty($this->sem) ? '&sem=' . $this->sem : '') . '&department=' . $this->orgID;
                break;
            case 'lectureByLecturer':
                // $url .= 'lectures'.(!empty($this->atts['lang'])?'&lang='.$this->atts['lang']:'').(isset($this->atts['lv_import']) && !$this->atts['lv_import']?'&noimports=1':'').(!empty($this->atts['type'])?'&type='.$this->atts['type']:'').(!empty($this->sem)?'&sem='.$this->sem:'').'&lecturer=';
                $url .= 'lectures' . (isset($this->atts['lv_import']) && !$this->atts['lv_import'] ? '&noimports=1' : '') . (!empty($this->sem) ? '&sem=' . $this->sem : '') . '&lecturer=';
                break;
            case 'lectureByLecturerID':
                // $url .= 'lectures'.(!empty($this->atts['lang'])?'&lang='.$this->atts['lang']:'').(isset($this->atts['lv_import']) && !$this->atts['lv_import']?'&noimports=1':'').(!empty($this->atts['type'])?'&type='.$this->atts['type']:'').(!empty($this->sem)?'&sem='.$this->sem:'').'&lecturerid=';
                $url .= 'lectures' . (isset($this->atts['lv_import']) && !$this->atts['lv_import'] ? '&noimports=1' : '') . (!empty($this->sem) ? '&sem=' . $this->sem : '') . '&lecturerid=';
                break;
            case 'lectureByName':
                $url .= 'lectures&name=';
                break;
            case 'jobByID':
                $url .= 'positions&closed=1&id=';
                break;
            case 'jobAll':
                if (empty($this->orgID)) {
                    return false;
                }
                $url .= 'positions&closed=1&department=' . $this->orgID;
                break;
            case 'roomByID':
                $url .= 'rooms&id=';
                break;
            case 'roomByName':
                $url .= 'rooms&name=';
                break;
            case 'departmentByName':
                $url .= 'departments&name=';
                break;
            case 'departmentAll':
                $url .= 'departments';
                break;
            default:
                CampoAPI::log('getUrl', 'error', 'unknown dataType ' . $dataType);
        }
        return $url;
    }

    public function getMap($dataType)
    {
        $map = [];

        switch ($dataType) {
            case 'personByID':
            case 'personByOrga':
            case 'personByOrgaPhonebook':
            case 'personByName':
            case 'personAll':
                $map = [
                    'node' => 'Person',
                    'fields' => [
                        'person_id' => 'id',
                        'key' => 'key',
                        'title' => 'title',
                        'atitle' => 'atitle',
                        'firstname' => 'firstname',
                        'lastname' => 'lastname',
                        'work' => 'work',
                        'officehours' => 'officehour',
                        'department' => 'orgname',
                        'organization' => ['orgunit', 1],
                        'locations' => 'location',
                    ],
                ];
                break;
            case 'publicationByAuthor':
            case 'publicationByAuthorID':
            case 'publicationByDepartment':
                $map = [
                    'node' => 'Pub',
                    'fields' => [
                        'publication_id' => 'id',
                        'journal' => 'journal',
                        'pubtitle' => 'pubtitle',
                        'year' => 'year',
                        'author' => 'author',
                        'publication_type' => 'type',
                        'hstype' => 'hstype',
                    ],
                ];
                break;
            case 'lectureByID':
            case 'lectureByDepartment':
            case 'lectureByLecturer':
            case 'lectureByLecturerID':
            case 'lectureByName':
                $map = [
                    'node' => 'Lecture',
                    'fields' => [
                        'lecture_id' => 'id',
                        'name' => 'name',
                        'ects_name' => 'ects_name',
                        'comment' => 'comment',
                        'leclanguage' => 'leclanguage',
                        'key' => 'key',
                        'courses' => 'term',
                        'course_keys' => 'course',
                        'lecture_type' => 'type',
                        'keywords' => 'keywords',
                        'maxturnout' => 'maxturnout',
                        'url_description' => 'url_description',
                        'organizational' => 'organizational',
                        'summary' => 'summary',
                        'schein' => 'schein',
                        'sws' => 'sws',
                        'ects' => 'ects',
                        'ects_cred' => 'ects_cred',
                        'beginners' => 'beginners',
                        'fruehstud' => 'fruehstud',
                        'gast' => 'scientia',
                        'evaluation' => 'evaluation',
                        'doz' => 'doz',
                    ],
                ];
                break;
            case 'courses':
                $map = [
                    'node' => 'Lecture',
                    'fields' => [
                        'term' => 'term',
                        'coursename' => 'coursename',
                        'course_key' => 'key',
                        'doz' => 'doz',
                    ],
                ];
                break;
            case 'jobByID':
            case 'jobAll':
                $map = [
                    'node' => 'Position',
                    'fields' => [
                        'job_id' => 'id',
                        'application_end' => 'enddate',
                        'application_link' => 'desc6',
                        'job_intern' => 'intern',
                        'job_title' => 'title',
                        'job_start' => 'start',
                        'job_limitation' => 'type1',
                        'job_limitation_duration' => 'befristet',
                        'job_limitation_reason' => 'type3',
                        'job_salary_from' => 'vonbesold',
                        'job_salary_to' => 'bisbesold',
                        'job_qualifications' => 'desc2',
                        'job_qualifications_nth' => 'desc3',
                        'job_employmenttype' => 'type2',
                        'job_workhours' => 'wstunden',
                        'job_category' => 'group',
                        'job_description' => 'desc1',
                        'job_description_introduction' => 'desc5',
                        'job_experience' => 'desc2',
                        'job_benefits' => 'desc4',
                        'person_key' => 'acontact',
                    ],
                ];
                break;
            case 'roomByID':
            case 'roomByName':
                $map = [
                    'node' => 'Room',
                    'fields' => [
                        'room_id' => 'id',
                        'key' => 'key',
                        'name' => 'name',
                        'short' => 'short',
                        'roomno' => 'roomno',
                        'buildno' => 'buildno',
                        'north' => 'north',
                        'east' => 'east',
                        'address' => 'address',
                        'size' => 'size',
                        'description' => 'description',
                        'blackboard' => 'tafel',
                        'flipchart' => 'flip',
                        'beamer' => 'beam',
                        'microphone' => 'mic',
                        'audio' => 'audio',
                        'overheadprojector' => 'ohead',
                        'tv' => 'tv',
                        'internet' => 'inet',
                    ],
                ];
                break;
            case 'orga':
                $map = [
                    'node' => 'Org',
                    'fields' => [
                        'orga_positions' => 'job',
                    ],
                ];
                break;
            case 'departmentByName':
            case 'departmentAll':
                $map = [
                    'node' => 'Org',
                    'fields' => [
                        'orgnr' => 'orgnr',
                        'name' => 'name',
                    ],
                ];
                break;
        }

        return $map;
    }


    public function mapIt($dataType, &$data)
    {
        $map = $this->getMap($dataType);

        if (empty($map)) {
            return $data;
        }

        $ret = [];
        $show = true;

        if (isset($data[$map['node']])) {
            foreach ($data[$map['node']] as $nr => $entry) {
                foreach ($map['fields'] as $k => $v) {
                    if (is_array($v)) {
                        if (is_int($v[1])) {
                            if (isset($data[$map['node']][$nr][$v[0]][$v[1]])) {
                                $ret[$nr][$k] = $data[$map['node']][$nr][$v[0]][$v[1]];
                            } elseif (isset($data[$map['node']][$nr][$v[0]][0])) {
                                $ret[$nr][$k] = $data[$map['node']][$nr][$v[0]][0];
                            }
                        } else {
                            $y = 0;
                            while (isset($data[$map['node']][$nr][$v[0]][$y][$v[1]])) {
                                $ret[$nr][$k] = $data[$map['node']][$nr][$v[0]][$y][$v[1]];
                                $y++;
                            }
                        }
                    } else {
                        if (isset($data[$map['node']][$nr][$v])) {
                            $ret[$nr][$k] = $data[$map['node']][$nr][$v];
                        }
                    }
                }
            }
        }

        switch ($dataType) {
            case 'lectureByLecturerID':
                // $lecturer_key is used in template to filter courses that are not by this lecturer
                $lecturer = $this->getData('personByID', $this->campoParam);
                if (isset($lecturer[0]['key'])) {
                    $subs = explode('Person.', $lecturer[0]['key']);
                }
                $lecturer_key = (isset($subs[1]) ? $subs[1] : '');
            case 'lectureByLecturer':
                // $lecturer_key is used in template to filter courses that are not by this lecturer
                $lecturer = $this->getData('personByName', $this->campoParam);
                if (isset($lecturer[0]['key'])) {
                    $subs = explode('Person.', $lecturer[0]['key']);
                }
                $lecturer_key = (isset($subs[1]) ? $subs[1] : '');
            case 'lectureByID':
            case 'lectureByDepartment':
                // add details
                $courses = $this->mapIt('courses', $data);
                $persons = $this->mapIt('personByID', $data);
                $delNr = [];
                foreach ($ret as $e_nr => $entry) {
                    $ret[$e_nr]['lecturer_key'] = (!empty($lecturer_key) ? $lecturer_key : '');
                    // add course details
                    if (isset($entry['course_keys'])) {
                        foreach ($entry['course_keys'] as $course_key) {
                            foreach ($courses as $c_nr => $course) {
                                if (($course['course_key'] == 'Lecture.' . $course_key) && (isset($course['term']))) {
                                    unset($course['course_key']);
                                    $ret[$e_nr]['courses'][] = $course;
                                    // delete entry of this course
                                    foreach ($ret as $nr => $val) {
                                        if ($val['key'] == 'Lecture.' . $course_key) {
                                            $delNr[] = $nr;
                                        }
                                    }
                                }
                            }
                        }
                        unset($ret[$e_nr]['course_keys']);
                    } elseif (isset($entry['courses'])) {
                        unset($ret[$e_nr]['courses']);
                        $ret[$e_nr]['courses'][] = ['term' => $entry['courses']];
                    }
                    // add person details
                    if (isset($entry['doz'])) {
                        foreach ($entry['doz'] as $doz_key) {
                            foreach ($persons as $p_nr => $person) {
                                if ($person['key'] == 'Person.' . $doz_key) {
                                    // unset($person['key']);
                                    $ret[$e_nr]['lecturers'][] = $person;
                                    unset($person[$p_nr]);
                                }
                            }
                        }
                        unset($ret[$e_nr]['doz']);
                    }
                }
                foreach ($delNr as $nr) {
                    unset($ret[$nr]);
                }
                // add room details
                $rooms = $this->mapIt('roomByID', $data);
                foreach ($ret as $nr => $entry) {
                    if (isset($entry['courses'])) {
                        foreach ($entry['courses'] as $c_nr => $course) {
                            foreach ($course['term'] as $t_nr => $term) {
                                foreach ($rooms as $room) {
                                    if (isset($term['room']) && $term['room'] == $room['key']) {
                                        $ret[$nr]['courses'][$c_nr]['term'][$t_nr]['room'] = $room;
                                    }
                                }
                            }
                        }
                    }
                }
                break;
        }

        return $ret;
    }

    public function sortGroup($dataType, &$data)
    {
        if (empty($data)) {
            return [];
        }
        // group by lecture_type_long
        if (in_array($dataType, ['lectureByID', 'lectureByLecturerID', 'lectureByLecturer', 'lectureByDepartment'])) {

            // 2021-09-23 quickfix because there is a bug in Campo-API's filtering by language
            if (!empty($this->atts['lang'])) {
                $data = $this->filterByLang($data);
            }

            // 2021-10-01 quickfix because there is a bug in Campo-API's filtering by type
            if (!empty($this->atts['type'])) {
                $data = $this->filterByType($data);
            }

            // 2022-01-13 Campo-API's does not support filtering by gast ("für Gaststudium geeignet")
            if (!empty($this->atts['gast'])) {
                $data = $this->filterByGast($data);
            }

            $data = $this->groupBy($data, 'lecture_type_long');

            // sort by attribute "order"
            if (!empty($this->atts['order'])) {
                $aOrder = explode(',', $this->atts['order']);
                $sortedData = [];
                foreach ($aOrder as $order) {
                    foreach ($data as $lecture_type_long => $lectures) {
                        foreach ($lectures as $lecture) {
                            if ($lecture['lecture_type'] == trim($order)) {
                                $sortedData[$lecture_type_long] = $data[$lecture_type_long];
                                unset($data[$lecture_type_long]);
                                break 1;
                            }
                        }
                    }
                }
                $data = $sortedData;
            }
        }
        // sort by name
        if (in_array($dataType, ['departmentByName', 'departmentAll'])) {
            usort($data, [$this, 'sortByName']);
        }

        return $data;
    }

    private function filterByGast($arr)
    {
        $ret = [];
        foreach ($arr as $key => $val) {
            if (!empty($val['gast']) && ($val['gast'] == $this->gast)) {
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    private function filterByLang($arr)
    {
        $ret = [];
        foreach ($arr as $key => $val) {
            if (!empty($val['leclanguage']) && ($val['leclanguage'] == $this->atts['lang'])) {
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    private function multiMap($val)
    {
        return trim(strtolower($val));
    }

    private function filterByType($arr)
    {
        $ret = [];
        $aTypes = array_map([$this, 'multiMap'], explode(',', $this->atts['type']));

        foreach ($arr as $key => $val) {
            if (!empty($val['lecture_type']) && in_array($val['lecture_type'], $aTypes)) {
                $ret[$key] = $val;
            }
        }

        return $ret;
    }

    private function groupBy($arr, $key)
    {
        $ret = [];
        foreach ($arr as $val) {
            if (!empty($val[$key])) {
                $ret[$val[$key]][] = $val;
            }
        }
        return $ret;
    }

    private function sortByLastname($a, $b)
    {
        return strcasecmp($a["lastname"], $b["lastname"]);
    }

    private function sortByName($a, $b)
    {
        return strcasecmp($a["name"], $b["name"]);
    }

    private function sortByYear($a, $b)
    {
        return strcasecmp($b["year"], $a["year"]);
    }

    public static function checkSemester($sem)
    {
        return preg_match('/[12]\d{3}[ws]/', $sem);
    }

    public static function correctPhone($phone)
    {
        if ((strpos($phone, '+49 9131 85-') !== 0) && (strpos($phone, '+49 911 5302-') !== 0)) {
            if (!preg_match('/\+49 [1-9][0-9]{1,4} [1-9][0-9]+/', $phone)) {
                $phone_data = preg_replace('/\D/', '', $phone);
                $vorwahl_erl = '+49 9131 85-';
                $vorwahl_nbg = '+49 911 5302-';

                switch (strlen($phone_data)) {
                    case '3':
                        $phone = $vorwahl_nbg . $phone_data;
                        break;

                    case '5':
                        if (strpos($phone_data, '06') === 0) {
                            $phone = $vorwahl_nbg . substr($phone_data, -3);
                            break;
                        }
                        $phone = $vorwahl_erl . $phone_data;
                        break;

                    case '7':
                        if (strpos($phone_data, '85') === 0 || strpos($phone_data, '06') === 0) {
                            $phone = $vorwahl_erl . substr($phone_data, -5);
                            break;
                        }

                        if (strpos($phone_data, '5302') === 0) {
                            $phone = $vorwahl_nbg . substr($phone_data, -3);
                            break;
                        }

                    // no break
                    default:
                        if (strpos($phone_data, '9115302') !== false) {
                            $durchwahl = explode('9115302', $phone_data);
                            if (strlen($durchwahl[1]) === 3 || strlen($durchwahl[1]) === 5) {
                                $phone = $vorwahl_nbg . $durchwahl[1];
                            }
                            break;
                        }

                        if (strpos($phone_data, '913185') !== false) {
                            $durchwahl = explode('913185', $phone_data);
                            if (strlen($durchwahl[1]) === 5) {
                                $phone = $vorwahl_erl . $durchwahl[1];
                            }
                            break;
                        }

                        if (strpos($phone_data, '09131') === 0 || strpos($phone_data, '499131') === 0) {
                            $durchwahl = explode('9131', $phone_data);
                            $phone = "+49 9131 " . $durchwahl[1];
                            break;
                        }

                        if (strpos($phone_data, '0911') === 0 || strpos($phone_data, '49911') === 0) {
                            $durchwahl = explode('911', $phone_data);
                            $phone = "+49 911 " . $durchwahl[1];
                            break;
                        }
                }
            }
        }
        return $phone;
    }

    public function getInt($str)
    {
        preg_match_all('/\d+/', $str, $matches);
        return implode('', $matches[0]);
    }

    public function formatCampo($txt)
    {
        $subs = array(
            '/^\-+\s+(.*)?/mi' => '<ul><li>$1</li></ul>', // list
            '/(<\/ul>\n(.*)<ul>*)+/' => '', // list
            '/\*{2}/m' => '/\*/', // **
            '/_{2}/m' => '/_/', // __
            '/\|(.*)\|/m' => '<i>$1</i>', // |itallic|
            '/_(.*)_/m' => '<sub>$1</sub>', // H_2_O
            '/\^(.*)\^/m' => '<sup>$1</sup>', // pi^2^
            '/\[([^\]]*)\]\s{0,1}((http|https|ftp|ftps):\/\/\S*)/mi' => '<a href="$2">$1</a>', // [link text] http...
            '/\[([^\]]*)\]\s{0,1}(mailto:)([^")\s<>]+)/mi' => '<a href="mailto:$3">$1</a>', // find [link text] mailto:email@address.tld but not <a href="mailto:email@address.tld">mailto:email@address.tld</a>
            '/\*(.*)\*/m' => '<strong>$1</strong>', // *bold*
        );

        $txt = preg_replace(array_keys($subs), array_values($subs), $txt);
        $txt = nl2br($txt);
        $txt = make_clickable($txt);
        return $txt;
    }

    private function dict(&$data)
    {
        $fields = [
            'title' => [
                "Dr." => __('Doktor', 'rrze-campo'),
                "Prof." => __('Professor', 'rrze-campo'),
                "Dipl." => __('Diplom', 'rrze-campo'),
                "Inf." => __('Informatik', 'rrze-campo'),
                "Wi." => __('Wirtschaftsinformatik', 'rrze-campo'),
                "Ma." => __('Mathematik', 'rrze-campo'),
                "Ing." => __('Ingenieurwissenschaft', 'rrze-campo'),
                "B.A." => __('Bakkalaureus', 'rrze-campo'),
                "M.A." => __('Magister Artium', 'rrze-campo'),
                "phil." => __('Geisteswissenschaft', 'rrze-campo'),
                "pol." => __('Politikwissenschaft', 'rrze-campo'),
                "nat." => __('Naturwissenschaft', 'rrze-campo'),
                "soc." => __('Sozialwissenschaft', 'rrze-campo'),
                "techn." => __('technische Wissenschaften', 'rrze-campo'),
                "vet.med." => __('Tiermedizin', 'rrze-campo'),
                "med.dent." => __('Zahnmedizin', 'rrze-campo'),
                "h.c." => __('ehrenhalber', 'rrze-campo'),
                "med." => __('Medizin', 'rrze-campo'),
                "jur." => __('Recht', 'rrze-campo'),
                "rer." => "",
            ],
            'lecture_type' => [
                "awa" => __('Anleitung zu wiss. Arbeiten (AWA)', 'rrze-campo'),
                "ku" => __('Kurs (KU)', 'rrze-campo'),
                "ak" => __('Aufbaukurs (AK)', 'rrze-campo'),
                "ex" => __('Exkursion (EX)', 'rrze-campo'),
                "gk" => __('Grundkurs (GK)', 'rrze-campo'),
                "sem" => __('Seminar (SEM)', 'rrze-campo'),
                "es" => __('Examensseminar (ES)', 'rrze-campo'),
                "ts" => __('Theorieseminar (TS)', 'rrze-campo'),
                "ag" => __('Arbeitsgemeinschaft (AG)', 'rrze-campo'),
                "mas" => __('Masterseminar (MAS)', 'rrze-campo'),
                "gs" => __('Grundseminar (GS)', 'rrze-campo'),
                "us" => __('Übungsseminar (US)', 'rrze-campo'),
                "as" => __('Aufbauseminar (AS)', 'rrze-campo'),
                "hs" => __('Hauptseminar (HS)', 'rrze-campo'),
                "re" => __('Repetitorium (RE)', 'rrze-campo'),
                "kk" => __('Klausurenkurs (KK)', 'rrze-campo'),
                "klv" => __('Klinische Visite (KLV)', 'rrze-campo'),
                "ko" => __('Kolloquium (KO)', 'rrze-campo'),
                "ks" => __('Kombiseminar (KS)', 'rrze-campo'),
                "ek" => __('Einführungskurs (EK)', 'rrze-campo'),
                "ms" => __('Mittelseminar (MS)', 'rrze-campo'),
                "os" => __('Oberseminar (OS)', 'rrze-campo'),
                "pr" => __('Praktikum (PR)', 'rrze-campo'),
                "prs" => __('Praxisseminar (PRS)', 'rrze-campo'),
                "pjs" => __('Projektseminar (PJS)', 'rrze-campo'),
                "ps" => __('Proseminar (PS)', 'rrze-campo'),
                "sl" => __('Sonstige Lecture (SL)', 'rrze-campo'),
                "tut" => __('Tutorium (TUT)', 'rrze-campo'),
                "v-ue" => __('Vorlesung mit Übung (V/UE)', 'rrze-campo'),
                "ue" => __('Übung (UE)', 'rrze-campo'),
                "vorl" => __('Vorlesung (VORL)', 'rrze-campo'),
                "hvl" => __('Hauptvorlesung (HVL)', 'rrze-campo'),
                "pf" => __('Prüfung (PF)', 'rrze-campo'),
                "gsz" => __('Gremiensitzung (GSZ)', 'rrze-campo'),
                "ppu" => __('Propädeutische Übung (PPU)', 'rrze-campo'),
                "his" => __('Sprachhistorisches Seminar (HIS)', 'rrze-campo'),
                "bsem" => __('Begleitseminar (BSEM)', 'rrze-campo'),
                "kol" => __('Kolleg (KOL)', 'rrze-campo'),
                "mhs" => __('MS (HS, PO 2020) (MHS)', 'rrze-campo'),
                "pgmas" => __('PG Masterseminar (PGMAS)', 'rrze-campo'),
                "pms" => __('PS (MS, PO 2020) (PMS)', 'rrze-campo'),
            ],
            'repeat' => [
                "w1" => "",
                "w2" => __('Jede zweite Woche', 'rrze-campo'),
                "w3" => __('Jede dritte Woche', 'rrze-campo'),
                "w4" => __('Jede vierte Woche', 'rrze-campo'),
                "w5" => "",
                "m1" => "",
                "s1" => __('Einzeltermin am', 'rrze-campo'),
                "bd" => __('Blocklecture', 'rrze-campo'),
                '0' => __(' So', 'rrze-campo'),
                '1' => __(' Mo', 'rrze-campo'),
                '2' => __(' Di', 'rrze-campo'),
                '3' => __(' Mi', 'rrze-campo'),
                '4' => __(' Do', 'rrze-campo'),
                '5' => __(' Fr', 'rrze-campo'),
                '6' => __(' Sa', 'rrze-campo'),
                '7' => __(' So', 'rrze-campo'),
            ],
            'publication_type' => [
                "artmono" => __('Artikel im Sammelband', 'rrze-campo'),
                "arttagu" => __('Artikel im Tagungsband', 'rrze-campo'),
                "artzeit" => __('Artikel in Zeitschrift', 'rrze-campo'),
                "techrep" => __('Interner Bericht (Technischer Bericht, Forschungsbericht)', 'rrze-campo'),
                "hschri" => __('Hochschulschrift (Dissertation, Habilitationsschrift, Diplomarbeit etc.)', 'rrze-campo'),
                "dissvg" => __('Hochschulschrift (auch im Verlag erschienen)', 'rrze-campo'),
                "monogr" => __('Monographie', 'rrze-campo'),
                "tagband" => __('Tagungsband (nicht im Verlag erschienen)', 'rrze-campo'),
                "schutzr" => __('Schutzrecht', 'rrze-campo'),
            ],
            'hstype' => [
                "diss" => __('Dissertation', 'rrze-campo'),
                "dipl" => __('Diplomarbeit', 'rrze-campo'),
                "mag" => __('Magisterarbeit', 'rrze-campo'),
                "stud" => __('Studienarbeit', 'rrze-campo'),
                "habil" => __('Habilitationsschrift', 'rrze-campo'),
                "masth" => __('Masterarbeit', 'rrze-campo'),
                "bacth" => __('Bachelorarbeit', 'rrze-campo'),
                "intber" => __('Interner Bericht', 'rrze-campo'),
                "diskus" => __('Diskussionspapier', 'rrze-campo'),
                "discus" => __('Discussion paper', 'rrze-campo'),
                "forber" => __('Forschungsbericht', 'rrze-campo'),
                "absber" => __('Abschlussbericht', 'rrze-campo'),
                "patschri" => __('Patentschrift', 'rrze-campo'),
                "offenleg" => __('Offenlegungsschrift', 'rrze-campo'),
                "patanmel" => __('Patentanmeldung', 'rrze-campo'),
                "gebrmust" => __('Gebrauchsmuster', 'rrze-campo'),
            ],
            'leclanguage' => [
                0 => __('Unterrichtssprache Deutsch', 'rrze-campo'),
                "D" => __('Unterrichtssprache Deutsch', 'rrze-campo'),
                "E" => __('Unterrichtssprache Englisch', 'rrze-campo'),
            ],
            'sws' => __(' SWS', 'rrze-campo'),
            'schein' => __('Schein', 'rrze-campo'),
            'ects' => __('ECTS-Studium', 'rrze-campo'),
            'ects_cred' => __('ECTS-Credits: ', 'rrze-campo'),
            'beginners' => __('Für Anfänger geeignet', 'rrze-campo'),
            'fruehstud' => __('Frühstudium', 'rrze-campo'),
            'gast' => __('Für Gasthörer zugelassen', 'rrze-campo'),
            'evaluation' => __('Evaluation', 'rrze-campo'),
            'locations' => '',
            'organizational' => '',
        ];

        foreach ($data as $nr => $row) {
            foreach ($fields as $field => $values) {
                if (isset($data[$nr][$field]) && ($field == 'locations')) {
                    foreach ($data[$nr]['locations'] as $l_nr => $location) {
                        if (!empty($location['tel'])) {
                            $data[$nr]['locations'][$l_nr]['tel'] = self::correctPhone($data[$nr]['locations'][$l_nr]['tel']);
                            $data[$nr]['locations'][$l_nr]['tel_call'] = '+' . self::getInt($data[$nr]['locations'][$l_nr]['tel']);
                        }
                        if (!empty($location['fax'])) {
                            $data[$nr]['locations'][$l_nr]['fax'] = self::correctPhone($data[$nr]['locations'][$l_nr]['fax']);
                        }
                        if (!empty($location['mobile'])) {
                            $data[$nr]['locations'][$l_nr]['mobile'] = self::correctPhone($data[$nr]['locations'][$l_nr]['mobile']);
                            $data[$nr]['locations'][$l_nr]['mobile_call'] = '+' . self::getInt($data[$nr]['locations'][$l_nr]['mobile']);
                        }
                    }
                } elseif ($field == 'repeat') {
                    if (isset($data[$nr]['courses'])) {
                        foreach ($data[$nr]['courses'] as $c_nr => $course) {
                            foreach ($course['term'] as $m_nr => $meeting) {
                                if (isset($data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat'])) {
                                    $data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat'] = str_replace(array_keys($values), array_values($values), $data[$nr]['courses'][$c_nr]['term'][$m_nr]['repeat']);
                                }
                            }
                        }
                    } elseif (isset($data[$nr]['officehours'])) {
                        foreach ($data[$nr]['officehours'] as $c_nr => $entry) {
                            if (isset($data[$nr]['officehours'][$c_nr]['repeat'])) {
                                $data[$nr]['officehours'][$c_nr]['repeat'] = trim(str_replace(array_keys($values), array_values($values), $data[$nr]['officehours'][$c_nr]['repeat']));
                            }
                        }
                    }
                } elseif ($field == 'organizational') {
                    if (isset($data[$nr][$field])) {
                        $data[$nr][$field] = self::formatCampo($data[$nr][$field]);
                    }
                } elseif (isset($data[$nr][$field])) {
                    if (in_array($field, ['title'])) {
                        // multi replace
                        $data[$nr][$field . '_long'] = str_replace(array_keys($values), array_values($values), $data[$nr][$field]);
                    } else {
                        if (!is_array($values)) {
                            if ($field == 'sws') {
                                $data[$nr][$field] .= $values;
                            } elseif ($field == 'ects_cred') {
                                $data[$nr][$field] = $values . $data[$nr][$field];
                            } else {
                                $data[$nr][$field] = $values;
                            }
                        } else {
                            if (isset($row[$field]) && isset($values[$row[$field]])) {
                                $data[$nr][$field . '_long'] = $values[$row[$field]];
                                if ($field == 'lecture_type') {
                                    $data[$nr][$field . '_short'] = trim(substr($values[$row[$field]], 0, strpos($values[$row[$field]], '(')));
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

}

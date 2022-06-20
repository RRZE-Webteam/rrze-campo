<?php 

$aAllowedColors = [
    'med',
    'nat',
    'rw',
    'phil',
    'tk',
];

$aColor = array_intersect($this->show, $aAllowedColors);
$this->atts['color'] = (!empty($aColor) ? $aColor[1] : '');

$ret = '<div class="rrze-campo">';
if ($data){
    $lang = get_locale();
    $options = get_option('rrze-campo');
    $ssstart = (!empty($options['basic_ssStart']) ? $options['basic_ssStart'] : 0);
    $ssend = (!empty($options['basic_ssEnd']) ? $options['basic_ssEnd'] : 0);
    $wsstart = (!empty($options['basic_wsStart']) ? $options['basic_wsStart'] : 0);
    $wsend = (!empty($options['basic_wsEnd']) ? $options['basic_wsEnd'] : 0);

    if (in_array('accordion', $this->show)){
        $ret .= '[collapsibles hstart="' . $this->atts['hstart'] . '"]';
    }

    foreach ($data as $type => $lectures){

        if (in_array('accordion', $this->show)){
            $ret .= '[collapse title="' . $type . '" name="' . urlencode($type) . '" color="' . $this->atts['color'] . '"]';
        }else{
            $ret .= '<h' . $this->atts['hstart'] . '>' . $type . '</h' . $this->atts['hstart'] . '>';
        }

        $ret .= '<ul>';
        foreach ($lectures as $lecture){
            $url = get_permalink() . 'lv_id/' . $lecture['lecture_id'];
			$ret .= '<li>';
            $ret .= '<h' . ($this->atts['hstart'] + 1) . '><a href="' . $url . '">';
            if ($lang != 'de_DE' && $lang != 'de_DE_formal' && !empty($lecture['ects_name'])) {
                $lecture['title'] = $lecture['ects_name'];
            } else {
                $lecture['title'] = $lecture['name'];
            }
            $ret .= $lecture['title'];
            $ret .= '</a></h' . ($this->atts['hstart'] + 1) . '>';
            if (!empty($lecture['comment']) && !in_array('comment', $this->hide)) {
                $ret .= '<p>' . make_clickable($lecture['comment']) . '</p>';
            }
            if (!empty($lecture['organizational']) && !in_array('organizational', $this->hide)) {
                $ret .= '<p>' . make_clickable($lecture['organizational']) . '</p>';
            }

            $ret .= '<ul class="datemeta">';
            $ret .= '<li>';
            $infos = '';
            if (!empty($lecture['sws'])) {
                $infos .= '<span>' . $lecture['sws'] . '</span>';
            }
            if (!empty($lecture['maxturnout'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . __('Expected participants', 'rrze-campo') . ': ' . $lecture['maxturnout'] . '</span>';
            }
            if (!empty($lecture['fruehstud'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $lecture['fruehstud'] . '</span>';
            }
            if (!empty($lecture['gast'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $lecture['gast'] . '</span>';
            }
            if (!empty($lecture['schein'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $lecture['schein'] . '</span>';
            }
            if (!empty($lecture['ects'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $lecture['ects'] . '</span>';
                if (!empty($lecture['ects_cred'])) {
                    $infos .= ' (' . $lecture['ects_cred'] . ')';
                }
                $infos .= '</span>';
            }
            if (!empty($lecture['leclanguage_long']) && ($lecture['leclanguage_long'] != __('Lecture\'s language German', 'rrze-campo'))) {
                if (!empty($infos)) {$infos .= ', ';}
                $infos .= '<span>' . $lecture['leclanguage_long'] . '</span>';
            }
            $ret .= $infos . '</li>';

			$courseDates = '<li class="termindaten">' . __('Date', 'rrze-campo') . ':';
			$courseDates .= '<ul>';

            if (isset($lecture['courses'])){
                foreach ($lecture['courses'] as $course){
                    if ((empty($lecture['lecturer_key']) || empty($course['doz'])) || (!empty($lecture['lecturer_key']) && !empty($course['doz']) && (in_array($lecture['lecturer_key'], $course['doz'])))) {
                        foreach ($course['term'] as $term){
                            $t = array();
                            $time = array();
                            if (!empty($term['repeat'])){
                                $t['repeat'] = $term['repeat'];
                            }
                            if (!empty($term['startdate'])){
                                if (!empty($term['enddate']) && $term['startdate'] != $term['enddate']){
                                    $t['date'] = date("d.m.Y", strtotime($term['startdate'])) . '-' . date("d.m.Y", strtotime($term['enddate']));
                                }else{
                                    $t['date'] = date("d.m.Y", strtotime($term['startdate']));
                                }
                            }
                            if (!empty($term['starttime'])){
                                $time['starttime'] = $term['starttime'];
                            }
                            if (!empty($term['endtime'])){
                                $time['endtime'] = $term['endtime'];
                            }
                            if (!empty($time)){
                                $t['time'] = $time['starttime'] . '-' . $time['endtime'];
                            }else{
                                $t['time'] = __('Time on appointment', 'rrze-campo');
                            }
                            if (!empty($term['room']['short'])){
                                $t['room'] = __('Room', 'rrze-campo') . ' ' . $term['room']['short'];
                            }
                            if (!empty($term['exclude'])){
                                $t['exclude'] = '(' . __('exclude', 'rrze-campo') . ' ' . $term['exclude'] . ')';
                            }
                            if (!empty($course['coursename'])){
                                $t['coursename'] = '(' . __('Course', 'rrze-campo') . ' ' . $course['coursename'] . ')';
                            }
                            // ICS
                            if (in_array('ics', $this->show) && !in_array('ics', $this->hide)) {
                                $props = [
                                    'summary' => $lecture['title'],
                                    'startdate' => (!empty($term['startdate']) ? $term['startdate'] : null),
                                    'enddate' => (!empty($term['enddate']) ? $term['enddate'] : null),
                                    'starttime' => (!empty($term['starttime']) ? $term['starttime'] : null),
                                    'endtime' => (!empty($term['endtime']) ? $term['endtime'] : null),
                                    'repeat' => (!empty($term['repeat']) ? $term['repeat'] : null),
                                    'location' => (!empty($t['room']) ? $t['room'] : null),
                                    'description' => (!empty($lecture['comment']) ? $lecture['comment'] : null),
                                    'url' => get_permalink(),
                                    'map' => (!empty($term['room']['north']) && !empty($term['room']['east']) ? 'https://karte.fau.de/api/v1/iframe/marker/' . $term['room']['north'] . ',' . $term['room']['east'] . '/zoom/16' : ''),
                                    'filename' => sanitize_file_name($type),
                                    'ssstart' => $ssstart,
                                    'ssend' => $ssend,
                                    'wsstart' => $wsstart,
                                    'wsend' => $wsend,
                                ];

                                $screenReaderTxt = __('ICS', 'rrze-campo') . ': ' . __('Date', 'rrze-campo') . ' ' . (!empty($t['repeat']) ? $t['repeat'] : '') . ' ' . (!empty($t['date']) ? $t['date'] . ' ' : '') . $t['time'] . ' ' . __('import to calendar', 'rrze-campo');
                                $t['ics'] = '<span class="lecture-info-ics" itemprop="ics"><a href="' . plugin_dir_url(__DIR__) . 'ics.php?' . http_build_query($props) . '" aria-label="' . $screenReaderTxt . '">' . __('ICS', 'rrze-campo') . '</a></span>';
                            }
                            $t['time'] .= ',';
                            $term_formatted = implode(' ', $t);
						    $courseDates .= '<li>' . $term_formatted . '</li>';
                        }
                    }
                }
            }else{
			    $courseDates .= '<li>' . __('Time and place on appointment', 'rrze-campo') . '</li>';
            }
		    $courseDates .= '</ul>';
		    $courseDates .= '</li>';
		    $ret .= $courseDates. '</li>';
        }
        $ret .= '</ul>';

        if (in_array('accordion', $this->show)){
            $ret .= '[/collapse]';
        }
    }

    if (in_array('accordion', $this->show)){
        $ret .= '[/collapsibles]';
        $ret = do_shortcode($ret);
    }
}

$ret .= '</div>';

echo $ret;
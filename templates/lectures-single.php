<div class="rrze-campo">
<?php if ($lecture):
    $lang = get_locale();
    $options = get_option('rrze-campo');
    $ssstart = (!empty($options['basic_ssStart']) ? $options['basic_ssStart'] : 0);
    $ssend = (!empty($options['basic_ssEnd']) ? $options['basic_ssEnd'] : 0);
    $wsstart = (!empty($options['basic_wsStart']) ? $options['basic_wsStart'] : 0);
    $wsend = (!empty($options['basic_wsEnd']) ? $options['basic_wsEnd'] : 0);

    echo '<div itemscope itemtype="https://schema.org/Course">';

    echo '<h' . $this->atts['hstart'] . '>';
    if ($lang != 'de_DE' && $lang != 'de_DE_formal' && !empty($lecture['ects_name'])) {
        $lecture['title'] = $lecture['ects_name'];
    } else {
        $lecture['title'] = $lecture['name'];
    }
    echo '<span itemprop="name">' . $lecture['title'] . '</span>';

    // echo '<span itemprop="provider" itemscope itemtype="http://schema.org/EducationalOrganization">;

    echo '</h' . $this->atts['hstart'] . '>';
    if (!empty($lecture['lecturers'])):
        echo '<h' . ($this->atts['hstart'] + 1) . '>' . __('Lecturers', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 1) . '>';
        ?>
		        <ul>
		        <?php
        foreach ($lecture['lecturers'] as $doz):
            $name = array();
            if (!empty($doz['title'])):
                $name['title'] = '<span itemprop="honorificPrefix">' . $doz['title'] . '</span>';
            endif;
            if (!empty($doz['firstname'])):
                $name['firstname'] = '<span itemprop="givenName">' . $doz['firstname'] . '</span>';
            endif;
            if (!empty($doz['lastname'])):
                $name['lastname'] = '<span itemprop="familyName">' . $doz['lastname'] . '</span>';
            endif;
            $fullname = implode(' ', $name);
            if (!empty($doz['person_id'])):
                $url = '<a href="' . get_permalink() . 'campoid/' . $doz['person_id'] . '">' . $fullname . '</a>';
            else:
                $url = $fullname;
            endif;?>
			            <li itemprop="provider" itemscope itemtype="http://schema.org/Person"><?php echo $url; ?></li>
			            <?php
        endforeach;?>
		        </ul>
		    <?php endif;

    echo '<h' . ($this->atts['hstart'] + 1) . '>' . __('Details', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 1) . '>';

    if (!empty($lecture['angaben'])): ?>
	        <p><?php echo make_clickable($lecture['angaben']); ?></p>
	    <?php endif;

echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Time and place', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 2) . '>';
if (array_key_exists('comment', $lecture)): ?>
        <p><?php echo make_clickable($lecture['comment']); ?></p>
    <?php endif;?>
    <ul>
        <?php if (isset($lecture['courses'])):
    foreach ($lecture['courses'] as $course):
        foreach ($course['term'] as $term):
            $t = array();
            $time = array();
            if (!empty($term['repeat'])):
                $t['repeat'] = $term['repeat'];
            endif;
            if (!empty($term['startdate'])):
                if (!empty($term['enddate']) && $term['startdate'] != $term['enddate']):
                    $t['date'] = date("d.m.Y", strtotime($term['startdate'])) . '-' . date("d.m.Y", strtotime($term['enddate']));
                else:
                    $t['date'] = date("d.m.Y", strtotime($term['startdate']));
                endif;
            endif;
            if (!empty($term['starttime'])):
                $time['starttime'] = $term['starttime'];
            endif;
            if (!empty($term['endtime'])):
                $time['endtime'] = $term['endtime'];
            endif;
            if (!empty($time)):
                $t['time'] = $time['starttime'] . '-' . $time['endtime'];
            else:
                $t['time'] = __('Time on appointment', 'rrze-campo');
            endif;
            if (!empty($term['room']['short'])):
                $t['room'] = __('Room', 'rrze-campo') . ' ' . $term['room']['short'];
            endif;
            if (!empty($term['exclude'])):
                $t['exclude'] = '(' . __('exclude', 'rrze-campo') . ' ' . $term['exclude'] . ')';
            endif;
            // Kursname
            if (!empty($course['coursename'])):
                $t['coursename'] = '(' . __('Course', 'rrze-campo') . ' ' . $course['coursename'] . ')';
            endif;
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
                    'filename' => sanitize_file_name($lecture['lecture_type_long']),
                    'ssstart' => $ssstart,
                    'ssend' => $ssend,
                    'wsstart' => $wsstart,
                    'wsend' => $wsend,
                ];

                $screenReaderTxt = __('ICS', 'rrze-campo') . ': ' . __('Termin', 'rrze-campo') . ' ' . (!empty($t['repeat']) ? $t['repeat'] : '') . ' ' . (!empty($t['date']) ? $t['date'] . ' ' : '') . $t['time'] . ' ' . __('in den Kalender importieren', 'rrze-campo');
                $t['ics'] = '<span class="lecture-info-ics" itemprop="ics"><a href="' . plugin_dir_url(__DIR__) . 'ics.php?' . http_build_query($props) . '" aria-label="' . $screenReaderTxt . '">' . __('ICS', 'rrze-campo') . '</a></span>';
            }
            $t['time'] .= ',';
            $term_formatted = implode(' ', $t);
            ?>
			                    <li><?php echo $term_formatted; ?></li>
			            <?php endforeach;
    endforeach;
else: ?>
            <li><?php __('Time and place on appointment', 'rrze-campo');?></li>
        <?php endif;?>
    </ul>

    <?php if (array_key_exists('studs', $lecture) && array_key_exists('stud', $lecture['studs'][0])):
    echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Fields of study', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 2) . '>';
    ?>
	    <ul>
	        <?php
    foreach ($lecture['studs'][0]['stud'] as $stud):
        $s = array();
        if (!empty($stud['pflicht'])):
            $s['pflicht'] = $stud['pflicht'];
        endif;
        if (!empty($stud['richt'])):
            $s['richt'] = $stud['richt'];
        endif;
        if (!empty($stud['sem'][0]) && absint($stud['sem'][0])):
            $s['sem'] = sprintf('%s %d', __('from SEM', 'rrze-campo'), absint($stud['sem'][0]));
        endif;
        $studinfo = implode(' ', $s);
        ?>
		            <li><?php echo $studinfo; ?></li>
		    <?php endforeach;?>
	    </ul>
	    <?php endif;?>


    <?php if (!empty($lecture['organizational'])): ?>
        <h4><?php __('Prerequisites / Organizational information', 'rrze-campo');?></h4>
        <p><?php echo $lecture['organizational']; ?></p>
        <?php endif;
?>


    <?php
if (!empty($lecture['summary'])) {
    echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Content', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 2) . '>';
    echo '<p itemprop="description">' . make_clickable($lecture['summary']) . '</p>';
}

if (!empty($lecture['literature'])) {
    echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Recommended Literature', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 2) . '>';
    echo '<p>' . make_clickable($lecture['literature']) . '</p>';
}
if (!empty($lecture['ects_infos'])) {
    echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('ECTS information', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 2) . '>';
    if (!empty($lecture['ects_name'])) {
        echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Title', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 3) . '>';
        echo '<p>' . $lecture['ects_name'] . '</p>';
    }
    if (!empty($lecture['ects_cred'])) {
        echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Credits', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 3) . '>';
        echo '<p>' . $lecture['ects_cred'] . '</p>';
    }
    if (!empty($lecture['ects_summary'])) {
        echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Content', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 3) . '>';
        echo '<p>' . $lecture['ects_summary'] . '</p>';
    }
    if (!empty($lecture['ects_literature'])) {
        echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Literature', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 3) . '>';
        echo '<p>' . $lecture['ects_literature'] . '</p>';
    }
}

if (!empty($lecture['keywords']) || !empty($lecture['maxturnout']) || !empty($lecture['url_description'])) {
    echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Additional information', 'rrze-campo') . '</h' . ($this->atts['hstart'] + 2) . '>';
    if (!empty($lecture['keywords'])) {
        echo '<p>' . __('Keywords', 'rrze-campo') . ': ' . $lecture['keywords'] . '</p>';
    }
    if (!empty($lecture['maxturnout'])) {
        echo '<p>' . __('Expected participants', 'rrze-campo') . ': ' . $lecture['maxturnout'] . '</p>';
    }
    if (!empty($lecture['url_description'])) {
        echo '<p>www: <a href="' . $lecture['url_description'] . '">' . $lecture['url_description'] . '</a></p>';
    }
}

// echo '<div itemprop="provider" itemscope itemtype="https://schema.org/provider">';
// echo '<span itemprop="name">FAU</span>';
// echo '<span itemprop="url">https://www.fau.de</span>';
// echo '</div>';

echo '</div>'; // schema

endif;?>
</div>
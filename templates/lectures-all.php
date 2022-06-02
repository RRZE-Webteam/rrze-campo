<div class="rrze-campo">
<?php if ($data):
    $lang = get_locale();
    $options = get_option('rrze-campo');
    $ssstart = (!empty($options['basic_ssStart']) ? $options['basic_ssStart'] : 0);
    $ssend = (!empty($options['basic_ssEnd']) ? $options['basic_ssEnd'] : 0);
    $wsstart = (!empty($options['basic_wsStart']) ? $options['basic_wsStart'] : 0);
    $wsend = (!empty($options['basic_wsEnd']) ? $options['basic_wsEnd'] : 0);

    foreach ($data as $typ => $lectureen):
        echo '<h' . $this->atts['hstart'] . '>' . $typ . '</h' . $this->atts['hstart'] . '>';
        ?>
					<ul>
				        <?php
        foreach ($lectureen as $lecture):
            $url = get_permalink() . 'lv_id/' . $lecture['lecture_id'];
            ?>
						                <li>
						                <?php
            echo '<h' . ($this->atts['hstart'] + 1) . '><a href="' . $url . '">';
            if ($lang != 'de_DE' && $lang != 'de_DE_formal' && !empty($lecture['ects_name'])) {
                $lecture['title'] = $lecture['ects_name'];
            } else {
                $lecture['title'] = $lecture['name'];
            }
            echo $lecture['title'];
            echo '</a></h' . ($this->atts['hstart'] + 1) . '>';
            if (!empty($lecture['comment']) && !in_array('comment', $this->hide)) {
                echo '<p>' . make_clickable($lecture['comment']) . '</p>';
            }
            if (!empty($lecture['organizational']) && !in_array('organizational', $this->hide)) {
                echo '<p>' . make_clickable($lecture['organizational']) . '</p>';
            }

            echo '<ul class="terminmeta">';
            echo '<li>';

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

            if (!empty($lecture['leclanguage_long']) && ($lecture['leclanguage_long'] != __('Unterrichtssprache Deutsch', 'rrze-campo'))) {
                if (!empty($infos)) {$infos .= ', ';}
                $infos .= '<span>' . $lecture['leclanguage_long'] . '</span>';
            }
            echo $infos . '</li>';
            ?>
								<li class="termindaten"><?php _e('Termin', 'rrze-campo');?>:
						                <ul>
						                        <?php
            if (isset($lecture['courses'])):
                foreach ($lecture['courses'] as $course):
                    if ((empty($lecture['lecturer_key']) || empty($course['doz'])) || (!empty($lecture['lecturer_key']) && !empty($course['doz']) && (in_array($lecture['lecturer_key'], $course['doz'])))) {
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
                                    'filename' => sanitize_file_name($typ),
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
												                                    <?php
                    endforeach;
                    }
                endforeach;
            else: ?>
						                                <li><?php _e('Time and place on appointment', 'rrze-campo');?></li>
						                        <?php endif;?>
				                        </ul>
						    </li>
						    </ul>
				                </li>
				                <?php
    endforeach;
    ?>
			</ul>
		    <?php
endforeach;

endif;
?>
</div>
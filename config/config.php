<?php

namespace RRZE\Campo\Config;

defined('ABSPATH') || exit;

/**
 * Gibt der Name der Option zurück.
 * @return array [description]
 */
function getOptionName()
{
    return 'rrze-campo';
}

function getConstants()
{
    $options = array(
        'fauthemes' => [
            'FAU-Einrichtungen',
            'FAU-Einrichtungen-BETA',
            'FAU-Medfak',
            'FAU-RWFak',
            'FAU-Philfak',
            'FAU-Techfak',
            'FAU-Natfak',
            'FAU-Blog',
            'FAU-Jobs',
        ],
        'rrzethemes' => [
            'RRZE 2019',
        ],
        'langcodes' => [
            "de" => __('German', 'rrze-synonym'),
            "en" => __('English', 'rrze-synonym'),
            "es" => __('Spanish', 'rrze-synonym'),
            "fr" => __('French', 'rrze-synonym'),
            "ru" => __('Russian', 'rrze-synonym'),
            "zh" => __('Chinese', 'rrze-synonym'),
        ],
    );
    return $options;
}

/**
 * Gibt die Einstellungen des Menus zurück.
 * @return array [description]
 */
function getMenuSettings()
{
    return [
        'page_title' => __('RRZE Campo', 'rrze-campo'),
        'menu_title' => __('RRZE Campo', 'rrze-campo'),
        'capability' => 'manage_options',
        'menu_slug' => 'rrze-campo',
        'title' => __('RRZE Campo Settings', 'rrze-campo'),
    ];
}


/**
 * Gibt die Einstellungen der Optionsbereiche zurück.
 * @return array [description]
 */
function getSections()
{
    return [
        [
            'id' => 'basic',
            'title' => __('Campo Settings', 'rrze-campo'),
        ],
    ];
}

/**
 * Gibt die Einstellungen der Optionsfelder zurück.
 * @return array [description]
 */
function getFields()
{
    return [
        'basic' => [
            [
                'name' => 'campo_url',
                'label' => __('Link to Campo', 'rrze-campo'),
                'desc' => __('', 'rrze-campo'),
                'placeholder' => __('', 'rrze-campo'),
                'type' => 'text',
                'default' => 'https://www.campo.fau.de/',
                'sanitize_callback' => 'sanitize_url',
            ],
            [
                'name' => 'campo_linktxt',
                'label' => __('Text for the link to Campo', 'rrze-campo'),
                'desc' => __('', 'rrze-campo'),
                'placeholder' => __('', 'rrze-campo'),
                'type' => 'text',
                'default' => __('Campo - Information System of the FAU', 'rrze-campo'),
                'sanitize_callback' => 'sanitize_text_field',
            ],
            [
                'name' => 'ApiKey',
                'label' => __('Campo ApiKey', 'rrze-campo'),
                'desc' => __('', 'rrze-campo'),
                'placeholder' => '',
                'type' => 'text',
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            // [
            //     'name' => 'semesterMin',
            //     'label' => __('Find lectures starting with summer semester', 'rrze-campo'),
            //     'desc' => __('', 'rrze-campo'),
            //     'placeholder' => '',
            //     'min' => 0,
            //     'max' => 99999999999,
            //     'step' => '1',
            //     'type' => 'number',
            //     'default' => date("Y") - 1,
            //     'sanitize_callback' => 'floatval',
            // ],

            // Semester Start/Ende-Datum muss von der API geliefert werden
            // [
            //     'name' => 'wsStart',
            //     'label' => __('Beginn der Vorlesungszeit in diesem Wintersemester', 'rrze-campo'),
            //     'desc' => __('', 'rrze-campo'),
            //     'placeholder' => '',
            //     'type' => 'date',
            //     'default' => date("Y") - 1 . '-11-02',
            //     'sanitize_callback' => 'date',
            // ],
            // [
            //     'name' => 'wsEnd',
            //     'label' => __('Ende der Vorlesungszeit in diesem Wintersemester', 'rrze-campo'),
            //     'desc' => __('', 'rrze-campo'),
            //     'placeholder' => '',
            //     'type' => 'date',
            //     'default' => date("Y") . '-02-12',
            //     'sanitize_callback' => 'date',
            // ],
            // [
            //     'name' => 'ssStart',
            //     'label' => __('Beginn der Vorlesungszeit in diesem Sommersemester', 'rrze-campo'),
            //     'desc' => __('', 'rrze-campo'),
            //     'placeholder' => '',
            //     'type' => 'date',
            //     'default' => date("Y") . '-04-12',
            //     'sanitize_callback' => 'date',
            // ],
            // [
            //     'name' => 'ssEnd',
            //     'label' => __('Ende der Vorlesungszeit in diesem Sommersemester', 'rrze-campo'),
            //     'desc' => __('', 'rrze-campo'),
            //     'placeholder' => '',
            //     'type' => 'date',
            //     'default' => date("Y") . '-07-16',
            //     'sanitize_callback' => 'date',
            // ],
            [
                'name' => 'hstart',
                'label' => __('Headline\'s size', 'rrze-campo'),
                'desc' => __('', 'rrze-campo'),
                'min' => 2,
                'max' => 10,
                'step' => '1',
                'type' => 'number',
                'default' => '2',
                'sanitize_callback' => 'floatval',
            ],
        ],
    ];
}

/**
 * Gibt die Einstellungen der Parameter für Shortcode für den klassischen Editor und für Gutenberg zurück.
 * @return array [description]
 */

function getShortcodeSettings()
{
    return [
        'lectures' => [
            'block' => [
                'blocktype' => 'rrze-campo/campolectures',
                'blockname' => 'campolectures',
                'title' => 'RRZE-Campo',
                'category' => 'widgets',
                'icon' => 'bank',
                'tinymce_icon' => 'paste',
            ],
            'id' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Lecture ID', 'rrze-campo'),
                'type' => 'string',
            ],
            'name' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Firstname, Lastname', 'rrze-campo'),
                'type' => 'string',
            ],
            'campoid' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Person ID', 'rrze-campo'),
                'type' => 'string',
            ],
            'dozentid' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Lecturer ID', 'rrze-campo'),
                'type' => 'string',
            ],
            'type' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Typ. z.B. vorl (=Vorlesung)', 'rrze-campo'),
                'type' => 'string',
            ],
            'order' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Sort by type f.e. "vorl,ueb"', 'rrze-campo'),
                'type' => 'string',
            ],
            'sem' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Semester f.e. 2020w', 'rrze-campo'),
                'type' => 'string',
            ],
            'lang' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Language', 'rrze-campo'),
                'type' => 'string',
            ],
            'number' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Campo OrgID', 'rrze-campo'),
                'type' => 'string',
            ],

            // Fruehstudium und Gaststudium in show packen
            'show' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Show', 'rrze-campo'),
                'type' => 'string',
            ],
            'hide' => [
                'default' => '',
                'field_type' => 'text',
                'label' => __('Hide', 'rrze-campo'),
                'type' => 'string',
            ],
            'ics' => [
                'field_type' => 'toggle',
                'label' => __('Show Calendar file', 'rrze-campo'),
                'type' => 'boolean',
                'default' => true,
                'checked' => true,
            ],
            'hstart' => [
                'default' => 2,
                'field_type' => 'text',
                'label' => __('Headline\'s size', 'fau-person'),
                'type' => 'number',
            ],
            // 'fruehstud' => [
            //     'field_type' => 'toggle',
            //     'label' => __('Show early study only', 'rrze-campo'),
            //     'type' => 'boolean',
            //     'default' => null,
            //     'checked' => false,
            // ],
            // 'gast' => [
            //     'field_type' => 'toggle',
            //     'label' => __('Nur für Gaststudium geeignet anzeigen', 'rrze-campo'),
            //     'type' => 'boolean',
            //     'default' => null,
            //     'checked' => false,
            // ],
        ],
    ];
}

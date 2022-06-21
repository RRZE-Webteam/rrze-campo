<?php

namespace RRZE\Campo;

defined('ABSPATH') || exit;

class Functions
{

    protected $pluginFile;

    public function __construct($pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    public function onLoaded()
    {
        // add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('wp_ajax_GetCampoData', [$this, 'ajaxGetCampoData']);
        add_action('wp_ajax_nopriv_GetCampoData', [$this, 'ajaxGetCampoData']);
        add_action('wp_ajax_GetCampoDataForBlockelements', [$this, 'ajaxGetCampoDataForBlockelements']);
        add_action('wp_ajax_nopriv_GetCampoDataForBlockelements', [$this, 'ajaxGetCampoDataForBlockelements']);
    }

    // public function adminEnqueueScripts()
    // {
        // wp_enqueue_script(
        //     'rrze-unvis-ajax',
        //     plugins_url('js/rrze-campo.js', plugin_basename($this->pluginFile)),
        //     ['jquery'],
        //     null
        // );

        // wp_localize_script('rrze-unvis-ajax', 'campo_ajax', [
        //     'ajax_url' => admin_url('admin-ajax.php'),
        //     'nonce' => wp_create_nonce('campo-ajax-nonce'),
        // ]);

    // }

    public function getTableHTML($aIn)
    {
        if (!is_array($aIn)) {
            return $aIn;
        }
        $ret = '<table class="wp-list-table widefat striped"><thead><tr><td><b><i>Univ</i>IS</b> ID</td><td><strong>Name</strong></td></tr></thead>';
        foreach ($aIn as $ID => $val) {
            $ret .= "<tr><td>$ID</td><td style='word-wrap: break-word;'>$val</td></tr>";
        }
        $ret .= '</table>';
        return $ret;
    }

    public function ajaxGetCampoData()
    {
        check_ajax_referer('campo-ajax-nonce', 'nonce');
        $inputs = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $response = $this->getTableHTML($this->getCampoData(null, $inputs['dataType'], $inputs['keyword']));
        wp_send_json($response);
    }

    public function getSelectHTML($aIn)
    {
        if (!is_array($aIn)) {
            return "<option value=''>$aIn</option>";
        }
        $ret = '<option value="">' . __('-- All --', 'rrze-campo') . '</option>';
        natsort($aIn);
        foreach ($aIn as $ID => $val) {
            $ret .= "<option value='$ID'>$val</option>";
        }
        return $ret;
    }

    public function getCampoData($campoOrgID = null, $dataType = '', $keyword = null)
    {
        $data = false;
        $ret = __('No matching entries found.', 'rrze-campo');

        $options = get_option('rrze-campo');
        $data = 0;
        $CampoURL = (!empty($options['basic_campo_url']) ? $options['basic_campo_url'] : 'https://campo.uni-erlangen.de');
        $campoOrgID = (!empty($campoOrgID) ? $campoOrgID : (!empty($options['basic_CampoOrgNr']) ? $options['basic_CampoOrgNr'] : 0));

        if ($CampoURL) {
            $campo = new CampoAPI($CampoURL, $campoOrgID, null);
            $data = $campo->getData($dataType, $keyword);
        } elseif (!$CampoURL) {
            $ret = __('Link to Campo is missing.', 'rrze-campo');
        }

        if ($data) {
            $ret = [];
            switch ($dataType) {
                // case 'departmentByName':
                //     foreach ($data as $entry) {
                //         if (isset($entry['orgnr'])) {
                //             $ret[$entry['orgnr']] = $entry['name'];
                //         }
                //     }
                //     break;
                // case 'personByName':
                //     foreach ($data as $entry) {
                //         if (isset($entry['person_id'])) {
                //             $ret[$entry['person_id']] = $entry['lastname'] . ', ' . $entry['firstname'];
                //         }
                //     }
                //     break;
                // case 'personAll':
                //     foreach ($data as $position => $entries) {
                //         foreach ($entries as $entry) {
                //             if (isset($entry['person_id'])) {
                //                 $ret[$entry['person_id']] = $entry['lastname'] . ', ' . $entry['firstname'];
                //             }
                //         }
                //     }
                //     break;
                case 'lectureByName':
                    foreach ($data as $entry) {
                        if (isset($entry['lecture_id'])) {
                            $ret[$entry['lecture_id']] = $entry['name'];
                        }
                    }
                    break;
                case 'lectureByDepartment':
                    foreach ($data as $type => $entries) {
                        foreach ($entries as $entry) {
                            if (isset($entry['lecture_id'])) {
                                $ret[$entry['lecture_id']] = $entry['name'];
                            }
                        }
                    }
                    break;
                default:
                    $ret = 'unknown dataType';
                    break;
            }
        }

        return $ret;
    }

    public function ajaxGetCampoDataForBlockelements()
    {
        check_ajax_referer('campo-ajax-nonce', 'nonce');
        $inputs = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $response = $this->getSelectHTML($this->getCampoData($inputs['campoOrgID'], $inputs['dataType']));
        wp_send_json($response);
    }

}

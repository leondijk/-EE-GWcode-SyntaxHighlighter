<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/*
============================================================
 This ExpressionEngine add-on was created by Leon Dijk
 - http://gwcode.com/
============================================================
 This add-on is licensed under a
 Creative Commons Attribution-NoDerivs 3.0 Unported License.
 - http://creativecommons.org/licenses/by-nd/3.0/
============================================================
*/

class Gwcode_syntaxhighlighter_ext {

	var $name           = 'GWcode SyntaxHighlighter';
	var $version        = '1.1.0';
	var $description    = 'Adds a SyntaxHighlighter button to your Wygwam toolbar.';
	var $settings_exist = 'n';
	var $docs_url       = 'http://gwcode.com/add-ons/gwcode-syntaxhighlighter';

	private $_hooks = array(
		'wygwam_config',
		'wygwam_tb_groups'
	);

	private static $_included_resources = FALSE;

	// --------------------------------------------------------------------

	/**
	 * Activate Extension
	 */
	function activate_extension() {

		foreach ($this->_hooks as $hook) {
			ee()->db->insert('extensions', array(
				'class'    => get_class($this),
				'method'   => $hook,
				'hook'     => $hook,
				'settings' => '',
				'priority' => 10,
				'version'  => $this->version,
				'enabled'  => 'y'
			));
		}

	}

	/**
	 * Update Extension
	 */
	function update_extension($current = '') {

		if($current == '' OR $current == $this->version) {
			return FALSE;
		}

		ee()->db->where('class', __CLASS__);
		ee()->db->update('extensions', array('version' => $this->version));

	}

	/**
	 * Disable Extension
	 */
	function disable_extension() {
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

	// --------------------------------------------------------------------

	/**
	 * wygwam_config hook
	 */
	function wygwam_config($config, $settings) {

		if(($last_call = ee()->extensions->last_call) !== FALSE) {
			$config = $last_call;
		}

		// Check if our toolbar button has been added
		$include_btn = FALSE;

		foreach($config['toolbar'] as $tbgroup) {
			if(in_array('syntaxhighlight', $tbgroup)) {
				$include_btn = TRUE;
				break;
			}
		}

		if($include_btn) {
			// Add our plugin to CKEditor
			if(!empty($config['extraPlugins'])) {
				$config['extraPlugins'] .= ',';
			}

			$config['extraPlugins'] .= 'syntaxhighlight';

			$this->_include_resources();
		}

		return $config;

	}

	public function wygwam_tb_groups($tb_groups) {

		if(($last_call = ee()->extensions->last_call) !== FALSE) {
			$tb_groups = $last_call;
        }

		$tb_groups[] = array('syntaxhighlight');

		// Is this the toolbar editor?
		if(ee()->input->get('M') == 'show_module_cp') {
			// Give our toolbar button an icon
			$icon_url = URL_THIRD_THEMES.'gwcode_syntaxhighlighter/syntaxhighlight/icons/syntaxhighlight.png';
			ee()->cp->add_to_head('<style type="text/css">.cke_button__syntaxhighlight_icon { background-image: url('.$icon_url.'); }</style>');
		}

		return $tb_groups;

    }

	private function _include_resources() {

		// Is this the first time we've been called?
		if(!self::$_included_resources) {
			// Tell CKEditor where to find our plugin
			$plugin_url = URL_THIRD_THEMES.'gwcode_syntaxhighlighter/syntaxhighlight/';
			ee()->cp->add_to_foot('<script type="text/javascript">CKEDITOR.plugins.addExternal("syntaxhighlight", "'.$plugin_url.'");</script>');

			// Don't do that again
			self::$_included_resources = TRUE;
		}

	}

}
?>
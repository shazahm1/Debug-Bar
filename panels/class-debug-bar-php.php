<?php

class Debug_Bar_PHP extends Debug_Bar_Panel {
	var $warnings = array();
	var $notices = array();
	var $strict = array();
	var $deprecated = array();
	var $unknown = array();
	var $real_error_handler = array();

	function init() {
		if ( ! WP_DEBUG )
			return false;

		$this->title( __('Notices / Warnings', 'debug-bar') );

		$this->real_error_handler = set_error_handler( array( &$this, 'error_handler' ) );
	}

	function is_visible() {
		return count( $this->notices ) || count( $this->warnings ) || count( $this->strict ) || count( $this->deprecated ) || count( $this->unknown );
	}

	function debug_bar_classes( $classes ) {
		if ( count( $this->warnings ) )
			$classes[] = 'debug-bar-php-warning-summary';
		elseif ( count( $this->notices ) )
			$classes[] = 'debug-bar-php-notice-summary';
		elseif ( count( $this->strict ) )
			$classes[] = 'debug-bar-php-notice-summary';
		elseif ( count( $this->deprecated ) )
			$classes[] = 'debug-bar-php-notice-summary';
		elseif ( count( $this->unknown ) )
			$classes[] = 'debug-bar-php-notice-summary';
		return $classes;
	}

	function error_handler( $type, $message, $file, $line ) {
		$_key = md5( $file . ':' . $line . ':' . $message );

		$count = array_key_exists( $_key, $this->strict ) ? $this->strict[$_key]['count'] : 0;
		$count++;

		switch ( $type ) {
			case E_WARNING :
			case E_USER_WARNING :
				$this->warnings[$_key] = array( $file.':'.$line, $message, 'count' => $count );
				break;
			case E_NOTICE :
			case E_USER_NOTICE :
				$this->notices[$_key] = array( $file.':'.$line, $message, 'count' => $count );
				break;
			case 2048:
			case E_STRICT :
				$this->strict[$_key] = array( $file.':'.$line, $message, 'count' => $count );
				break;
			case 8192:
			case E_DEPRECATED :
			case E_USER_DEPRECATED :
				$this->deprecated[$_key] = array( $file.':'.$line, $message, 'count' => $count );
				break;
			case 0 :
				$this->unknown[$_key] = array( $file.':'.$line, $message, 'count' => $count );
				break;
		}

		if ( null != $this->real_error_handler )
			return call_user_func( $this->real_error_handler, $type, $message, $file, $line );
		else
			return false;
	}

	function render() {
		echo "<div id='debug-bar-php'>";
		echo '<h2><span>Total Warnings:</span>' . number_format( count( $this->warnings ) ) . "</h2>\n";
		echo '<h2><span>Total Notices:</span>' . number_format( count( $this->notices ) ) . "</h2>\n";
		echo '<h2><span>Total Strict:</span>' . number_format( count( $this->strict ) ) . "</h2>\n";
		echo '<h2><span>Total Deprecated:</span>' . number_format( count( $this->deprecated ) ) . "</h2>\n";
		echo '<h2><span>Total Unknown:</span>' . number_format( count( $this->unknown ) ) . "</h2>\n";
		if ( count( $this->warnings ) ) {
			echo '<ol class="debug-bar-php-list">';
			foreach ( $this->warnings as $location_message) {
				list( $location, $message, $count ) = array_values( $location_message );
				echo "<li class='debug-bar-php-warning'>WARNING (" . $count . ") : ".str_replace(ABSPATH, '', $location) . ' - ' . strip_tags($message). "</li>";
			}
			echo '</ol>';
		}
		if ( count( $this->notices ) ) {
			echo '<ol class="debug-bar-php-list">';
			foreach ( $this->notices as $location_message) {
				list( $location, $message, $count ) = array_values( $location_message );
				echo "<li  class='debug-bar-php-notice'>NOTICE (" . $count . ") : ".str_replace(ABSPATH, '', $location) . ' - ' . strip_tags($message). "</li>";
			}
			echo '</ol>';
		}
		if ( count( $this->strict ) ) {
			echo '<ol class="debug-bar-php-list">';
			foreach ( $this->strict as $location_message) {
				list( $location, $message, $count ) = array_values( $location_message );
				echo "<li  class='debug-bar-php-notice'>STRICT (" . $count . ") : ".str_replace(ABSPATH, '', $location) . ' - ' . strip_tags($message). "</li>";
			}
			echo '</ol>';
		}
		if ( count( $this->deprecated ) ) {
			echo '<ol class="debug-bar-php-list">';
			foreach ( $this->deprecated as $location_message) {
				list( $location, $message, $count ) = array_values( $location_message );
				echo "<li  class='debug-bar-php-notice'>DEPRECATED (" . $count . ") : ".str_replace(ABSPATH, '', $location) . ' - ' . strip_tags($message). "</li>";
			}
			echo '</ol>';
		}
		if ( count( $this->unknown ) ) {
			echo '<ol class="debug-bar-php-list">';
			foreach ( $this->unknown as $location_message) {
				list( $location, $message, $count ) = array_values( $location_message );
				echo "<li  class='debug-bar-php-notice'>UNKNOWN (" . $count . ") : ".str_replace(ABSPATH, '', $location) . ' - ' . strip_tags($message). "</li>";
			}
			echo '</ol>';
		}
		echo "</div>";

	}
}

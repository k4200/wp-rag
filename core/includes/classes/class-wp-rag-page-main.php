<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wp_Rag_Page_Main
 *
 * This class handles rendering of the main page.
 *
 * @package     WPRAG
 * @subpackage  Classes/Wp_Rag_Page_Main
 * @author      Kashima, Kazuo
 * @since       0.0.1
 */
class Wp_Rag_Page_Main {
	/**
	 * Renders the main page
	 *
	 * @return void
	 */
	public function render_main_page() {
		if ( ! WPRAG()->helpers->is_verified() ) {
			$this->render_main_page_not_verified();
			return;
		}
		?>
		<div class="wrap">
			<h2>WP RAG</h2>
			<h3>Operations</h3>
			<form method="post" action="">
				<?php wp_nonce_field( 'wp_rag_operation_submit', 'wp_rag_nonce' ); ?>
				<input type="submit" name="wp_rag_import_submit" class="button button-primary" value="Import">
				<input type="submit" name="wp_rag_embed_submit" class="button button-primary" value="Embed">
			</form>
			<h3>Test Query</h3>
			<form method="post" action="">
				<?php wp_nonce_field( 'wp_rag_query_submit', 'wp_rag_nonce' ); ?>
				<input type="text" name="wp_rag_question" />
				<input type="submit" name="wp_rag_query_submit" class="button button-primary" value="Query">
			</form>
		</div>
		<?php
	}

	private function render_main_page_not_verified() {
		?>
		<div class="wrap">
			<h2>WP RAG</h2>
			<div>
				Please register the site on the settings page.
			</div>
		</div>
		<?php
	}


	/**
	 * @return void
	 */
	function handle_import_form_submission() {
		check_admin_referer( 'wp_rag_operation_submit', 'wp_rag_nonce' );
		$data     = array(
			'task_type' => 'ImportWordpressPosts',
			'params'    => array( 'post_type' => 'post' ),
		);
		$response = WPRAG()->helpers->call_api_for_site( '/tasks', 'POST', $data );

		$type = 202 === $response['httpCode'] ? 'success' : 'error';

		add_settings_error(
			'wp_rag_messages',
			'wp_rag_message',
			'Response from the API: ' . wp_json_encode( $response ),
			$type
		);
	}

	/**
	 * @return void
	 */
	function handle_embed_form_submission() {
		check_admin_referer( 'wp_rag_operation_submit', 'wp_rag_nonce' );
		$data     = array(
			'task_type' => 'Embed',
			'params'    => array(),
		);
		$response = WPRAG()->helpers->call_api_for_site( '/tasks', 'POST', $data );

		$type = 202 === $response['httpCode'] ? 'success' : 'error';

		add_settings_error(
			'wp_rag_messages',
			'wp_rag_message',
			'Response from the API: ' . wp_json_encode( $response ),
			$type
		);
	}

	/**
	 * Handles the query  form submission, validates the nonce, processes the posted data, and calls the API.
	 *
	 * @return void
	 */
	function handle_query_form_submission() {
		check_admin_referer( 'wp_rag_query_submit', 'wp_rag_nonce' );
		if ( empty( $_POST['wp_rag_question'] ) ) {
			return;
		}
		$data     = array( 'question' => sanitize_text_field( wp_unslash( $_POST['wp_rag_question'] ) ) );
		$response = WPRAG()->helpers->call_api_for_site( '/posts/query', 'POST', $data );

		$type = 200 === $response['httpCode'] ? 'success' : 'error';

		add_settings_error(
			'wp_rag_messages',
			'wp_rag_message',
			'Response from the API: ' . wp_json_encode( $response ),
			$type
		);
	}
}
<?php
/**
 * Save Exception.
 *
 * This class is responsible for handling all
 * Save exceptions.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Exceptions;

class SaveException extends \Exception {
	/**
	 * Context.
	 *
	 * The error context (e.g., what part of the process failed).
	 *
	 * @since 1.0.2
	 *
	 * @var string
	 */
	protected $context;

	/**
	 * Setup.
	 *
	 * @since 1.0.2
	 *
	 * @param string      $message   The exception message.
	 * @param int         $code      The exception code.
	 * @param string|null $context   Additional context about where the exception occurred.
	 */
	public function __construct( $message, $code = 0, $context = null ) {
		$this->context = $context;

		// Log the error for debugging purposes.
		error_log(
			sprintf(
				'%d Fatal Error, Save Exception: %s | Context: %s',
				$code,
				$message,
				$context,
			)
		);

		// Pass to base Exception class.
		parent::__construct( $message, $code );
	}

	/**
	 * Get the error Context.
	 *
	 * @since 1.0.2
	 *
	 * @return string|null
	 */
	public function getContext() {
		return $this->context;
	}
}

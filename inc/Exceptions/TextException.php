<?php
/**
 * Text Exception.
 *
 * This class is responsible for handling all
 * Text exceptions.
 *
 * @package WatermarkMyImages
 */

namespace WatermarkMyImages\Exceptions;

class TextException extends \Exception {
	/**
	 * Context.
	 *
	 * The error context (e.g., what part of the process failed).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $context;

	/**
	 * Original Exception.
	 *
	 * The original exception that caused this error (optional).
	 *
	 * @since 1.0.0
	 *
	 * @var \Exception
	 */
	protected $exception;

	/**
	 * Setup.
	 *
	 * @since 1.0.0
	 *
	 * @param string          $message   The exception message.
	 * @param int             $code      The exception code.
	 * @param string|null     $context   Additional context about where the exception occurred.
	 * @param \Exception|null $exception The original exception (optional).
	 */
	public function __construct( $message, $code = 0, $context = null, \Exception $exception = null ) {
		$this->context   = $context;
		$this->exception = $exception;

		// Log the error for debugging purposes.
		error_log(
			sprintf(
				'%d Fatal Error, Text Exception: %s | Context: %s',
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
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * Get the Original Exception.
	 *
	 * @since 1.0.0
	 *
	 * @return \Exception|null
	 */
	public function getOriginalException() {
		return $this->exception;
	}
}

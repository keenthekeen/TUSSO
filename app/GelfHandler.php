<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use Gelf\PublisherInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\Formatter\GelfMessageFormatter;

/**
 * Handler to send messages to a Graylog2 (http://www.graylog2.org) server
 *
 * @author Matt Lehner <mlehner@gmail.com>
 * @author Benjamin Zikarsky <benjamin@zikarsky.de>
 */
class GelfHandler extends AbstractProcessingHandler
{
	/**
	 * @var Publisher the publisher object that sends the message to the server
	 */
	protected $publisher;

	/**
	 * @param PublisherInterface $publisher a publisher object
	 * @param int                $level     The minimum logging level at which this handler will be triggered
	 * @param bool               $bubble    Whether the messages that are handled can bubble up the stack or not
	 */
	public function __construct(PublisherInterface $publisher, $level = Logger::DEBUG, $bubble = true)
	{
		parent::__construct($level, $bubble);

		$this->publisher = $publisher;
	}

	/**
	 * {@inheritdoc}
	 */
	public function close()
	{
		$this->publisher = null;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function write(array $record)
	{
		$this->publisher->publish($record['formatted']);
	}

	/**
	 * {@inheritDoc}
	 */
	// PHP7: protected function getDefaultFormatter(): FormatterInterface
	protected function getDefaultFormatter()
	{
		return new GelfMessageFormatter();
	}
}

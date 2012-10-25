<?php

/**
 * sfRavenLogger allows you send symfony logs to Sentry.
 */
class sfRavenLogger extends sfLogger
{
  protected $level = self::ERROR;

  /**
   * Logs a message.
   *
   * @param string $message Message
   * @param string $priority Message priority
   *
   * @see sfLogger::doLog
   */
  protected function doLog($message, $priority)
  {
    if (!($client = $this->getClient()))
    {
      return;
    }

    $client->captureMessage($message, array(), $this->getLevelFromLoggerPriority($priority));
  }

  /**
   * Map symfony logger error priority to Raven level.
   *
   * @param integer $priority Logger priority
   * @return string Raven level
   */
  protected function getLevelFromLoggerPriority($priority)
  {
    switch ($priority)
    {
      case self::EMERG:
      case self::ALERT:
      case self::CRIT:
        return 'fatal';
      case self::ERR:
        return 'error';
      case self::WARNING:
        return 'warning';
      case self::NOTICE:
      case self::INFO:
        return 'info';
      case self::DEBUG:
        return 'debug';
    }

    throw new Exception(sprintf('Unknown priority "%s" in Raven.', $priority));
  }

  /**
   * Get cleint
   * 
   *  @return sfRavenClient
   */
  protected function getClient()
  {
    if (null !== $this->client)
    {
      return $this->client;
    }

    if (!($dsn = sfConfig::get('raven_dsn')))
    {
      return null;
    }

    return $this->client = new sfRavenClient($dsn);
  }
}
